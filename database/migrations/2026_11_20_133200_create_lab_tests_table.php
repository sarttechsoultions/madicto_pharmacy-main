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
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_name');
            $table->string('test_code')->nullable();
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('category_id');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->text('preparation_instructions')->nullable();
            $table->string('sample_type')->nullable(); // e.g. Blood, Urine
            $table->string('report_delivery_time')->nullable(); // e.g. 24 Hours
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('lab_test_categories')->onDelete('cascade');

            $table->index('status');
            $table->index('lab_id');
            $table->index('category_id');
            $table->index('test_name');
            $table->index('test_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
