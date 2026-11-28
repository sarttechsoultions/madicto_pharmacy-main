<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Payment Settings Table (For dynamic Admin control)
        if (!Schema::hasTable('payment_settings')) {
            Schema::create('payment_settings', function (Blueprint $table) {
                $table->id();
                $table->string('gateway')->default('razorpay')->unique();
                $table->string('key_id')->nullable();
                $table->string('key_secret')->nullable();
                $table->enum('mode', ['test', 'live'])->default('test');
                $table->boolean('is_active')->default(true);
                $table->string('currency', 10)->default('INR');
                $table->string('webhook_secret')->nullable();
                $table->string('display_name')->default('Razorpay');
                $table->text('description')->nullable();
                $table->json('extra_settings')->nullable();
                $table->timestamps();
            });

            // Seed initial Razorpay credentials provided by user
            DB::table('payment_settings')->insert([
                'gateway' => 'razorpay',
                'key_id' => 'rzp_test_U5ZUZ60slH5W2J',
                'key_secret' => '7s3I21rNfySQ4rH5GISlWIyu',
                'mode' => 'test',
                'is_active' => true,
                'currency' => 'INR',
                'display_name' => 'Razorpay Payment Gateway',
                'description' => 'Online payments via UPI, Credit/Debit Cards, NetBanking, and Wallets.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Payments Table (Complete Transaction Ledger)
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('order_id')->nullable()->index();
                $table->string('booking_id')->nullable()->index();
                $table->string('payment_type', 50)->default('order'); // 'order', 'lab_booking', 'wallet'
                $table->string('razorpay_order_id')->nullable()->index();
                $table->string('razorpay_payment_id')->nullable()->index();
                $table->string('razorpay_signature')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 10)->default('INR');
                $table->string('status', 50)->default('created'); // created, authorized, captured, failed, refunded
                $table->string('method', 50)->nullable(); // card, netbanking, wallet, upi
                $table->string('bank', 100)->nullable();
                $table->string('wallet', 100)->nullable();
                $table->string('vpa', 100)->nullable(); // UPI ID
                $table->string('email')->nullable();
                $table->string('contact', 30)->nullable();
                $table->string('error_code', 100)->nullable();
                $table->text('error_description')->nullable();
                $table->string('refund_id', 100)->nullable();
                $table->string('refund_status', 50)->nullable();
                $table->decimal('amount_refunded', 10, 2)->default(0);
                $table->json('raw_response')->nullable();
                $table->timestamps();
            });
        }

        // 3. Add payment columns to orders table if missing
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('payment_status');
                }
                if (!Schema::hasColumn('orders', 'razorpay_order_id')) {
                    $table->string('razorpay_order_id')->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('orders', 'razorpay_payment_id')) {
                    $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
        Schema::dropIfExists('payments');

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'razorpay_payment_id')) {
                    $table->dropColumn('razorpay_payment_id');
                }
                if (Schema::hasColumn('orders', 'razorpay_order_id')) {
                    $table->dropColumn('razorpay_order_id');
                }
                if (Schema::hasColumn('orders', 'payment_method')) {
                    $table->dropColumn('payment_method');
                }
            });
        }
    }
};
