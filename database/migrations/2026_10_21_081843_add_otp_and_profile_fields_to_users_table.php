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
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable()->change();
            }
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'otp')) {
                $table->string('otp')->nullable()->after('referral_code');
            }
            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('2')->after('otp_expires_at');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('Active')->after('role');
            }
            if (!Schema::hasColumn('users', 'profile_img')) {
                $table->string('profile_img')->nullable()->after('status');
            }
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
