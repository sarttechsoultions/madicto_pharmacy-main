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
        Schema::create('lab_test_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('lab_test_id');
            $table->unsignedBigInteger('lab_id');
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->integer('patient_age')->nullable();
            $table->string('patient_gender')->nullable(); // Male, Female, Other
            $table->string('sample_collection_type')->default('home_collection'); // home_collection, lab_visit
            $table->text('collection_address')->nullable();
            $table->date('appointment_date')->nullable();
            $table->string('appointment_time')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('payment_method')->nullable(); // cod, online
            $table->string('status')->default('pending'); // pending, sample_collected, in_process, completed, cancelled
            $table->string('report_file')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lab_test_id')->references('id')->on('lab_tests')->onDelete('cascade');
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');

            $table->index('status');
            $table->index('order_id');
            $table->index('user_id');
            $table->index('lab_id');
            $table->index('appointment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_test_orders');
    }
};
