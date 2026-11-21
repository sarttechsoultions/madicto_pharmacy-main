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
        // 1. lab_test_bookings table
        if (!Schema::hasTable('lab_test_bookings')) {
            Schema::create('lab_test_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('booking_id', 100)->unique();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('lab_test_id')->nullable();
                $table->unsignedBigInteger('lab_id')->nullable();

                // Customer details
                $table->string('customer_name');
                $table->string('customer_phone', 50);
                $table->string('customer_email', 191)->nullable();

                // Complete address details
                $table->text('address');
                $table->string('city', 100);
                $table->string('state', 100);
                $table->string('pincode', 20);

                // Schedule details
                $table->date('booking_date');
                $table->string('booking_time', 50); // e.g. "08:00 AM - 09:00 AM"

                // Pricing
                $table->decimal('total_amount', 10, 2)->default(0.00);

                // Status
                $table->string('status', 50)->default('Pending');
                // Statuses: Pending, Confirmed, Sample Collection Scheduled, Sample Collected, Completed, Cancelled

                $table->string('payment_status', 50)->default('Pending');
                $table->string('payment_method', 50)->default('Cash on Collection');
                $table->string('sample_collection_type', 50)->default('home_collection');

                $table->text('additional_instructions')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->string('report_file', 255)->nullable();

                $table->timestamps();
                $table->softDeletes();

                // Foreign keys
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('lab_test_id')->references('id')->on('lab_tests')->onDelete('set null');
                $table->foreign('lab_id')->references('id')->on('labs')->onDelete('set null');

                // Indexes
                $table->index('booking_id');
                $table->index('user_id');
                $table->index('lab_test_id');
                $table->index('lab_id');
                $table->index('status');
                $table->index('booking_date');
                $table->index('customer_phone');
            });
        }

        // 2. lab_test_booking_items table (supports multiple tests or itemized receipt)
        if (!Schema::hasTable('lab_test_booking_items')) {
            Schema::create('lab_test_booking_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->unsignedBigInteger('lab_test_id');
                $table->unsignedBigInteger('lab_id');

                // Snapshot fields
                $table->string('test_name');
                $table->string('test_code', 100)->nullable();
                $table->string('lab_name');
                $table->decimal('price', 10, 2)->default(0.00);

                $table->timestamps();

                $table->foreign('booking_id')->references('id')->on('lab_test_bookings')->onDelete('cascade');
                $table->foreign('lab_test_id')->references('id')->on('lab_tests')->onDelete('cascade');
                $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');

                $table->index('booking_id');
                $table->index('lab_test_id');
                $table->index('lab_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_test_booking_items');
        Schema::dropIfExists('lab_test_bookings');
    }
};
