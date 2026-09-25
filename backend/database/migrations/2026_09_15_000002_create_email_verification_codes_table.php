<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the email_verification_codes table for storing hashed, expirable
     * verification codes sent to users during registration and resend flows.
     */
    public function up(): void
    {
        Schema::create('email_verification_codes', function (Blueprint $table) {
            $table->id();

            // Reference to the user; cascade delete keeps DB clean
            $table->foreignId('user_id')
                ->unique()          // One active code per user at a time
                ->constrained('users')
                ->cascadeOnDelete();

            // SHA-256 hash of the 6-digit code — never stored in plain text
            $table->string('code', 64);

            // When the code becomes invalid
            $table->timestamp('expires_at');

            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_verification_codes');
    }
};
