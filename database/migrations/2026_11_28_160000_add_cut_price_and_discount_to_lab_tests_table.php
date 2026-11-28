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
        if (Schema::hasTable('lab_tests')) {
            Schema::table('lab_tests', function (Blueprint $table) {
                if (!Schema::hasColumn('lab_tests', 'cut_price')) {
                    $table->decimal('cut_price', 10, 2)->nullable()->after('price');
                }
                if (!Schema::hasColumn('lab_tests', 'discount_percentage')) {
                    $table->integer('discount_percentage')->nullable()->after('cut_price');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lab_tests')) {
            Schema::table('lab_tests', function (Blueprint $table) {
                if (Schema::hasColumn('lab_tests', 'discount_percentage')) {
                    $table->dropColumn('discount_percentage');
                }
                if (Schema::hasColumn('lab_tests', 'cut_price')) {
                    $table->dropColumn('cut_price');
                }
            });
        }
    }
};
