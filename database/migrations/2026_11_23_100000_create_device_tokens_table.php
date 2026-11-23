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
        if (!Schema::hasTable('device_tokens')) {
            Schema::create('device_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->text('fcm_token');
                $table->string('token_hash', 64)->unique(); // SHA-256 hash for fast indexing and deduplication
                $table->enum('device_type', ['android', 'ios', 'web', 'other'])->default('android')->index();
                $table->string('device_id', 191)->nullable()->index();
                $table->string('device_name', 191)->nullable();
                $table->string('app_version', 50)->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamp('last_active_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
