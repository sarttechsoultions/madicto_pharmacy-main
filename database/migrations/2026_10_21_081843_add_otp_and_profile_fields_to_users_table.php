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
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->string('referral_code')->nullable()->after('email');
            $table->string('otp')->nullable()->after('referral_code');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
            $table->string('role')->default('2')->after('otp_expires_at');
            $table->string('status')->default('Active')->after('role');
            $table->string('profile_img')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'otp', 'otp_expires_at', 'role', 'status', 'profile_img']);
        });
    }
};
