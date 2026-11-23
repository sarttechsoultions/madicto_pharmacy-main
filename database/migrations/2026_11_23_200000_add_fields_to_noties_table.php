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
        Schema::table('noties', function (Blueprint $table) {
            if (!Schema::hasColumn('noties', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('noties', 'target_role')) {
                $table->enum('target_role', ['all', 'user', 'admin'])->default('all')->after('user_id')->index();
            }
            if (!Schema::hasColumn('noties', 'screen')) {
                $table->string('screen', 100)->nullable()->after('type');
            }
            if (!Schema::hasColumn('noties', 'reference_id')) {
                $table->string('reference_id', 100)->nullable()->after('screen')->index();
            }
            if (!Schema::hasColumn('noties', 'data')) {
                $table->json('data')->nullable()->after('reference_id');
            }
            if (!Schema::hasColumn('noties', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('status')->index();
            }
            if (!Schema::hasColumn('noties', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('noties', function (Blueprint $table) {
            $columns = ['user_id', 'target_role', 'screen', 'reference_id', 'data', 'is_read', 'read_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('noties', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
