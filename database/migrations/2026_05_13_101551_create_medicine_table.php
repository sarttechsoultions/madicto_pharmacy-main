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
        Schema::create('medicine', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('image')->nullable();
            $table->string('category_id')->nullable();
            $table->string('batch_no')->nullable();
            $table->string('usage_instructions')->nullable();
            $table->string('discount')->nullable();
            $table->string('quantity')->nullable();
            $table->string('reorder_level')->nullable();
            $table->string('unit_type')->nullable();
            $table->string('pack_size')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('manufacture_date')->nullable();
            $table->string('expiry_date')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->string('price')->nullable();
            $table->string('stock')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine');
    }
};
