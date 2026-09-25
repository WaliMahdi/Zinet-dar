<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Resources\UserResource;
use App\Mail\VerificationCodeMail;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    // ──────────────────────────────────────────────────────────────────
    //  Constants
    // ──────────────────────────────────────────────────────────────────

    /** Verification code expiry in minutes. */
    private const CODE_EXPIRY_MINUTES = 15;

    // ──────────────────────────────────────────────────────────────────
    //  Public endpoints
    // ──────────────────────────────────────────────────────────────────

    /**
     * POST /api/auth/register
     *
     * Creates a new user account, generates a verification code, and sends it
     * by email. The user is NOT authenticated until they verify their email.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $user = User::create([
                'email' => $request->email,
                'password' => $request->password, // hashed automatically via model cast
            ]);

            $this->issueVerificationCode($user);

            return $user;
        });

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. A 6-digit verification code has been sent to your email address. Please verify your email to activate your account.',
        ], 201);
    }

    /**
     * POST /api/auth/verify-email
     *
     * Validates the 6-digit code submitted by the user. On success, marks the
     * email as verified and deletes the used code so it cannot be reused.
     */
    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse('No account found with this email address.', 404);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse('This email address has already been verified.', 409);
        }

        $record = $user->emailVerificationCode;

        if (!$record) {
            return $this->errorResponse('No verification code found. Please request a new one.', 404);
        }

        // Verify the code — compare against the stored hash
        if (!hash_equals($record->code, hash('sha256', $request->verification_code))) {
            return $this->errorResponse('The verification code is incorrect.', 422);
        }

        if ($record->isExpired()) {
            return $this->errorResponse('The verification code has expired. Please request a new one.', 422);
        }

        DB::transaction(function () use ($user, $record): void {
            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();

            // Invalidate the code so it cannot be reused
            $record->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully. You can now log in.',
        ]);
    }

    /**
     * POST /api/auth/resend-verification
     *
     * Generates a new verification code and sends it to the user's email.
     * Rate-limited to 3 requests per minute per email address.
     */
    public function resendVerification(ResendVerificationRequest $request): JsonResponse
    {
        // Rate limiting: 3 resend attempts per email per minute
        $rateLimitKey = 'resend-verification:' . strtolower($request->email);

        if (RateLimiter::tooManyAttempts($rateLimitKey, maxAttempts: 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return $this->errorResponse(
                "Too many requests. Please wait {$seconds} seconds before trying again.",
                429
            );
        }

        RateLimiter::hit($rateLimitKey, decaySeconds: 60);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Do not reveal whether the email exists (security best practice)
            return response()->json([
                'success' => true,
                'message' => 'If an account exists with this email address, a new verification code has been sent.',
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse('This email address has already been verified.', 409);
        }

        // Delete any existing code and issue a fresh one
        DB::transaction(function () use ($user): void {
            $user->emailVerificationCode()->delete();
            $this->issueVerificationCode($user);
        });

        return response()->json([
            'success' => true,
            'message' => 'A new verification code has been sent to your email address.',
        ]);
    }

    /**
     * POST /api/auth/login
     *
     * Validates credentials, checks email verification, and issues a Sanctum token.
     * Rate-limited to 5 attempts per minute per IP + email combination.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Rate limiting: 5 login attempts per minute per IP + email
        $rateLimitKey = 'login:' . $request->ip() . '|' . strtolower($request->email);

        if (RateLimiter::tooManyAttempts($rateLimitKey, maxAttempts: 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return $this->errorResponse(
                "Too many login attempts. Please wait {$seconds} seconds.",
                429
            );
        }

        $user = User::where('email', $request->email)->first();

        // Verify credentials — use constant-time comparison via Hash::check
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($rateLimitKey, decaySeconds: 60);

            return $this->errorResponse('The provided credentials are incorrect.', 401);
        }

        // Block login until email is verified
        if (!$user->hasVerifiedEmail()) {
            return $this->errorResponse(
                'Your email address has not been verified. Please check your inbox for the verification code.',
                403
            );
        }

        // Successful login — clear rate limiter
        RateLimiter::clear($rateLimitKey);

        // Revoke any existing tokens to enforce single-session (optional; remove if multi-device needed)
        // $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    //  Protected endpoints (auth:sanctum required)
    // ──────────────────────────────────────────────────────────────────

    /**
     * POST /api/auth/logout
     *
     * Revokes the current Sanctum access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * GET /api/auth/profile
     *
     * Returns the authenticated user's profile. Password is never included.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully.',
            'data' => new UserResource($request->user()),
        ]);
    }

    /**
     * PUT|PATCH /api/auth/profile
     *
     * Updates the authenticated user's profile fields and/or password.
     * Password change requires: current_password, new_password, new_password_confirmation.
     * Only the authenticated user's own record is ever modified.
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        // ── Handle password change ────────────────────────────────────────
        if ($request->filled('current_password')) {
            // Verify the current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('The current password is incorrect.', 422);
            }

            // Update password — the hashed cast on the model handles hashing automatically
            $user->password = $request->new_password;
        }

        // ── Update profile fields (only provided ones) ────────────────────
        $user->fill($request->only(['username', 'first_name', 'last_name']));

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => new UserResource($user->fresh()),
            'debug_input' => $request->all(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    //  Private helpers
    // ──────────────────────────────────────────────────────────────────

    /**
     * Generate a random 6-digit code, store its SHA-256 hash in the DB,
     * and dispatch the verification email.
     *
     * The plain-text code is only kept in memory long enough to send the email;
     * it is never persisted to the database.
     */
    private function issueVerificationCode(User $user): void
    {
        // Generate cryptographically random 6-digit code
        $plainCode = (string) random_int(100_000, 999_999);

        // Delete any previous code for this user before creating a new one.
        // The unique constraint on user_id means there can only ever be one.
        $user->emailVerificationCode()->delete();

        EmailVerificationCode::create([
            'user_id' => $user->id,
            'code' => hash('sha256', $plainCode),
            'expires_at' => now()->addMinutes(self::CODE_EXPIRY_MINUTES),
        ]);

        // Send the plain code — it is not stored anywhere after this point
        Mail::to($user->email)->send(
            new VerificationCodeMail($plainCode, self::CODE_EXPIRY_MINUTES)
        );
    }

    /**
     * Build a consistent JSON error response.
     */
    private function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
