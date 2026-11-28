<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayService
{
    protected ?Api $api = null;
    protected ?PaymentSetting $setting = null;

    public function __construct()
    {
        $this->loadSettings();
    }

    /**
     * Load settings from database or fallback to config
     */
    protected function loadSettings(): void
    {
        try {
            $this->setting = PaymentSetting::where('gateway', 'razorpay')->first();
        } catch (\Throwable $e) {
            $this->setting = null;
        }

        $key = $this->getKeyId();
        $secret = $this->getKeySecret();

        if (!empty($key) && !empty($secret)) {
            $this->api = new Api($key, $secret);
        }
    }

    /**
     * Active Razorpay Key ID
     */
    public function getKeyId(): string
    {
        return (string)($this->setting->key_id ?? config('razorpay.key', 'rzp_test_U5ZUZ60slH5W2J'));
    }

    /**
     * Active Razorpay Key Secret
     */
    public function getKeySecret(): string
    {
        return (string)($this->setting->key_secret ?? config('razorpay.secret', '7s3I21rNfySQ4rH5GISlWIyu'));
    }

    /**
     * Active Currency
     */
    public function getCurrency(): string
    {
        return (string)($this->setting->currency ?? config('razorpay.currency', 'INR'));
    }

    /**
     * Active Gateway Mode: 'test' or 'live'
     */
    public function getMode(): string
    {
        return (string)($this->setting->mode ?? config('razorpay.mode', 'test'));
    }

    /**
     * Check if Razorpay is actively enabled
     */
    public function isEnabled(): bool
    {
        if ($this->setting) {
            return (bool)$this->setting->is_active;
        }
        return true;
    }

    /**
     * Get underlying Razorpay API SDK Client
     */
    public function getApi(): Api
    {
        if (!$this->api) {
            $this->api = new Api($this->getKeyId(), $this->getKeySecret());
        }
        return $this->api;
    }

    /**
     * Create a Razorpay Order
     *
     * @param float $amount Amount in Rupees (e.g. 450.00)
     * @param string $receipt Unique identifier or receipt ID
     * @param array $notes Additional metadata
     * @param string|null $currency Currency code (defaults to INR)
     * @return array
     */
    public function createOrder(float $amount, string $receipt, array $notes = [], ?string $currency = null): array
    {
        $currency = $currency ?: $this->getCurrency();
        // Convert Rupees to Paise (Razorpay expects integer in lowest currency unit)
        $amountInPaise = (int)round($amount * 100);

        if ($amountInPaise <= 0) {
            throw new \InvalidArgumentException("Payment amount must be greater than zero.");
        }

        $orderPayload = [
            'receipt'         => (string)$receipt,
            'amount'          => $amountInPaise,
            'currency'        => strtoupper($currency),
            'notes'           => $notes,
            'payment_capture' => 1, // Auto-capture payment on authorization
        ];

        try {
            $razorpayOrder = $this->getApi()->order->create($orderPayload);
            $orderData = $razorpayOrder->toArray();

            return [
                'success'           => true,
                'razorpay_order_id' => $orderData['id'],
                'amount_paise'      => $orderData['amount'],
                'amount'            => $amount,
                'currency'          => $orderData['currency'],
                'receipt'           => $orderData['receipt'] ?? $receipt,
                'status'            => $orderData['status'],
                'raw'               => $orderData,
            ];
        } catch (\Throwable $e) {
            Log::error("Razorpay createOrder failed: " . $e->getMessage(), ['payload' => $orderPayload]);
            throw $e;
        }
    }

    /**
     * Verify payment signature from mobile SDK
     *
     * @param string $razorpayOrderId
     * @param string $razorpayPaymentId
     * @param string $razorpaySignature
     * @return bool
     */
    public function verifySignature(string $razorpayOrderId, string $razorpayPaymentId, string $razorpaySignature): bool
    {
        try {
            $expectedSignature = hash_hmac(
                'sha256',
                $razorpayOrderId . '|' . $razorpayPaymentId,
                $this->getKeySecret()
            );

            return hash_equals($expectedSignature, $razorpaySignature);
        } catch (\Throwable $e) {
            Log::error("Razorpay verifySignature error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch payment details by Razorpay Payment ID (pay_xxxx)
     */
    public function fetchPayment(string $paymentId): array
    {
        try {
            $payment = $this->getApi()->payment->fetch($paymentId);
            return $payment->toArray();
        } catch (\Throwable $e) {
            Log::error("Razorpay fetchPayment [{$paymentId}] error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Capture payment if authorized
     */
    public function capturePayment(string $paymentId, float $amount, string $currency = 'INR'): array
    {
        $amountInPaise = (int)round($amount * 100);
        try {
            $payment = $this->getApi()->payment->fetch($paymentId)->capture([
                'amount'   => $amountInPaise,
                'currency' => $currency,
            ]);
            return $payment->toArray();
        } catch (\Throwable $e) {
            Log::error("Razorpay capturePayment [{$paymentId}] error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Issue full or partial refund
     */
    public function refund(string $paymentId, ?float $amount = null, array $notes = []): array
    {
        try {
            $params = ['notes' => $notes];
            if ($amount !== null && $amount > 0) {
                $params['amount'] = (int)round($amount * 100);
            }

            $refund = $this->getApi()->payment->fetch($paymentId)->refund($params);
            return $refund->toArray();
        } catch (\Throwable $e) {
            Log::error("Razorpay refund [{$paymentId}] error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Live test connection with credentials
     */
    public function testConnection(): array
    {
        try {
            // Fetch 1 order to check auth
            $orders = $this->getApi()->order->all(['count' => 1]);
            return [
                'success' => true,
                'message' => 'Connected successfully to Razorpay API in ' . strtoupper($this->getMode()) . ' mode.',
                'mode'    => $this->getMode(),
                'key_id'  => $this->getKeyId(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
                'mode'    => $this->getMode(),
                'key_id'  => $this->getKeyId(),
            ];
        }
    }
}
