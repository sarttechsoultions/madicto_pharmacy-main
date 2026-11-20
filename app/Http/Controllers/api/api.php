<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AddressModel;
use App\Models\BannersModel;
use App\Models\CartModel;
use App\Models\CategoryModel;
use App\Models\MedicineFavourite;
use App\Models\medicineModel;
use App\Models\OrdersModel;
use App\Models\OrderItemModel;
use App\Models\CoustmerMedicineModel;
use App\Models\ReviewModel;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class api extends Controller
{


    public function getMedicines(Request $request)
    {
        $id = $request->id ?? $request->route('id');

        // Single medicine details lookup
        if ($id) {
            return $this->getMedicineDetail($id, $request);
        }

        $query = medicineModel::with([
            'category',
            'reviews.coustmer'
        ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Exclude Inactive medicines unless specifically asked for inactive or all
        if ($request->status === 'Inactive') {
            $query->where('status', 'Inactive');
        } elseif ($request->filled('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'In Stock':
                    $query->where(function ($q) {
                        $q->whereNull('status')->orWhere('status', '!=', 'Inactive');
                    })->where('quantity', '>', 5);
                    break;
                case 'Low Stock':
                    $query->where(function ($q) {
                        $q->whereNull('status')->orWhere('status', '!=', 'Inactive');
                    })->where('quantity', '>', 0)->where('quantity', '<=', 5);
                    break;
                case 'Out of Stock':
                    $query->where(function ($q) {
                        $q->whereNull('status')->orWhere('status', '!=', 'Inactive');
                    })->where('quantity', 0);
                    break;
                default:
                    $query->where('status', $request->status);
                    break;
            }
        } else {
            // Default: only active medicines shown to customers
            $query->active();
        }

        // Comprehensive search: checks search, query, q, keyword, name, medicine_name
        $rawSearch = $request->input('search')
            ?? $request->input('query')
            ?? $request->input('q')
            ?? $request->input('keyword')
            ?? $request->input('name')
            ?? $request->input('medicine_name');

        $searchTerm = is_string($rawSearch) ? trim($rawSearch) : '';
        if ($searchTerm !== '') {
            $query->search($searchTerm);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('manufacturer')) {
            $query->where('manufacturer', 'LIKE', '%' . $request->manufacturer . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float)$request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float)$request->max_price);
        }

        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->unit_type);
        }

        if ($request->filled('discount')) {
            $query->where('discount', '>=', (float)$request->discount);
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'Price ↑':
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'Price ↓':
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'Name (A-Z)':
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'Name (Z-A)':
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'popular':
                case 'trending':
                    $query->orderByDesc('reviews_count');
                    break;
                default:
                    $query->latest('id');
                    break;
            }
        } else {
            $query->latest('id');
        }

        $perPage = max(1, min(100, (int)$request->input('per_page', 20)));
        $medicines = $query->paginate($perPage);

        $userId = auth('sanctum')->id() ?? auth()->id() ?? $request->coustmer_id ?? $request->customer_id;
        $favouriteIds = ($userId && Schema::hasTable('medicine_favourites'))
            ? MedicineFavourite::where('coustmer_id', $userId)->pluck('medicine_id')->map(fn($v) => (string)$v)->toArray()
            : [];

        $medicines->getCollection()->transform(function ($medicine) use ($favouriteIds) {
            $medicine->is_favourite = in_array($medicine->id, $favouriteIds);
            return $medicine;
        });

        return response()->json([
            'success'    => true,
            'status'     => true,
            'message'    => 'Medicines fetched successfully',
            'data'       => $medicines->items(),
            'pagination' => [
                'total'         => $medicines->total(),
                'per_page'      => $medicines->perPage(),
                'current_page'  => $medicines->currentPage(),
                'last_page'     => $medicines->lastPage(),
                'has_more_pages'=> $medicines->hasMorePages(),
                'next_page_url' => $medicines->nextPageUrl(),
                'prev_page_url' => $medicines->previousPageUrl(),
            ],
            'medicines'  => $medicines,
        ], 200);
    }

    public function getMedicineDetail($id, Request $request = null)
    {
        $medicine = medicineModel::with([
            'category',
            'reviews.coustmer'
        ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->find($id);

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Medicine not found with ID ' . $id,
                'data'    => null,
            ], 404);
        }

        // Calculate ratings breakdown
        $medicine->ratings_breakdown = [
            5 => ReviewModel::where('medicine_id', $id)->where('rating', 5)->count(),
            4 => ReviewModel::where('medicine_id', $id)->where('rating', 4)->count(),
            3 => ReviewModel::where('medicine_id', $id)->where('rating', 3)->count(),
            2 => ReviewModel::where('medicine_id', $id)->where('rating', 2)->count(),
            1 => ReviewModel::where('medicine_id', $id)->where('rating', 1)->count(),
        ];

        $userId = auth('sanctum')->id() ?? auth()->id() ?? ($request ? ($request->coustmer_id ?? $request->customer_id) : null);
        $medicine->is_favourite = ($userId && Schema::hasTable('medicine_favourites'))
            ? MedicineFavourite::where('coustmer_id', $userId)->where('medicine_id', $medicine->id)->exists()
            : false;

        return response()->json([
            'success' => true,
            'status'  => true,
            'message' => 'Medicine details fetched successfully',
            'data'    => $medicine,
        ], 200);
    }

    public function relatedMedicines($id)
    {
        $medicine = medicineModel::find($id);

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Medicine not found',
                'data'    => [],
            ], 404);
        }

        $query = medicineModel::with(['category', 'reviews.coustmer'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('id', '!=', $medicine->id)
            ->active();

        $query->where(function ($q) use ($medicine) {
            if (!empty($medicine->category_id)) {
                $q->where('category_id', $medicine->category_id);
            }
            if (!empty($medicine->manufacturer)) {
                $q->orWhere('manufacturer', $medicine->manufacturer);
            }
        });

        $relatedMedicines = $query->take(10)->get();

        // If fewer than 4 related items, supplement with active medicines from same general group
        if ($relatedMedicines->count() < 4) {
            $fallback = medicineModel::with(['category', 'reviews.coustmer'])
                ->where('id', '!=', $medicine->id)
                ->whereNotIn('id', $relatedMedicines->pluck('id'))
                ->active()
                ->take(10 - $relatedMedicines->count())
                ->get();
            $relatedMedicines = $relatedMedicines->concat($fallback);
        }

        return response()->json([
            'success' => true,
            'status'  => true,
            'message' => 'Related medicines fetched successfully',
            'data'    => $relatedMedicines,
        ], 200);
    }

    public function getCategory(Request $request)
    {
        if ($request->filled('id')) {

            $category = CategoryModel::find($request->id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $category->is_available = medicineModel::where(
                'category_id',
                $category->id
            )->exists();

            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        }

        $query = CategoryModel::query();

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'LIKE', '%' . $request->description . '%');
        }

        $categories = $query->get();

        $categories->transform(function ($category) {

            $category->is_available = medicineModel::where(
                'category_id',
                $category->id
            )->exists();

            return $category;
        });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function categoryIsAvailable()
    {
        $categories = CategoryModel::withCount('medicines')->get();

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'is_available' => $category->medicines_count > 0,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function getBanners()
    {
        $banners = BannersModel::get();

        $banners->transform(function ($banner) {

            $banner->medicines = medicineModel::where(
                'discount',
                $banner->discount
            )->get();

            return $banner;
        });

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }
    public function getOrders(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $orders = OrdersModel::with('items')
            ->where('coustmer_id', auth()->id())
            ->latest()
            ->paginate($perPage);

        foreach ($orders as $order) {

            foreach ($order->items as $item) {

                $rating = ReviewModel::where('medicine_id', $item->medicine_id)
                    ->where('coustmer_id', auth()->id())
                    ->first();

                $item->is_rated = $rating ? true : false;
            }
        }

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

                if ($medicine->quantity < $quantity) {
                    return response()->json([
                        'status' => false,
                        'message' => $medicine->name . ' stock not available'
                    ], 422);
                }

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

                    $medicine = MedicineModel::lockForUpdate()
                        ->find($item->medicine_id);

                    if (!$medicine) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Medicine not found'
                        ], 404);
                    }

                    if ($medicine->quantity < $item->quantity) {
                        return response()->json([
                            'status' => false,
                            'message' => $medicine->name . ' quantity not available'
                        ], 422);
                    }

                    $lineTotal = $medicine->price * $item->quantity;

                    $subtotal += $lineTotal;

                    $items->push([
                        'medicine_id' => $medicine->id,
                        'medicine_name' => $medicine->name,
                        'medicine_image' => $medicine->image,
                        'price' => $medicine->price,
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

                $medicine = MedicineModel::find($item['medicine_id']);

                if ($medicine) {

                    $medicine->quantity =
                        max(0, $medicine->quantity - $item['quantity']);

                    if ($medicine->quantity == 0) {
                        $medicine->status = 'Out of Stock';
                    } elseif ($medicine->quantity <= 5) {
                        $medicine->status = 'Low Stock';
                    } else {
                        $medicine->status = 'In Stock';
                    }

                    $medicine->save();
                }
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
            'full_name' => ['required', 'string'],
            'address_label' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'street_address' => ['required', 'string'],
            'landmark' => ['required', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'pin_code' => ['required', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        // Agar default address select kiya hai
        if ($request->boolean('is_default')) {
            AddressModel::where('coustmer_id', auth()->id())
                ->update(['is_default' => false]);
        }

        $address = AddressModel::create([
            'coustmer_id' => auth()->id(),
            'full_name' => $request->full_name,
            'address_label' => $request->address_label,
            'phone_number' => $request->phone_number,
            'street_address' => $request->street_address,
            'landmark' => $request->landmark,
            'city' => $request->city,
            'state' => $request->state,
            'pin_code' => $request->pin_code,
            'is_default' => $request->boolean('is_default'),
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
            'full_name' => ['required', 'string'],
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
            'full_name' => $request->full_name,
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
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        $customerId = auth()->id();

        $order = OrdersModel::where('id', $request->order_id)
            ->where('coustmer_id', $customerId)
            ->where('status', 'Delivered')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Delivered order not found'
            ], 400);
        }

        $items = OrderItemModel::where('order_id', $order->id)->get();

        foreach ($items as $item) {

            $alreadyReviewed = ReviewModel::where('coustmer_id', $customerId)
                ->where('medicine_id', $item->medicine_id)
                ->exists();

            if (!$alreadyReviewed) {

                ReviewModel::create([
                    'coustmer_id' => $customerId,
                    'medicine_id' => $item->medicine_id,
                    'rating' => $request->rating,
                    'review' => $request->review
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Review added for all medicines in this order'
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
        $perPage = $request->get('per_page', 10);

        $cart = CartModel::with('medicine')
            ->where('coustmer_id', auth()->id())
            ->latest()
            ->paginate($perPage);

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

    public function toggleFavourite(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicine,id',
        ]);

        $favourite = MedicineFavourite::where([
            'coustmer_id' => auth()->id(),
            'medicine_id' => $request->medicine_id,
        ])->first();

        if ($favourite) {

            $favourite->delete();

            return response()->json([
                'status' => true,
                'is_favourite' => false,
                'message' => 'Medicine removed from favourites'
            ]);
        }

        MedicineFavourite::create([
            'coustmer_id' => auth()->id(),
            'medicine_id' => $request->medicine_id,
        ]);

        return response()->json([
            'status' => true,
            'is_favourite' => true,
            'message' => 'Medicine added to favourites'
        ]);
    }

    public function favouriteMedicines(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $medicineIds = MedicineFavourite::where('coustmer_id', auth()->id())
            ->pluck('medicine_id');

        $medicines = medicineModel::whereIn('id', $medicineIds)
            ->latest()
            ->paginate($perPage);

        $medicines->getCollection()->transform(function ($medicine) {
            $medicine->is_favourite = true;
            return $medicine;
        });

        return response()->json([
            'status' => true,
            'data' => $medicines
        ]);
    }

    public function trendingProducts(Request $request)
    {
        $userId = auth('sanctum')->id() ?? auth()->id() ?? $request->coustmer_id ?? $request->customer_id;

        $perPage = max(1, min(100, (int)$request->input('per_page', 10)));

        $medicines = medicineModel::with(['category'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->active()
            ->orderByDesc('reviews_count')
            ->paginate($perPage);

        $favouriteIds = $userId ? MedicineFavourite::where('coustmer_id', $userId)
            ->pluck('medicine_id')
            ->flip() : collect();

        $medicines->getCollection()->transform(function ($medicine) use ($favouriteIds) {
            $medicine->is_favourite = isset($favouriteIds[$medicine->id]);
            return $medicine;
        });

        return response()->json([
            'success'    => true,
            'status'     => true,
            'message'    => 'Trending products fetched successfully',
            'data'       => $medicines->items(),
            'pagination' => [
                'current_page'  => $medicines->currentPage(),
                'last_page'     => $medicines->lastPage(),
                'per_page'      => $medicines->perPage(),
                'total'         => $medicines->total(),
                'has_more_pages'=> $medicines->hasMorePages(),
                'next_page_url' => $medicines->nextPageUrl(),
                'prev_page_url' => $medicines->previousPageUrl(),
            ]
        ], 200);
    }

    public function dealsOfDay(Request $request)
    {
        $userId = auth('sanctum')->id() ?? auth()->id() ?? $request->coustmer_id ?? $request->customer_id;

        $perPage = max(1, min(100, (int)$request->input('per_page', 10)));

        $medicines = medicineModel::with(['category'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->active()
            ->where('dod', 1) // Only Deal Of Day products
            ->orderByDesc('discount')
            ->paginate($perPage);

        $favouriteIds = $userId ? MedicineFavourite::where('coustmer_id', $userId)
            ->pluck('medicine_id')
            ->flip() : collect();

        $medicines->getCollection()->transform(function ($item) use ($favouriteIds) {
            $item->is_favourite = isset($favouriteIds[$item->id]);
            return $item;
        });

        return response()->json([
            'success'    => true,
            'status'     => true,
            'message'    => 'Deals of the day fetched successfully',
            'data'       => $medicines->items(),
            'pagination' => [
                'current_page'  => $medicines->currentPage(),
                'last_page'     => $medicines->lastPage(),
                'per_page'      => $medicines->perPage(),
                'total'         => $medicines->total(),
                'has_more_pages'=> $medicines->hasMorePages(),
                'next_page_url' => $medicines->nextPageUrl(),
                'prev_page_url' => $medicines->previousPageUrl(),
            ]
        ], 200);
    }



    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        DB::beginTransaction();

        try {

            $order = OrdersModel::with('items')->findOrFail($request->order_id);

            // Optional: Delivered order cancel na ho
            if ($order->status == 'Delivered') {
                return response()->json([
                    'status' => false,
                    'message' => 'Delivered order cannot be cancelled.'
                ], 400);
            }

            // Restore medicine stock
            foreach ($order->items as $item) {
                MedicineModel::where('id', $item->medicine_id)
                    ->increment('quantity', $item->quantity);
            }

            // Delete order items
            $order->items()->delete();

            // Delete order
            $order->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order cancelled successfully.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeCustomerMedicine(Request $request)
    {
        $request->validate([
            'img' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        $imagePath = null;

        if ($request->hasFile('img')) {

            $image = $request->file('img');

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move(
                public_path('uploads/cstmedicine'),
                $imageName
            );

            $imagePath = 'uploads/cstmedicine/' . $imageName;
        }

        $data = CoustmerMedicineModel::create([
            'coustmer_id' => auth()->id(),
            'img' => $imagePath
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data stored successfully',
            'data' => $data
        ]);
    }

    public function searchMedicines(Request $request)
    {
        $rawSearch = $request->input('search')
            ?? $request->input('query')
            ?? $request->input('q')
            ?? $request->input('keyword')
            ?? $request->input('medicine_name')
            ?? $request->input('name');

        $searchTerm = is_string($rawSearch) ? trim($rawSearch) : '';

        $query = medicineModel::with([
            'category',
            'reviews.coustmer'
        ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->active();

        if ($searchTerm !== '') {
            $query->search($searchTerm);
        }

        if ($request->filled('description')) {
            $query->where('description', 'LIKE', '%' . $request->description . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('manufacturer')) {
            $query->where('manufacturer', 'LIKE', '%' . $request->manufacturer . '%');
        }

        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->unit_type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float)$request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float)$request->max_price);
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'Price ↑':
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'Price ↓':
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'Name (A-Z)':
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'Name (Z-A)':
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                default:
                    $query->latest('id');
                    break;
            }
        } else {
            $query->latest('id');
        }

        $perPage = max(1, min(100, (int)$request->input('per_page', 20)));
        $medicines = $query->paginate($perPage);

        $userId = auth('sanctum')->id() ?? auth()->id() ?? $request->coustmer_id ?? $request->customer_id;
        $favouriteIds = ($userId && Schema::hasTable('medicine_favourites'))
            ? MedicineFavourite::where('coustmer_id', $userId)->pluck('medicine_id')->map(fn($v) => (string)$v)->toArray()
            : [];

        $medicines->getCollection()->transform(function ($medicine) use ($favouriteIds) {
            $medicine->is_favourite = in_array($medicine->id, $favouriteIds);
            return $medicine;
        });

        $count = $medicines->total();
        $message = $count > 0
            ? "Found {$count} medicine(s) matching your search"
            : ($searchTerm !== '' ? "No medicines found matching '{$searchTerm}'" : "No medicines found");

        return response()->json([
            'success'    => true,
            'status'     => true,
            'message'    => $message,
            'data'       => $medicines->items(),
            'pagination' => [
                'total'          => $medicines->total(),
                'per_page'       => $medicines->perPage(),
                'current_page'   => $medicines->currentPage(),
                'last_page'      => $medicines->lastPage(),
                'has_more_pages' => $medicines->hasMorePages(),
                'next_page_url'  => $medicines->nextPageUrl(),
                'prev_page_url'  => $medicines->previousPageUrl(),
            ],
            'medicines'  => $medicines,
        ], 200);
    }

    public function storeMedicine(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'price'            => 'required',
            'category_id'      => 'nullable',
            'quantity'         => 'nullable|numeric|min:0',
            'stock'            => 'nullable|numeric|min:0',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'medicine_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        try {
            $data = new medicineModel();
            $data->name = $request->name;
            $data->category_id = $request->category_id;
            $data->batch_no = $request->batch_no;
            $data->usage_instructions = $request->usage_instructions;
            $data->discount = $request->discount ?? 0;
            $data->manufacturer = $request->manufacturer;
            $data->reorder_level = $request->reorder_level ?? 20;
            $data->description = $request->description;
            $data->price = $request->price;
            $data->unit_type = $request->unit_type ?? 'Tablets';
            $data->pack_size = $request->pack_size;
            $data->manufacture_date = $request->manufacture_date;
            $data->expiry_date = $request->expiry_date;
            $data->quantity = $request->quantity ?? $request->stock ?? 0;
            $data->stock = $request->stock ?? $request->quantity ?? 0;

            if ($request->filled('status')) {
                $data->status = $request->status;
            } else {
                $qty = (int)$data->quantity;
                $data->status = $qty > 5 ? 'In Stock' : ($qty > 0 ? 'Low Stock' : 'Out of Stock');
            }

            if ($request->hasFile('image')) {
                File::ensureDirectoryExists(public_path('uploads/medicine'));
                $img = $request->file('image');
                $imageName = time() . '_main_' . uniqid() . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('uploads/medicine'), $imageName);
                $data->image = 'uploads/medicine/' . $imageName;
            }

            $medicineImages = [];
            if ($request->hasFile('medicine_image')) {
                File::ensureDirectoryExists(public_path('uploads/medicine/gallery'));
                foreach ($request->file('medicine_image') as $img) {
                    $fileName = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                    $img->move(public_path('uploads/medicine/gallery'), $fileName);
                    $medicineImages[] = 'uploads/medicine/gallery/' . $fileName;
                }
            }
            $data->medicine_image = json_encode($medicineImages);

            $data->save();

            return response()->json([
                'success' => true,
                'status'  => true,
                'message' => 'Medicine added successfully!',
                'data'    => $data->fresh(['category']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Failed to add medicine: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateMedicine(Request $request, $id)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'price'            => 'required',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'medicine_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        try {
            $medicine = medicineModel::findOrFail($id);
            $medicine->name = $request->name;
            $medicine->category_id = $request->category_id;
            $medicine->batch_no = $request->batch_no;
            $medicine->usage_instructions = $request->usage_instructions;
            $medicine->discount = $request->discount ?? $medicine->discount;
            $medicine->manufacturer = $request->manufacturer;
            $medicine->reorder_level = $request->reorder_level ?? $medicine->reorder_level;
            $medicine->description = $request->description;
            $medicine->price = $request->price;
            $medicine->unit_type = $request->unit_type ?? $medicine->unit_type;
            $medicine->pack_size = $request->pack_size ?? $medicine->pack_size;
            $medicine->manufacture_date = $request->manufacture_date ?? $medicine->manufacture_date;
            $medicine->expiry_date = $request->expiry_date ?? $medicine->expiry_date;
            $medicine->quantity = $request->quantity ?? $request->stock ?? $medicine->quantity;
            $medicine->stock = $request->stock ?? $request->quantity ?? $medicine->stock;

            if ($request->filled('status')) {
                $medicine->status = $request->status;
            } else {
                $qty = (int)$medicine->quantity;
                $medicine->status = $qty > 5 ? 'In Stock' : ($qty > 0 ? 'Low Stock' : 'Out of Stock');
            }

            if ($request->hasFile('image')) {
                if ($medicine->image) {
                    $existingPath = public_path($medicine->image);
                    if (File::exists($existingPath)) {
                        File::delete($existingPath);
                    }
                }
                File::ensureDirectoryExists(public_path('uploads/medicine'));
                $img = $request->file('image');
                $imageName = time() . '_main_' . uniqid() . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('uploads/medicine'), $imageName);
                $medicine->image = 'uploads/medicine/' . $imageName;
            }

            if ($request->hasFile('medicine_image')) {
                File::ensureDirectoryExists(public_path('uploads/medicine/gallery'));
                $gallery = [];
                foreach ($request->file('medicine_image') as $img) {
                    $fileName = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                    $img->move(public_path('uploads/medicine/gallery'), $fileName);
                    $gallery[] = 'uploads/medicine/gallery/' . $fileName;
                }
                $medicine->medicine_image = json_encode($gallery);
            }

            $medicine->save();

            return response()->json([
                'success' => true,
                'status'  => true,
                'message' => 'Medicine updated successfully!',
                'data'    => $medicine->fresh(['category']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Failed to update medicine: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroyMedicine($id)
    {
        try {
            $medicine = medicineModel::findOrFail($id);

            // Clean up main image
            if ($medicine->image && File::exists(public_path($medicine->image))) {
                File::delete(public_path($medicine->image));
            }

            $medicine->delete();

            return response()->json([
                'success' => true,
                'status'  => true,
                'message' => 'Medicine deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Failed to delete medicine: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function uploadMedicineImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        try {
            $medicine = medicineModel::findOrFail($id);

            File::ensureDirectoryExists(public_path('uploads/medicine'));
            $img = $request->file('image');
            $imageName = time() . '_main_' . uniqid() . '.' . $img->getClientOriginalExtension();
            $img->move(public_path('uploads/medicine'), $imageName);

            $medicine->image = 'uploads/medicine/' . $imageName;
            $medicine->save();

            return response()->json([
                'success'   => true,
                'status'    => true,
                'message'   => 'Medicine image uploaded successfully!',
                'image_url' => asset($medicine->image),
                'data'      => $medicine,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Failed to upload image: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleMedicineStatus($id)
    {
        try {
            $medicine = medicineModel::findOrFail($id);

            if ($medicine->status === 'Inactive') {
                $qty = (int)$medicine->quantity;
                $medicine->status = $qty > 5 ? 'In Stock' : ($qty > 0 ? 'Low Stock' : 'Out of Stock');
            } else {
                $medicine->status = 'Inactive';
            }

            $medicine->save();

            return response()->json([
                'success' => true,
                'status'  => true,
                'message' => "Medicine status updated to {$medicine->status}",
                'data'    => $medicine,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Failed to toggle status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // FCM token remove
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'fcm_token')) {
            $user->update([
                'fcm_token' => null
            ]);
        }

        // Current token delete
        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Anonymize email and referral code so the same email can register as a brand new account
        $timestamp = time();
        $user->email = 'deleted_' . $timestamp . '_' . $user->email;
        if ($user->referral_code) {
            $user->referral_code = 'del_' . $timestamp . '_' . $user->referral_code;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'fcm_token')) {
            $user->fcm_token = null;
        }

        $user->save();

        // Soft delete the user
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Account deleted successfully'
        ], 200);
    }
}
