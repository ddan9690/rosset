<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('salutation')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();

            $table->string('phone');
            $table->string('tsc_number')->unique();
            $table->string('id_number')->unique();
            $table->string('membership_number')->nullable()->unique();

            $table->string('school_level')->nullable();
            $table->string('school')->nullable();

            $table->string('email')->unique()->nullable();

            $table->enum('status', ['pending', 'active', 'suspended', 'deregistered'])->default('pending');
            $table->boolean('registration_fee_paid')->default(false);

            $table->string('password');

            $table->string('email_otp')->nullable();
            $table->timestamp('email_otp_expires_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('sms_otp')->nullable();
            $table->timestamp('sms_otp_expires_at')->nullable();
            $table->timestamp('sms_verified_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
