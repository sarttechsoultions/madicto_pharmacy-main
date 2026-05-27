<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AddressModel;
use App\Models\BannersModel;
use App\Models\CategoryModel;
use App\Models\medicineModel;
use App\Models\OrdersModel;
use App\Models\ReviewModel;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\File;

class api extends Controller
{

    public function getMedicines(Request $request)
    {
        $query = medicineModel::query();

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        // Filter by category_id
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by unit
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->unit_type);
        }

        $medicines = $query->get();

        return response()->json([
            'success' => true,
            'data' => $medicines
        ]);
    }

    public function getCategory(Request $request)
    {
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'profile_img' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::guard('sanctum')->user();

        $user->name = $request->name;
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

            if ($user->profile_img && File::exists($path . '/' . $user->profile_img)) {
                File::delete($path . '/' . $user->profile_img);
            }

            $image = $request->file('profile_img');

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move($path, $imageName);

            $user->profile_img = $imageName;
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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
