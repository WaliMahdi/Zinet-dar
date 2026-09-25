<?php

namespace Tests\Feature\Auth;

use App\Mail\VerificationCodeMail;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Feature tests for all authentication endpoints.
 *
 * The test suite uses an in-memory SQLite database (configured in phpunit.xml)
 * and RefreshDatabase to keep each test isolated.
 * Mail is faked so no real emails are sent during tests.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────────

    /**
     * Create a user with an active verification code.
     *
     * @param  bool  $verified  Whether to mark the email as already verified.
     */
    private function createUserWithCode(bool $verified = false): array
    {
        $user = User::factory()->create([
            'email_verified_at' => $verified ? now() : null,
            'password'          => bcrypt('Password123!'),
        ]);

        $plainCode = '123456';

        EmailVerificationCode::create([
            'user_id'    => $user->id,
            'code'       => hash('sha256', $plainCode),
            'expires_at' => now()->addMinutes(15),
        ]);

        return [$user, $plainCode];
    }

    /**
     * Create a fully verified user ready to log in.
     */
    private function createVerifiedUser(string $password = 'Password123!'): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'password'          => bcrypt($password),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    //  Registration tests
    // ──────────────────────────────────────────────────────────────────

    public function test_successful_registration(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/auth/register', [
            'email'                 => 'newuser@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertStringContainsString(
            'verification code',
            $response->json('message')
        );

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
        $this->assertNull(User::where('email', 'newuser@example.com')->first()->email_verified_at);

        Mail::assertSent(VerificationCodeMail::class, fn ($mail) => $mail->hasTo('newuser@example.com'));
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        Mail::fake();
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'email'                 => 'duplicate@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_registration_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'email'                 => 'not-an-email',
            'password'              => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure(['errors' => ['email', 'password']]);
    }

    // ──────────────────────────────────────────────────────────────────
    //  Email verification tests
    // ──────────────────────────────────────────────────────────────────

    public function test_email_verification_with_valid_code(): void
    {
        [$user, $plainCode] = $this->createUserWithCode();

        $response = $this->postJson('/api/auth/verify-email', [
            'email'             => $user->email,
            'verification_code' => $plainCode,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertDatabaseMissing('email_verification_codes', ['user_id' => $user->id]);
    }

    public function test_email_verification_with_invalid_code(): void
    {
        [$user] = $this->createUserWithCode();

        $response = $this->postJson('/api/auth/verify-email', [
            'email'             => $user->email,
            'verification_code' => '000000', // Wrong code
        ]);

        $response->assertStatus(422)
                 ->assertJson(['success' => false]);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_email_verification_with_expired_code(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $plainCode = '654321';

        EmailVerificationCode::create([
            'user_id'    => $user->id,
            'code'       => hash('sha256', $plainCode),
            'expires_at' => now()->subMinutes(5), // Already expired
        ]);

        $response = $this->postJson('/api/auth/verify-email', [
            'email'             => $user->email,
            'verification_code' => $plainCode,
        ]);

        $response->assertStatus(422)
                 ->assertJson(['success' => false]);

        $this->assertStringContainsString(
            'expired',
            $response->json('message')
        );
    }

    // ──────────────────────────────────────────────────────────────────
    //  Resend verification tests
    // ──────────────────────────────────────────────────────────────────

    public function test_resend_verification_code(): void
    {
        Mail::fake();
        [$user] = $this->createUserWithCode();

        $response = $this->postJson('/api/auth/resend-verification', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        Mail::assertSent(VerificationCodeMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    public function test_resend_verification_fails_if_already_verified(): void
    {
        [$user] = $this->createUserWithCode(verified: true);

        $response = $this->postJson('/api/auth/resend-verification', [
            'email' => $user->email,
        ]);

        $response->assertStatus(409)
                 ->assertJson(['success' => false]);
    }

    // ──────────────────────────────────────────────────────────────────
    //  Login tests
    // ──────────────────────────────────────────────────────────────────

    public function test_login_with_valid_credentials(): void
    {
        $user = $this->createVerifiedUser('Secret99!');

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'Secret99!',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure(['token', 'user' => ['id', 'email']]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = $this->createVerifiedUser();

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['success' => false]);
    }

    public function test_login_fails_before_email_verification(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'password'          => bcrypt('Password123!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'Password123!',
        ]);

        $response->assertStatus(403)
                 ->assertJson(['success' => false]);
    }

    // ──────────────────────────────────────────────────────────────────
    //  Logout tests
    // ──────────────────────────────────────────────────────────────────

    public function test_successful_logout(): void
    {
        $user  = $this->createVerifiedUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
    }

    // ──────────────────────────────────────────────────────────────────
    //  Profile tests
    // ──────────────────────────────────────────────────────────────────

    public function test_profile_requires_authentication(): void
    {
        $response = $this->getJson('/api/auth/profile');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_profile(): void
    {
        $user  = $this->createVerifiedUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/auth/profile');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure([
                     'data' => ['id', 'email', 'username', 'first_name', 'last_name', 'email_verified_at'],
                 ]);

        // Password must never appear in the response
        $this->assertArrayNotHasKey('password', $response->json('data'));
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user  = $this->createVerifiedUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->putJson('/api/auth/profile', [
            'username'   => 'johndoe',
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonPath('data.username', 'johndoe')
                 ->assertJsonPath('data.first_name', 'John')
                 ->assertJsonPath('data.last_name', 'Doe');

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'username'   => 'johndoe',
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ]);
    }

    public function test_user_cannot_access_another_users_profile(): void
    {
        // User A logs in
        $userA  = $this->createVerifiedUser();
        $tokenA = $userA->createToken('token-a')->plainTextToken;

        // User B exists
        $userB = $this->createVerifiedUser();

        // User A can only access /api/auth/profile which always returns THEIR own profile.
        // There is no endpoint that accepts another user's ID — test confirms only own data is returned.
        $response = $this->withToken($tokenA)->getJson('/api/auth/profile');

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $userA->id); // Returns user A, not user B

        $this->assertNotEquals($userB->id, $response->json('data.id'));
    }

    // ──────────────────────────────────────────────────────────────────
    //  Password change tests
    // ──────────────────────────────────────────────────────────────────

    public function test_user_can_change_password(): void
    {
        $user  = $this->createVerifiedUser('OldPass99!');
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->putJson('/api/auth/profile', [
            'current_password'          => 'OldPass99!',
            'new_password'              => 'NewPass99!',
            'new_password_confirmation' => 'NewPass99!',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        // Verify the new password actually works for login
        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('NewPass99!', $user->fresh()->password)
        );
    }

    public function test_password_change_fails_with_wrong_current_password(): void
    {
        $user  = $this->createVerifiedUser('OldPass99!');
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->putJson('/api/auth/profile', [
            'current_password'          => 'WrongPassword!',
            'new_password'              => 'NewPass99!',
            'new_password_confirmation' => 'NewPass99!',
        ]);

        $response->assertStatus(422)
                 ->assertJson(['success' => false]);

        // Password must NOT have changed
        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('OldPass99!', $user->fresh()->password)
        );
    }

    public function test_password_change_fails_when_new_password_same_as_current(): void
    {
        $user  = $this->createVerifiedUser('OldPass99!');
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->putJson('/api/auth/profile', [
            'current_password'          => 'OldPass99!',
            'new_password'              => 'OldPass99!',   // same as current
            'new_password_confirmation' => 'OldPass99!',
        ]);

        $response->assertStatus(422)
                 ->assertJson(['success' => false])
                 ->assertJsonStructure(['errors' => ['new_password']]);
    }
}
