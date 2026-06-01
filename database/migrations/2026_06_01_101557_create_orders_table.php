<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->string('order_id')->unique();

            $table->unsignedBigInteger('coustmer_id');

            // Delivery Address Snapshot
            $table->string('delivery_address_label')->nullable();
            $table->string('delivery_phone_number')->nullable();
            $table->string('delivery_street_address')->nullable();
            $table->string('delivery_landmark')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_state')->nullable();
            $table->string('delivery_pin_code')->nullable();

            // Amounts
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            // Status
            $table->enum('status', [
                'Ordered',
                'Confirmed',
                'Processing',
                'Out For Delivery',
                'Delivered',
                'Cancelled'
            ])->default('Ordered');

            // Tracking Timeline
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('out_for_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
