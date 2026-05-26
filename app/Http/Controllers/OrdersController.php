<?php

namespace App\Http\Controllers;

use App\Models\OrdersModel;
use App\Models\User;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = OrdersModel::paginate(20);
        $ordertotal = OrdersModel::where('status', 'Pending')->count();
        $orderdelivered = OrdersModel::where('status', 'Delivered')->count();
        $paymentpending = OrdersModel::where('payment_status', 'Pending')->count();
        $paymentpaid = OrdersModel::where('payment_status', 'Paid')->count();

        return view('admin.orders', compact('orders', 'ordertotal', 'orderdelivered', 'paymentpending', 'paymentpaid'));
    }

    public function updateOrderStatus(Request $request)
    {
        $order = OrdersModel::find($request->id);

        $order->status = $request->status;

        if ($request->status == 'Pending') {
            $order->processing_at = now();
        }

        if ($request->status == 'Shipped') {
            $order->shipped_at = now();
        }

        if ($request->status == 'Delivered') {
            $order->delivered_at = now();
        }

        $order->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function updatePaymentStatus(Request $request)
    {
        $order = OrdersModel::find($request->id);

        $order->payment_status = $request->payment_status;

        $order->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function details($id)
    {
        $order = OrdersModel::with(['user', 'medicine'])->findOrFail($id);

        $customerOrders = OrdersModel::with('medicine')
            ->where('user_id', $order->user_id)
            ->get();

        return view('admin.order-details', compact('order', 'customerOrders'));
    }

    public function destroy($id)
    {
        $order = OrdersModel::findOrFail($id);
        $order->delete();

        return redirect()->back()->with('success', 'Order deleted successfully');
    }
}
