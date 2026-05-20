<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AddressModel;
use App\Models\BannersModel;
use App\Models\CategoryModel;
use App\Models\medicineModel;
use App\Models\OrdersModel;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;

class api extends Controller
{

    public function getMedicines()
    {
        $medicines = medicineModel::all();
        return response()->json([
            'success' => true,
            'data' => $medicines
        ]);
    }

    public function getCategory()
    {
        $categories = CategoryModel::all();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function getBanners()
    {
        $banners = BannersModel::all();
        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }
    public function getOrders()
    {
        $orders = OrdersModel::with('medicine', 'user')->get();
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function OrdersCreated(Request $request)
    {
        $request->validate([
            'medicine_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer'],
        ]);

        $lastOrder = OrdersModel::latest('id')->first();

        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;

        $orderId = 'ORD-' . $nextId;

        $order = OrdersModel::create([
            'order_id' => $orderId,
            'user_id' => auth()->id(),
            'medicine_id' => $request->medicine_id,
            'quantity' => $request->quantity,
            'processing_at' => now(),
        ]);


        return response()->json([
            'message' => 'Order Created',
            'order' => $order
        ]);
    }

    public function AddressCreated(Request $request)
    {
        $request->validate([
            'address_label' => ['required', 'string'],
            'full_name' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'street_address' => ['required', 'string'],
            'landmark' => ['required', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'pin_code' => ['required', 'string'],
        ]);

        $address = AddressModel::create([
            'coustmer_id' => auth()->id(),
            'address_label' => $request->address_label,
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'street_address' => $request->street_address,
            'landmark' => $request->landmark,
            'city' => $request->city,
            'state' => $request->state,
            'pin_code' => $request->pin_code,
        ]);

        return response()->json([
            'message' => 'Address Created',
            'address' => $address
        ]);
    }

    public function AddressDeleted(Request $request)
    {
        $request->validate([
            'address_id' => ['required', 'integer'],
        ]);

        $address = AddressModel::where('id', $request->address_id)
            ->where('coustmer_id', auth()->id())
            ->first();

        if (!$address) {
            return response()->json([
                'message' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'message' => 'Address Deleted'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
