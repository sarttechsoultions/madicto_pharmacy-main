<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'delivery_address_label')) {
                    $table->string('delivery_address_label')->nullable()->after('coustmer_id');
                }
                if (!Schema::hasColumn('orders', 'delivery_phone_number')) {
                    $table->string('delivery_phone_number')->nullable()->after('delivery_address_label');
                }
                if (!Schema::hasColumn('orders', 'delivery_street_address')) {
                    $table->string('delivery_street_address')->nullable()->after('delivery_phone_number');
                }
                if (!Schema::hasColumn('orders', 'delivery_landmark')) {
                    $table->string('delivery_landmark')->nullable()->after('delivery_street_address');
                }
                if (!Schema::hasColumn('orders', 'delivery_city')) {
                    $table->string('delivery_city')->nullable()->after('delivery_landmark');
                }
                if (!Schema::hasColumn('orders', 'delivery_state')) {
                    $table->string('delivery_state')->nullable()->after('delivery_city');
                }
                if (!Schema::hasColumn('orders', 'delivery_pin_code')) {
                    $table->string('delivery_pin_code')->nullable()->after('delivery_state');
                }
                if (!Schema::hasColumn('orders', 'subtotal')) {
                    $table->decimal('subtotal', 10, 2)->default(0)->after('delivery_pin_code');
                }
                if (!Schema::hasColumn('orders', 'shipping_charge')) {
                    $table->decimal('shipping_charge', 10, 2)->default(0)->after('subtotal');
                }
                if (!Schema::hasColumn('orders', 'discount')) {
                    $table->decimal('discount', 10, 2)->default(0)->after('shipping_charge');
                }
                if (!Schema::hasColumn('orders', 'ordered_at')) {
                    $table->timestamp('ordered_at')->nullable();
                }
                if (!Schema::hasColumn('orders', 'confirmed_at')) {
                    $table->timestamp('confirmed_at')->nullable();
                }
                if (!Schema::hasColumn('orders', 'out_for_delivery_at')) {
                    $table->timestamp('out_for_delivery_at')->nullable();
                }
                if (!Schema::hasColumn('orders', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'medicine_image')) {
                    $table->string('medicine_image')->nullable()->after('medicine_name');
                }
                if (!Schema::hasColumn('order_items', 'subtotal')) {
                    $table->decimal('subtotal', 10, 2)->default(0)->after('price');
                }
            });
        }
    }

    public function down(): void
    {
        // Safe down migration
    }
};
