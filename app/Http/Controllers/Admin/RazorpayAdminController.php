<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrdersModel;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Services\RazorpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayAdminController extends Controller
{
    protected RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Display Payments Dashboard & Transaction Ledger
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'order'])->latest();

        // 1. Text Search (Order ID, Payment ID, Customer, Contact)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('razorpay_payment_id', 'like', "%{$search}%")
                  ->orWhere('razorpay_order_id', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%")
                  ->orWhere('booking_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('number', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // 3. Payment Type Filter
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('payment_type', $request->type);
        }

        // 4. Method Filter (upi, card, netbanking, wallet)
        if ($request->filled('method') && $request->method !== 'all') {
            $query->where('method', $request->method);
        }

        // 5. Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->paginate(20)->withQueryString();

        // Summary Aggregates
        $totalRevenue = Payment::whereIn('status', ['captured', 'authorized'])->sum('amount');
        $successfulCount = Payment::where('status', 'captured')->count();
        $pendingCount = Payment::where('status', 'created')->count();
        $failedCount = Payment::where('status', 'failed')->count();
        $refundedCount = Payment::where('status', 'refunded')->count();

        $setting = PaymentSetting::getRazorpay();

        return view('admin.payments.index', compact(
            'payments',
            'totalRevenue',
            'successfulCount',
            'pendingCount',
            'failedCount',
            'refundedCount',
            'setting'
        ));
    }

    /**
     * Get single payment detail for admin modal
     */
    public function show($id): JsonResponse
    {
        $payment = Payment::with(['user', 'order'])->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment record not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'payment' => $payment,
        ]);
    }

    /**
     * Sync payment status live from Razorpay API
     */
    public function sync($id): JsonResponse
    {
        $payment = Payment::findOrFail($id);

        if (empty($payment->razorpay_payment_id)) {
            return response()->json([
                'success' => false,
                'message' => 'No Razorpay Payment ID associated with this record to sync.',
            ], 400);
        }

        try {
            $details = $this->razorpayService->fetchPayment($payment->razorpay_payment_id);
            $payment->status = $details['status'] ?? $payment->status;
            $payment->method = $details['method'] ?? $payment->method;
            $payment->bank = $details['bank'] ?? $payment->bank;
            $payment->wallet = $details['wallet'] ?? $payment->wallet;
            $payment->vpa = $details['vpa'] ?? $payment->vpa;
            $payment->raw_response = $details;
            $payment->save();

            // Sync with order
            if ($payment->order_id && $payment->status === 'captured') {
                OrdersModel::where('order_id', $payment->order_id)->update([
                    'payment_status' => 'Paid',
                    'payment_method' => 'Razorpay',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment status synced successfully from Razorpay.',
                'payment' => $payment,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process Refund via Razorpay API
     */
    public function refund(Request $request, $id): JsonResponse
    {
        $payment = Payment::findOrFail($id);

        if (empty($payment->razorpay_payment_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot refund a payment without a valid Razorpay Payment ID.',
            ], 400);
        }

        if ($payment->status === 'refunded') {
            return response()->json([
                'success' => false,
                'message' => 'This payment has already been refunded.',
            ], 400);
        }

        $amount = $request->filled('amount') ? (float)$request->amount : null;
        $reason = $request->input('reason', 'Customer requested refund');

        try {
            $refundResult = $this->razorpayService->refund(
                $payment->razorpay_payment_id,
                $amount,
                ['admin_reason' => $reason]
            );

            $refundId = $refundResult['id'] ?? null;
            $refundAmount = isset($refundResult['amount']) ? ($refundResult['amount'] / 100) : ($amount ?: $payment->amount);

            $payment->status = 'refunded';
            $payment->refund_id = $refundId;
            $payment->refund_status = $refundResult['status'] ?? 'processed';
            $payment->amount_refunded = $refundAmount;
            $payment->save();

            if (!empty($payment->order_id)) {
                OrdersModel::where('order_id', $payment->order_id)->update([
                    'payment_status' => 'Refunded',
                ]);
            }

            return response()->json([
                'success'       => true,
                'message'       => "Refund of ₹" . number_format($refundAmount, 2) . " processed successfully.",
                'refund_id'     => $refundId,
                'refund_amount' => $refundAmount,
            ]);
        } catch (\Throwable $e) {
            Log::error("Admin refund error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Refund failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display Razorpay Gateway Settings & Developer API Reference
     */
    public function settings()
    {
        $setting = PaymentSetting::getRazorpay();
        $testResult = null;

        return view('admin.payments.settings', compact('setting', 'testResult'));
    }

    /**
     * Update Razorpay Gateway Credentials & Status
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'key_id'       => 'required|string|max:191',
            'key_secret'   => 'required|string|max:191',
            'mode'         => 'required|in:test,live',
            'is_active'    => 'nullable|boolean',
            'currency'     => 'required|string|max:10',
            'display_name' => 'nullable|string|max:100',
            'description'  => 'nullable|string|max:255',
        ]);

        $setting = PaymentSetting::getRazorpay();
        $setting->update([
            'key_id'       => trim($request->key_id),
            'key_secret'   => trim($request->key_secret),
            'mode'         => $request->mode,
            'is_active'    => $request->has('is_active') ? true : false,
            'currency'     => strtoupper(trim($request->currency)),
            'display_name' => $request->display_name ?? 'Razorpay Payment Gateway',
            'description'  => $request->description,
        ]);

        return redirect()->route('admin.payments.settings')
            ->with('success', 'Razorpay payment settings updated successfully!');
    }

    /**
     * Test Razorpay Connection Live
     */
    public function testConnection(): JsonResponse
    {
        $result = $this->razorpayService->testConnection();

        return response()->json($result);
    }
}
