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
        Schema::table('medicine', function (Blueprint $table) {
            if (!Schema::hasColumn('medicine', 'medicine_image')) {
                $table->longText('medicine_image')->nullable()->after('image');
            }
            if (!Schema::hasColumn('medicine', 'dod')) {
                $table->tinyInteger('dod')->default(2)->comment('1: Yes, 2: No')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine', function (Blueprint $table) {
            if (Schema::hasColumn('medicine', 'medicine_image')) {
                $table->dropColumn('medicine_image');
            }
            if (Schema::hasColumn('medicine', 'dod')) {
                $table->dropColumn('dod');
            }
        });
    }
};
