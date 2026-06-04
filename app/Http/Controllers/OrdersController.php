<?php

namespace App\Http\Controllers;

use App\Models\OrdersModel;
use App\Models\User;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = OrdersModel::with([
            'user',
            'items'
        ])
            ->latest()
            ->paginate(20);

        $ordertotal = OrdersModel::where('status', 'Ordered')->count();

        $orderdelivered = OrdersModel::where('status', 'Delivered')->count();

        $paymentpending = OrdersModel::where('payment_status', 'Pending')->count();

        $paymentpaid = OrdersModel::where('payment_status', 'Paid')->count();

        return view(
            'admin.orders',
            compact(
                'orders',
                'ordertotal',
                'orderdelivered',
                'paymentpending',
                'paymentpaid'
            )
        );
    }

    public function updateOrderStatus(Request $request)
    {
        $order = OrdersModel::findOrFail($request->id);

        $order->status = $request->status;

        switch ($request->status) {

            case 'Confirmed':
                $order->confirmed_at = now();
                break;

            case 'Processing':
                $order->processing_at = now();
                break;

            case 'Shipped':
                $order->out_for_delivery_at = now();
                break;

            case 'Delivered':
                $order->delivered_at = now();
                break;

            case 'Cancelled':
                $order->cancelled_at = now();
                break;
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated'
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
        $order = OrdersModel::with([
            'user',
            'items'
        ])->findOrFail($id);

        return view('admin.order-details', compact('order'));
    }

    public function destroy($id)
    {
        $order = OrdersModel::findOrFail($id);
        $order->delete();

        return redirect()->back()->with('success', 'Order deleted successfully');
    }
}
