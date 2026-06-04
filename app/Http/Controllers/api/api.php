<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AddressModel;
use App\Models\BannersModel;
use App\Models\CartModel;
use App\Models\CategoryModel;
use App\Models\medicineModel;
use App\Models\OrdersModel;
use App\Models\OrderItemModel;
use App\Models\ReviewModel;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class api extends Controller
{

    public function getMedicines(Request $request, $id = null)
    {
        // Single medicine by ID
        if ($id) {
            $medicine = medicineModel::find($id);

            if (!$medicine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Medicine not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $medicine
            ]);
        }

        // Multiple medicines with filters
        $query = medicineModel::query();

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->unit_type);
        }

        $medicines = $query->get();

        return response()->json([
            'success' => true,
            'data' => $medicines
        ]);
    }

    public function relatedMedicines($id)
    {
        $medicine = medicineModel::find($id);

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found'
            ], 404);
        }

        $relatedMedicines = medicineModel::where('id', '!=', $medicine->id)
            ->where(function ($query) use ($medicine) {

                $query->where(
                    'category_id',
                    $medicine->category_id
                );

                if (!empty($medicine->description)) {

                    $words = explode(
                        ' ',
                        trim($medicine->description)
                    );

                    foreach ($words as $word) {

                        if (strlen($word) > 3) {

                            $query->orWhere(
                                'description',
                                'LIKE',
                                '%' . $word . '%'
                            );
                        }
                    }
                }
            })
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $relatedMedicines
        ]);
    }

    public function getCategory(Request $request, $id = null)
    {
        if ($id) {
            $category = CategoryModel::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } {
            $query = CategoryModel::query();

            if ($request->filled('name')) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->filled('description')) {
                $query->where('description', 'LIKE', '%' . $request->description . '%');
            }

            $categories = $query->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        }
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
        $orders = OrdersModel::with('items')
            ->where('coustmer_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function orderDetail($id)
    {
        $order = OrdersModel::with('items')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }

    public function OrdersCreated(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:address,id',
            'medicine_id' => 'nullable|integer',
            'quantity' => 'nullable|integer|min:1',
            'wallet_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {

            $address = AddressModel::where('id', $request->address_id)
                ->where('coustmer_id', auth()->id())
                ->first();

            if (!$address) {
                return response()->json([
                    'status' => false,
                    'message' => 'Address not found'
                ], 404);
            }

            $lastOrder = OrdersModel::latest('id')->first();
            $nextId = $lastOrder ? $lastOrder->id + 1 : 1;

            $orderCode = 'ORD-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $items = collect();
            $subtotal = 0;

            /*
        |--------------------------------------------------------------------------
        | Buy Now Flow
        |--------------------------------------------------------------------------
        */
            if ($request->filled('medicine_id')) {

                $medicine = MedicineModel::find($request->medicine_id);

                if (!$medicine) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Medicine not found'
                    ], 404);
                }

                $quantity = $request->quantity ?? 1;

                $subtotal = $medicine->price * $quantity;

                $items->push([
                    'medicine_id' => $medicine->id,
                    'medicine_name' => $medicine->name,
                    'medicine_image' => $medicine->image,
                    'price' => $medicine->price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Cart Checkout Flow
        |--------------------------------------------------------------------------
        */ else {

                $cartItems = CartModel::with('medicine')
                    ->where('coustmer_id', auth()->id())
                    ->get();

                if ($cartItems->isEmpty()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Cart is empty'
                    ], 404);
                }

                foreach ($cartItems as $item) {

                    $lineTotal = $item->medicine->price * $item->quantity;

                    $subtotal += $lineTotal;

                    $items->push([
                        'medicine_id' => $item->medicine_id,
                        'medicine_name' => $item->medicine->name,
                        'medicine_image' => $item->medicine->image,
                        'price' => $item->medicine->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $lineTotal,
                    ]);
                }
            }

            $shippingCharge = 0;
            $discount = 0;
            $totalAmount = $subtotal + $shippingCharge - $discount;

            /*
|--------------------------------------------------------------------------
| Wallet Logic
|--------------------------------------------------------------------------
*/

            $user = User::find(auth()->id());

            $walletUsed = $request->wallet_amount ?? 0;

            // Safety checks

            if ($walletUsed > $user->wallet) {

                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient wallet balance'
                ], 422);
            }

            // Wallet total amount se jyada nahi ho sakta

            if ($walletUsed > $totalAmount) {
                $walletUsed = $totalAmount;
            }

            // Final payable amount

            $finalAmount = $totalAmount - $walletUsed;

            // Wallet deduct

            if ($walletUsed > 0) {

                $user->wallet =
                    $user->wallet - $walletUsed;

                $user->save();
            }

            $order = OrdersModel::create([
                'order_id' => $orderCode,
                'coustmer_id' => auth()->id(),

                'delivery_address_label' => $address->address_label,
                'delivery_phone_number' => $address->phone_number,
                'delivery_street_address' => $address->street_address,
                'delivery_city' => $address->city,
                'delivery_state' => $address->state,
                'delivery_pin_code' => $address->pin_code,
                'delivery_landmark' => $address->landmark,

                'subtotal' => $subtotal,
                'shipping_charge' => $shippingCharge,
                'discount' => $walletUsed,
                'total_amount' => $finalAmount,

                'status' => 'Ordered',
                'ordered_at' => now(),
            ]);

            foreach ($items as $item) {

                OrderItemModel::create([
                    'order_id' => $order->id,

                    'medicine_id' => $item['medicine_id'],
                    'medicine_name' => $item['medicine_name'],
                    'medicine_image' => $item['medicine_image'],

                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Cart clear only when cart checkout
            if (!$request->filled('medicine_id')) {

                CartModel::where(
                    'coustmer_id',
                    auth()->id()
                )->delete();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully',
                'order_id' => $orderCode,
                'order_db_id' => $order->id
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAddresses()
    {
        $addresses = AddressModel::where('coustmer_id', auth()->id())->get();
        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function AddressCreated(Request $request)
    {
        $request->validate([
            'address_label' => ['required', 'string'],
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
            'phone_number' => $request->phone_number,
            'street_address' => $request->street_address,
            'landmark' => $request->landmark,
            'city' => $request->city,
            'state' => $request->state,
            'pin_code' => $request->pin_code,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address Created',
            'address' => $address
        ]);
    }

    public function AddressUpdated(Request $request, $id)
    {
        $request->validate([
            'address_label' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'street_address' => ['required', 'string'],
            'landmark' => ['required', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'pin_code' => ['required', 'string'],
        ]);

        $address = AddressModel::where('id', $id)
            ->where('coustmer_id', auth()->id())
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $address->update([
            'address_label' => $request->address_label,
            'phone_number' => $request->phone_number,
            'street_address' => $request->street_address,
            'landmark' => $request->landmark,
            'city' => $request->city,
            'state' => $request->state,
            'pin_code' => $request->pin_code,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    public function AddressDeleted($id)
    {
        $address = AddressModel::where('id', $id)
            ->where('coustmer_id', auth()->id())
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    public function getProfile()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'email' => ['nullable', 'email'],
            'number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'profile_img' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::guard('sanctum')->user();

        $user->name = $request->name ?? 'User';
        $user->gender = $request->gender;

        if (!empty($user->email)) {

            $user->number = $request->number ?? $user->number;
        } elseif (!empty($user->number)) {

            $user->email = $request->email ?? $user->email;
        }

        if ($request->hasFile('profile_img')) {

            $path = public_path('uploads/profile');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            if ($user->profile_img && File::exists(public_path($user->profile_img))) {
                File::delete(public_path($user->profile_img));
            }

            $image = $request->file('profile_img');

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move($path, $imageName);

            // Save full relative path
            $user->profile_img = 'uploads/profile/' . $imageName;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function shareReferral()
    {
        $user = Auth::user();

        $referralCode = $user->referral_code;

        $referralLink = "https://yourapp.onelink.me/abcd?ref={$referralCode}";

        $shareMessage = "Download the Medicto Pharmacy app to get upto 51% savings on your medicines. Use the link or code below to get ₹200 off on your first order.\n\nCode: {$referralCode}\n\nLink: {$referralLink}";

        return response()->json([
            'success' => true,
            'referral_code' => $referralCode,
            'referral_link' => $referralLink,
            'share_message' => $shareMessage,
        ]);
    }


    public function applyReferralCode(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string'
        ]);

        $user = Auth::user();

        if ($user->referred_by != null) {

            return response()->json([
                'success' => false,
                'message' => 'Referral code already used'
            ]);
        }

        if ($user->referral_code == $request->referral_code) {

            return response()->json([
                'success' => false,
                'message' => 'You cannot use your own referral code'
            ]);
        }

        $referrer = User::where(
            'referral_code',
            $request->referral_code
        )->first();

        if (!$referrer) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid referral code'
            ]);
        }


        $referrer->wallet += 100;
        $referrer->save();

        $user->wallet += 50;

        $user->referred_by = $referrer->id;

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Referral code applied successfully',
            'your_wallet' => $user->wallet,
            'referred_by' => $user->referred_by
        ]);
    }

    // GET REVIEWS
    public function index($medicine_id)
    {
        $reviews = ReviewModel::with('coustmer')
            ->where('medicine_id', $medicine_id)
            ->latest()
            ->get();

        $averageRating = ReviewModel::where('medicine_id', $medicine_id)
            ->avg('rating');

        return response()->json([

            'success' => true,

            'average_rating' => round($averageRating, 1),

            'total_reviews' => $reviews->count(),

            'reviews' => $reviews

        ]);
    }

    // ADD REVIEW
    public function store(Request $request)
    {
        $request->validate([

            'medicine_id' => 'required|exists:medicine,id',

            'rating' => 'required|integer|min:1|max:5',

            'review' => 'nullable|string'

        ]);

        $alreadyReviewed = ReviewModel::where('coustmer_id', auth()->id())
            ->where('medicine_id', $request->medicine_id)
            ->exists();

        if ($alreadyReviewed) {

            return response()->json([

                'success' => false,

                'message' => 'You already reviewed this medicine'

            ], 400);
        }

        $review = ReviewModel::create([

            'coustmer_id' => auth()->id(),

            'medicine_id' => $request->medicine_id,

            'rating' => $request->rating,

            'review' => $request->review

        ]);

        return response()->json([

            'success' => true,

            'message' => 'Review added successfully',

            'data' => $review

        ]);
    }

    // UPDATE REVIEW
    public function update(Request $request, $id)
    {
        $review = ReviewModel::where('coustmer_id', auth()->id())
            ->findOrFail($id);

        $request->validate([

            'rating' => 'required|integer|min:1|max:5',

            'review' => 'nullable|string'

        ]);

        $review->update([

            'rating' => $request->rating,

            'review' => $request->review

        ]);

        return response()->json([

            'success' => true,

            'message' => 'Review updated successfully',

            'data' => $review

        ]);
    }

    // DELETE REVIEW
    public function destroy($id)
    {
        $review = ReviewModel::where('coustmer_id', auth()->id())
            ->findOrFail($id);

        $review->delete();

        return response()->json([

            'success' => true,

            'message' => 'Review deleted successfully'

        ]);
    }

    public function carts(Request $request)
    {
        $cart = CartModel::with('medicine')
            ->where('coustmer_id', auth()->id())
            ->get();

        return response()->json([
            'status' => true,
            'data' => $cart
        ]);
    }

    // Add To Cart
    public function cartstore(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicine,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = CartModel::where('coustmer_id', auth()->id())
            ->where('medicine_id', $request->medicine_id)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $request->quantity);
        } else {
            $cart = CartModel::create([
                'coustmer_id' => auth()->id(),
                'medicine_id' => $request->medicine_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Medicine added to cart',
            'data' => $cart
        ]);
    }

    // Update Quantity
    public function cartupdate(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = CartModel::where('id', $id)
            ->where('coustmer_id', auth()->id())
            ->first();

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cart->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cart updated'
        ]);
    }

    // Remove Item
    public function cartdestroy($id)
    {
        $cart = CartModel::where('id', $id)
            ->where('coustmer_id', auth()->id())
            ->first();

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart'
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
