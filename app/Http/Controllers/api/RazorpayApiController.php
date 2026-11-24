<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\LabTestBooking;
use App\Models\OrdersModel;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use App\Services\RazorpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RazorpayApiController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Public API: Get Razorpay Client Configuration
     * The mobile app calls this to get the active Key ID and checkout options.
     */
    public function getConfig(): JsonResponse
    {
        $setting = PaymentSetting::getRazorpay();

        return response()->json([
            'status'        => true,
            'is_enabled'    => (bool)$setting->is_active,
            'key_id'        => $this->razorpayService->getKeyId(),
            'currency'      => $this->razorpayService->getCurrency(),
            'mode'          => $this->razorpayService->getMode(),
            'company_name'  => config('app.name', 'Medicto Pharmacy'),
            'display_name'  => $setting->display_name ?? 'Razorpay Payment Gateway',
            'description'   => $setting->description ?? 'Secure online payments via UPI, Cards, NetBanking & Wallets',
        ]);
    }

    /**
     * Authenticated API: Create Razorpay Order
     * Generates a Razorpay Order ID (order_xxxx) for Mobile Checkout.
     */
    public function createOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount'       => 'nullable|numeric|min:1',
            'order_id'     => 'nullable|string',
            'booking_id'   => 'nullable|string',
            'payment_type' => 'nullable|string|in:order,lab_booking,wallet,other',
            'notes'        => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated user.',
            ], 401);
        }

        if (!$this->razorpayService->isEnabled()) {
            return response()->json([
                'status'  => false,
                'message' => 'Online payment via Razorpay is currently disabled by administrator.',
            ], 403);
        }

        $orderId = $request->input('order_id');
        $bookingId = $request->input('booking_id');
        $paymentType = $request->input('payment_type', 'order');
        $amount = (float)$request->input('amount', 0);
        $receipt = 'RCPT-' . time();
        $notes = $request->input('notes', []);

        // 1. Medicine Order Context
        if (!empty($orderId)) {
            $order = OrdersModel::where('order_id', $orderId)
                ->orWhere('id', $orderId)
                ->first();

            if (!$order) {
                return response()->json([
                    'status'  => false,
                    'message' => "Order #{$orderId} not found.",
                ], 404);
            }

            if (strtolower($order->payment_status) === 'paid') {
                return response()->json([
                    'status'  => false,
                    'message' => "Order #{$orderId} has already been paid.",
                ], 400);
            }

            $orderId = (string)$order->order_id;
            $amount = (float)$order->total_amount;
            $receipt = (string)$order->order_id;
            $paymentType = 'order';
            $notes['order_id'] = $orderId;
            $notes['order_db_id'] = (string)$order->id;
        }
        // 2. Lab Test Booking Context
        elseif (!empty($bookingId)) {
            $booking = LabTestBooking::where('booking_id', $bookingId)
                ->orWhere('id', $bookingId)
                ->first();

            if (!$booking) {
                return response()->json([
                    'status'  => false,
                    'message' => "Lab test booking #{$bookingId} not found.",
                ], 404);
            }

            if (strtolower($booking->payment_status) === 'paid') {
                return response()->json([
                    'status'  => false,
                    'message' => "Booking #{$bookingId} has already been paid.",
                ], 400);
            }

            $bookingId = (string)$booking->booking_id;
            $amount = (float)($booking->final_amount ?? $booking->total_amount);
            $receipt = (string)$booking->booking_id;
            $paymentType = 'lab_booking';
            $notes['booking_id'] = $bookingId;
        }

        if ($amount <= 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid payment amount. Amount must be greater than zero.',
            ], 422);
        }

        $notes['user_id'] = (string)$user->id;
        $notes['user_email'] = (string)($user->email ?? '');
        $notes['user_phone'] = (string)($user->number ?? '');

        try {
            // Call Razorpay API to generate order
            $razorpayOrder = $this->razorpayService->createOrder(
                $amount,
                $receipt,
                $notes,
                $this->razorpayService->getCurrency()
            );

            // Record transaction in database
            $payment = Payment::create([
                'user_id'           => $user->id,
                'order_id'          => $orderId,
                'booking_id'        => $bookingId,
                'payment_type'      => $paymentType,
                'razorpay_order_id' => $razorpayOrder['razorpay_order_id'],
                'amount'            => $amount,
                'currency'          => $razorpayOrder['currency'],
                'status'            => 'created',
                'email'             => $user->email,
                'contact'           => $user->number,
                'raw_response'      => $razorpayOrder['raw'],
            ]);

            // If order exists, also update its razorpay_order_id
            if (!empty($orderId)) {
                OrdersModel::where('order_id', $orderId)->update([
                    'razorpay_order_id' => $razorpayOrder['razorpay_order_id'],
                    'payment_method'    => 'Razorpay',
                ]);
            }

            return response()->json([
                'status'            => true,
                'message'           => 'Razorpay order created successfully',
                'payment_id'        => $payment->id,
                'razorpay_order_id' => $razorpayOrder['razorpay_order_id'],
                'amount_paise'      => $razorpayOrder['amount_paise'],
                'amount'            => $amount,
                'currency'          => $razorpayOrder['currency'],
                'key_id'            => $this->razorpayService->getKeyId(),
                'company_name'      => config('app.name', 'Medicto Pharmacy'),
                'customer'          => [
                    'name'    => $user->name ?? 'Customer',
                    'email'   => $user->email ?? '',
                    'contact' => $user->number ?? '',
                ],
                'receipt'           => $receipt,
                'order_id'          => $orderId,
                'booking_id'        => $bookingId,
            ]);
        } catch (\Throwable $e) {
            Log::error("RazorpayApiController::createOrder failed: " . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Failed to initialize Razorpay order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Authenticated API: Verify Razorpay Payment Signature
     * The mobile app calls this after the Razorpay checkout sheet succeeds.
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
            'order_id'            => 'nullable|string',
            'booking_id'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();
        $razorpayOrderId   = $request->input('razorpay_order_id');
        $razorpayPaymentId = $request->input('razorpay_payment_id');
        $razorpaySignature = $request->input('razorpay_signature');
        $orderId           = $request->input('order_id');
        $bookingId         = $request->input('booking_id');

        // 1. Verify cryptographic signature
        $isValid = $this->razorpayService->verifySignature(
            $razorpayOrderId,
            $razorpayPaymentId,
            $razorpaySignature
        );

        if (!$isValid) {
            Log::warning("Razorpay signature verification failed for order: {$razorpayOrderId}, payment: {$razorpayPaymentId}");

            Payment::where('razorpay_order_id', $razorpayOrderId)->update([
                'status'            => 'failed',
                'error_code'        => 'SIGNATURE_VERIFICATION_FAILED',
                'error_description' => 'Calculated signature did not match provided Razorpay signature.',
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Invalid payment signature. Payment could not be verified.',
            ], 400);
        }

        // 2. Fetch full payment details from Razorpay to verify capture status and method
        $paymentDetails = [];
        $method = null;
        $bank = null;
        $wallet = null;
        $vpa = null;
        $email = null;
        $contact = null;
        $status = 'captured';

        try {
            $paymentDetails = $this->razorpayService->fetchPayment($razorpayPaymentId);
            $method = $paymentDetails['method'] ?? null;
            $bank = $paymentDetails['bank'] ?? null;
            $wallet = $paymentDetails['wallet'] ?? null;
            $vpa = $paymentDetails['vpa'] ?? null;
            $email = $paymentDetails['email'] ?? null;
            $contact = $paymentDetails['contact'] ?? null;
            $status = $paymentDetails['status'] ?? 'captured';
        } catch (\Throwable $e) {
            Log::warning("Failed to fetch Razorpay payment details [{$razorpayPaymentId}]: " . $e->getMessage());
        }

        DB::beginTransaction();
        try {
            // 3. Update or create payment record
            $payment = Payment::where('razorpay_order_id', $razorpayOrderId)->first();

            if (!$payment) {
                $payment = new Payment();
                $payment->user_id = $user->id ?? null;
                $payment->razorpay_order_id = $razorpayOrderId;
                $payment->amount = isset($paymentDetails['amount']) ? ($paymentDetails['amount'] / 100) : 0;
                $payment->currency = $paymentDetails['currency'] ?? 'INR';
            }

            $payment->razorpay_payment_id = $razorpayPaymentId;
            $payment->razorpay_signature = $razorpaySignature;
            $payment->status = $status === 'captured' ? 'captured' : 'authorized';
            $payment->method = $method;
            $payment->bank = $bank;
            $payment->wallet = $wallet;
            $payment->vpa = $vpa;
            if ($email) $payment->email = $email;
            if ($contact) $payment->contact = $contact;
            if (!empty($orderId)) $payment->order_id = $orderId;
            if (!empty($bookingId)) $payment->booking_id = $bookingId;
            if (!empty($paymentDetails)) $payment->raw_response = $paymentDetails;
            $payment->save();

            // 4. Update Medicine Order if applicable
            $resolvedOrderId = $orderId ?: $payment->order_id;
            $order = null;
            if (!empty($resolvedOrderId)) {
                $order = OrdersModel::where('order_id', $resolvedOrderId)
                    ->orWhere('id', $resolvedOrderId)
                    ->first();

                if ($order) {
                    $order->payment_status = 'Paid';
                    $order->payment_method = 'Razorpay';
                    $order->razorpay_order_id = $razorpayOrderId;
                    $order->razorpay_payment_id = $razorpayPaymentId;
                    $order->save();

                    // Trigger notification strictly once
                    try {
                        $orderUser = $order->user ?: $user;
                        if ($orderUser) {
                            $order->loadMissing('items');
                            app(FirebaseNotificationService::class)->notifyNewOrderPlaced($order, $orderUser);
                        }
                    } catch (\Throwable $e) {
                        Log::warning("Notification dispatch warning in verifyPayment: " . $e->getMessage());
                    }
                }
            }

            // 5. Update Lab Booking if applicable
            $resolvedBookingId = $bookingId ?: $payment->booking_id;
            if (!empty($resolvedBookingId)) {
                $booking = LabTestBooking::where('booking_id', $resolvedBookingId)
                    ->orWhere('id', $resolvedBookingId)
                    ->first();

                if ($booking) {
                    $booking->payment_status = 'paid';
                    $booking->payment_method = 'Razorpay';
                    $booking->transaction_id = $razorpayPaymentId;
                    $booking->save();
                }
            }

            DB::commit();

            return response()->json([
                'status'         => true,
                'message'        => 'Payment verified and captured successfully',
                'payment'        => [
                    'id'                  => $payment->id,
                    'razorpay_order_id'   => $razorpayOrderId,
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'amount'              => (float)$payment->amount,
                    'currency'            => $payment->currency,
                    'status'              => $payment->status,
                    'method'              => $payment->method,
                    'vpa'                 => $payment->vpa,
                    'order_id'            => $payment->order_id,
                    'booking_id'          => $payment->booking_id,
                    'paid_at'             => $payment->updated_at->toDateTimeString(),
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("RazorpayApiController::verifyPayment DB error: " . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Failed to process payment confirmation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Authenticated API: Record Payment Failure
     * Mobile app calls this when user cancels or gateway returns an error.
     */
    public function recordFailure(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'razorpay_order_id'   => 'required|string',
            'error_code'          => 'nullable|string',
            'error_description'   => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)->first();

        if ($payment) {
            $payment->status = 'failed';
            $payment->error_code = $request->error_code;
            $payment->error_description = $request->error_description;
            if ($request->filled('razorpay_payment_id')) {
                $payment->razorpay_payment_id = $request->razorpay_payment_id;
            }
            $payment->save();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Payment failure logged successfully',
        ]);
    }

    /**
     * Authenticated API: Customer's Payment History
     */
    public function getHistory(Request $request): JsonResponse
    {
        $user = auth()->user();
        $perPage = min((int)$request->input('per_page', 20), 50);

        $payments = Payment::where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data'   => $payments->items(),
            'meta'   => [
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
                'total'        => $payments->total(),
            ],
        ]);
    }

    /**
     * Authenticated API: Single Payment Details
     */
    public function getPaymentDetail($id): JsonResponse
    {
        $user = auth()->user();

        $payment = Payment::where('user_id', $user->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                  ->orWhere('razorpay_order_id', $id)
                  ->orWhere('razorpay_payment_id', $id);
            })
            ->first();

        if (!$payment) {
            return response()->json([
                'status'  => false,
                'message' => 'Payment record not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $payment,
        ]);
    }

    /**
     * Public Webhook: Asynchronous Razorpay Events
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        Log::info("Razorpay Webhook received", ['event' => $request->input('event')]);

        $event = $request->input('event');
        $payloadData = $request->input('payload', []);

        if ($event === 'payment.captured') {
            $paymentEntity = $payloadData['payment']['entity'] ?? [];
            $razorpayPaymentId = $paymentEntity['id'] ?? null;
            $razorpayOrderId = $paymentEntity['order_id'] ?? null;

            if ($razorpayOrderId && $razorpayPaymentId) {
                Payment::where('razorpay_order_id', $razorpayOrderId)->update([
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'status'              => 'captured',
                    'method'              => $paymentEntity['method'] ?? null,
                ]);

                OrdersModel::where('razorpay_order_id', $razorpayOrderId)->update([
                    'payment_status'      => 'Paid',
                    'razorpay_payment_id' => $razorpayPaymentId,
                ]);
            }
        } elseif ($event === 'payment.failed') {
            $paymentEntity = $payloadData['payment']['entity'] ?? [];
            $razorpayOrderId = $paymentEntity['order_id'] ?? null;

            if ($razorpayOrderId) {
                Payment::where('razorpay_order_id', $razorpayOrderId)->update([
                    'status'            => 'failed',
                    'error_code'        => $paymentEntity['error_code'] ?? null,
                    'error_description' => $paymentEntity['error_description'] ?? null,
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
