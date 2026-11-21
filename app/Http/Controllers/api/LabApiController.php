<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use App\Models\LabTestBooking;
use App\Models\LabTestBookingItem;
use App\Models\LabTestOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LabApiController extends Controller
{
    /**
     * API: Get Available Time Slots for Lab Test Booking
     * GET /api/lab-test-bookings/available-slots
     */
    public function getAvailableSlots(Request $request)
    {
        $allSlots = [
            ['slot' => '07:00 AM - 08:00 AM', 'period' => 'Morning (Fasting)', 'start' => '07:00'],
            ['slot' => '08:00 AM - 09:00 AM', 'period' => 'Morning (Fasting)', 'start' => '08:00'],
            ['slot' => '09:00 AM - 10:00 AM', 'period' => 'Morning', 'start' => '09:00'],
            ['slot' => '10:00 AM - 11:00 AM', 'period' => 'Morning', 'start' => '10:00'],
            ['slot' => '11:00 AM - 12:00 PM', 'period' => 'Midday', 'start' => '11:00'],
            ['slot' => '12:00 PM - 01:00 PM', 'period' => 'Afternoon', 'start' => '12:00'],
            ['slot' => '02:00 PM - 03:00 PM', 'period' => 'Afternoon', 'start' => '14:00'],
            ['slot' => '03:00 PM - 04:00 PM', 'period' => 'Afternoon', 'start' => '15:00'],
            ['slot' => '04:00 PM - 05:00 PM', 'period' => 'Evening', 'start' => '16:00'],
            ['slot' => '05:00 PM - 06:00 PM', 'period' => 'Evening', 'start' => '17:00'],
            ['slot' => '06:00 PM - 07:00 PM', 'period' => 'Evening', 'start' => '18:00'],
        ];

        $dateParam = $request->input('date');
        $availableSlots = [];
        $isToday = false;

        if ($dateParam && Carbon::hasFormat($dateParam, 'Y-m-d')) {
            $bookingDate = Carbon::parse($dateParam)->startOfDay();
            $today = Carbon::today();

            if ($bookingDate->isPast() && !$bookingDate->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot fetch time slots for past dates. Please select today or a future date.',
                    'data'    => [],
                ], 422);
            }

            if ($bookingDate->isToday()) {
                $isToday = true;
                $currentHour = (int)Carbon::now()->format('H');
                foreach ($allSlots as $item) {
                    $slotStartHour = (int)explode(':', $item['start'])[0];
                    if ($slotStartHour > $currentHour) {
                        $availableSlots[] = $item['slot'];
                    }
                }
            } else {
                $availableSlots = array_column($allSlots, 'slot');
            }
        } else {
            $availableSlots = array_column($allSlots, 'slot');
        }

        return response()->json([
            'success'          => true,
            'message'          => 'Available booking time slots fetched successfully',
            'date'             => $dateParam ?? Carbon::today()->format('Y-m-d'),
            'is_today'         => $isToday,
            'total_available'  => count($availableSlots),
            'data'             => $availableSlots,
            'all_slots_detail' => $allSlots,
        ], 200);
    }

    /**
     * API 10: Create a New Lab Test Booking
     * POST /api/lab-test-bookings
     */
    public function createBooking(Request $request)
    {
        // Accept either lab_test_id or test_id
        if (!$request->has('lab_test_id') && $request->has('test_id')) {
            $request->merge(['lab_test_id' => $request->input('test_id')]);
        }

        // Accept customer_name or name
        if (!$request->has('customer_name') && $request->has('name')) {
            $request->merge(['customer_name' => $request->input('name')]);
        }

        // Accept customer_phone or phone or mobile
        if (!$request->has('customer_phone')) {
            $phone = $request->input('phone') ?? $request->input('mobile');
            if ($phone) {
                $request->merge(['customer_phone' => $phone]);
            }
        }

        // Accept customer_email or email
        if (!$request->has('customer_email') && $request->has('email')) {
            $request->merge(['customer_email' => $request->input('email')]);
        }

        // Accept pincode or pin_code or postal_code
        if (!$request->has('pincode')) {
            $pin = $request->input('pin_code') ?? $request->input('postal_code') ?? $request->input('pin');
            if ($pin) {
                $request->merge(['pincode' => $pin]);
            }
        }

        $validator = Validator::make($request->all(), [
            'lab_test_id'             => 'required|integer|exists:lab_tests,id',
            'customer_name'           => 'required|string|min:2|max:255',
            'customer_phone'          => 'required|string|min:8|max:20',
            'customer_email'          => 'nullable|email|max:191',
            'address'                 => 'required|string|min:3',
            'city'                    => 'required|string|max:100',
            'state'                   => 'required|string|max:100',
            'pincode'                 => 'required|string|max:20',
            'booking_date'            => 'required|date|after_or_equal:today',
            'booking_time'            => 'required|string|max:100',
            'sample_collection_type'  => 'nullable|string|in:home_collection,lab_visit',
            'payment_method'          => 'nullable|string|max:50',
            'additional_instructions' => 'nullable|string|max:1000',
        ], [
            'lab_test_id.required'     => 'Please select a lab test to book.',
            'lab_test_id.exists'       => 'Selected lab test does not exist in our catalog.',
            'customer_name.required'   => 'Customer / Patient full name is required.',
            'customer_phone.required'  => 'Valid mobile contact number is required.',
            'address.required'         => 'Complete address for sample collection is required.',
            'city.required'            => 'City is required.',
            'state.required'           => 'State is required.',
            'pincode.required'         => 'PIN Code is required.',
            'booking_date.required'    => 'Please select a preferred booking date.',
            'booking_date.after_or_equal' => 'Booking date cannot be in the past. Please select today or a future date.',
            'booking_time.required'    => 'Please select an available appointment time slot.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Verify that the lab test is active and its lab is active
        $test = LabTest::with(['lab', 'category'])
            ->where('id', $request->lab_test_id)
            ->where('status', 'active')
            ->first();

        if (!$test) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'The selected lab test is currently unavailable or inactive.',
            ], 404);
        }

        if (!$test->lab || $test->lab->status !== 'active') {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'The laboratory offering this test is currently inactive.',
            ], 400);
        }

        // Validate booking slot if booking for today
        $bookingDate = Carbon::parse($request->booking_date)->startOfDay();
        if ($bookingDate->isToday()) {
            $currentHour = (int)Carbon::now()->format('H');
            preg_match('/(\d{1,2}):\d{2}\s*(AM|PM)/i', $request->booking_time, $matches);
            if (!empty($matches)) {
                $slotHour = (int)$matches[1];
                $ampm = strtoupper($matches[2]);
                if ($ampm === 'PM' && $slotHour < 12) {
                    $slotHour += 12;
                } elseif ($ampm === 'AM' && $slotHour === 12) {
                    $slotHour = 0;
                }
                if ($slotHour <= $currentHour) {
                    return response()->json([
                        'success' => false,
                        'status'  => false,
                        'message' => 'The selected time slot has already passed for today. Please select a later time slot or another date.',
                    ], 422);
                }
            }
        }

        // Determine effective price at time of booking
        $price = $test->discount_price !== null && (float)$test->discount_price > 0
            ? (float)$test->discount_price
            : (float)$test->price;

        // Resolve authenticated user ID
        $userId = auth('sanctum')->id() ?? auth()->id() ?? ($request->user_id ? (int)$request->user_id : null);

        // Generate unique booking ID: LB-YYYYMMDD-XXXXX
        $uniqueSuffix = strtoupper(Str::random(5));
        $bookingId = 'LB-' . date('Ymd') . '-' . $uniqueSuffix;

        try {
            $booking = DB::transaction(function () use ($request, $test, $price, $userId, $bookingId) {
                // 1. Create main booking
                $newBooking = LabTestBooking::create([
                    'booking_id'              => $bookingId,
                    'user_id'                 => $userId,
                    'lab_test_id'             => $test->id,
                    'lab_id'                  => $test->lab_id,
                    'customer_name'           => trim($request->customer_name),
                    'customer_phone'          => trim($request->customer_phone),
                    'customer_email'          => $request->customer_email ? trim($request->customer_email) : null,
                    'address'                 => trim($request->address),
                    'city'                    => trim($request->city),
                    'state'                   => trim($request->state),
                    'pincode'                 => trim($request->pincode),
                    'booking_date'            => $request->booking_date,
                    'booking_time'            => trim($request->booking_time),
                    'total_amount'            => $price,
                    'status'                  => 'Pending',
                    'payment_status'          => 'Pending',
                    'payment_method'          => $request->payment_method ?? 'Cash on Collection',
                    'sample_collection_type'  => $request->sample_collection_type ?? 'home_collection',
                    'additional_instructions' => $request->additional_instructions ? trim($request->additional_instructions) : null,
                ]);

                // 2. Create booking item snapshot
                LabTestBookingItem::create([
                    'booking_id'  => $newBooking->id,
                    'lab_test_id' => $test->id,
                    'lab_id'      => $test->lab_id,
                    'test_name'   => $test->test_name,
                    'test_code'   => $test->test_code,
                    'lab_name'    => $test->lab->name ?? 'Diagnostic Lab',
                    'price'       => $price,
                ]);

                // 3. Mirror into lab_test_orders table for compatibility
                if (Schema::hasTable('lab_test_orders')) {
                    LabTestOrder::create([
                        'order_id'               => $bookingId,
                        'user_id'                => $userId,
                        'lab_test_id'            => $test->id,
                        'lab_id'                 => $test->lab_id,
                        'patient_name'           => trim($request->customer_name),
                        'patient_phone'          => trim($request->customer_phone),
                        'sample_collection_type' => $request->sample_collection_type ?? 'home_collection',
                        'collection_address'     => trim($request->address) . ', ' . trim($request->city) . ', ' . trim($request->state) . ' - ' . trim($request->pincode),
                        'appointment_date'       => $request->booking_date,
                        'appointment_time'       => trim($request->booking_time),
                        'amount'                 => $price,
                        'payment_status'         => 'pending',
                        'payment_method'         => $request->payment_method ?? 'Cash on Collection',
                        'status'                 => 'pending',
                        'notes'                  => $request->additional_instructions,
                    ]);
                }

                return $newBooking;
            });

            // Format full customer address
            $fullAddress = implode(', ', array_filter([
                $booking->address,
                $booking->city,
                $booking->state,
                $booking->pincode ? "PIN: {$booking->pincode}" : null,
            ]));

            // Build confirmation response payload matching Section 4
            $confirmationData = [
                'booking_id'             => (string)$booking->booking_id,
                'id'                     => (int)$booking->id,
                'test_id'                => (int)$test->id,
                'test_name'              => (string)$test->test_name,
                'test_code'              => (string)($test->test_code ?? ''),
                'lab_id'                 => (int)$test->lab_id,
                'lab_name'               => (string)($test->lab->name ?? ''),
                'category_name'          => (string)($test->category->name ?? ''),
                'price'                  => (float)$booking->total_amount,
                'customer_name'          => (string)$booking->customer_name,
                'customer_phone'         => (string)$booking->customer_phone,
                'customer_email'         => (string)($booking->customer_email ?? ''),
                'customer_address'       => (string)$fullAddress,
                'address_details'        => [
                    'address' => $booking->address,
                    'city'    => $booking->city,
                    'state'   => $booking->state,
                    'pincode' => $booking->pincode,
                ],
                'booking_date'           => (string)$booking->booking_date->format('Y-m-d'),
                'booking_date_formatted' => (string)$booking->booking_date->format('d M, Y'),
                'booking_time'           => (string)$booking->booking_time,
                'booking_status'         => (string)$booking->status,
                'status'                 => (string)$booking->status,
                'sample_collection_type' => (string)$booking->sample_collection_type,
                'payment_status'         => (string)$booking->payment_status,
                'payment_method'         => (string)$booking->payment_method,
                'additional_instructions'=> (string)($booking->additional_instructions ?? ''),
                'preparation_instructions'=> (string)($test->preparation_instructions ?? ''),
                'sample_type'            => (string)($test->sample_type ?? ''),
                'report_delivery_time'   => (string)($test->report_delivery_time ?? ''),
                'created_at'             => (string)$booking->created_at->format('Y-m-d H:i:s'),
            ];

            return response()->json([
                'success' => true,
                'status'  => true,
                'message' => 'Lab test booking confirmed successfully!',
                'data'    => $confirmationData,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Failed to process lab test booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 11: Get Customer's Own Lab Test Bookings (Booking History)
     * GET /api/my-lab-test-bookings
     */
    public function getMyBookings(Request $request)
    {
        $userId = auth('sanctum')->id() ?? auth()->id() ?? ($request->user_id ? (int)$request->user_id : null);

        if (!$userId) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Unauthenticated. Please login to view your lab test bookings.',
                'data'    => [],
            ], 401);
        }

        $query = LabTestBooking::where('user_id', $userId)
            ->with(['labTest.category', 'lab', 'items']);

        // Optional status filter: ?status=Pending
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = max(1, min(50, (int)$request->input('per_page', 15)));
        $paginated = $query->latest('id')->paginate($perPage);

        $formatted = collect($paginated->items())->map(function ($b) {
            $test = $b->labTest;
            $lab = $b->lab;

            return [
                'id'                     => (int)$b->id,
                'booking_id'             => (string)$b->booking_id,
                'test_id'                => (int)($b->lab_test_id ?? 0),
                'test_name'              => (string)($test->test_name ?? ($b->items->first()->test_name ?? 'Diagnostic Test')),
                'test_code'              => (string)($test->test_code ?? ''),
                'category_name'          => (string)($test->category->name ?? ''),
                'lab_id'                 => (int)($b->lab_id ?? 0),
                'lab_name'               => (string)($lab->name ?? ($b->items->first()->lab_name ?? 'Diagnostic Lab')),
                'price'                  => (float)$b->total_amount,
                'customer_name'          => (string)$b->customer_name,
                'customer_phone'         => (string)$b->customer_phone,
                'customer_email'         => (string)($b->customer_email ?? ''),
                'customer_address'       => (string)$b->full_address,
                'booking_date'           => (string)$b->booking_date->format('Y-m-d'),
                'booking_date_formatted' => (string)$b->booking_date->format('d M, Y'),
                'booking_time'           => (string)$b->booking_time,
                'booking_status'         => (string)$b->status,
                'status'                 => (string)$b->status,
                'status_badge'           => $b->status_badge,
                'sample_collection_type' => (string)$b->sample_collection_type,
                'payment_status'         => (string)$b->payment_status,
                'report_file'            => $b->report_file ? asset($b->report_file) : null,
                'created_at'             => (string)$b->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success'    => true,
            'status'     => true,
            'message'    => 'Customer lab test bookings fetched successfully',
            'data'       => $formatted,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ], 200);
    }

    /**
     * API 12: Get Single Lab Test Booking Details
     * GET /api/lab-test-bookings/{id}
     */
    public function getBookingDetail(Request $request, $id)
    {
        $userId = auth('sanctum')->id() ?? auth()->id() ?? ($request->user_id ? (int)$request->user_id : null);

        // Find by integer ID or string booking_id (e.g. LB-20261121-ABC12)
        $booking = LabTestBooking::with(['labTest.category', 'lab', 'items'])
            ->where(function ($q) use ($id) {
                if (is_numeric($id)) {
                    $q->where('id', $id)->orWhere('booking_id', $id);
                } else {
                    $q->where('booking_id', $id);
                }
            })
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Lab test booking not found',
            ], 404);
        }

        // Authorization: if a user is authenticated, ensure they own the booking (or are admin)
        if ($userId && $booking->user_id && (int)$booking->user_id !== (int)$userId) {
            $user = User::find($userId);
            if (!$user || !in_array($user->role ?? '', ['admin', 'superadmin'])) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Unauthorized. You do not have permission to view this booking.',
                ], 403);
            }
        }

        $test = $booking->labTest;
        $lab = $booking->lab;

        $payload = [
            'id'                     => (int)$booking->id,
            'booking_id'             => (string)$booking->booking_id,
            'test_id'                => (int)($booking->lab_test_id ?? 0),
            'test_name'              => (string)($test->test_name ?? ($booking->items->first()->test_name ?? 'Diagnostic Test')),
            'test_code'              => (string)($test->test_code ?? ''),
            'category_name'          => (string)($test->category->name ?? ''),
            'lab_id'                 => (int)($booking->lab_id ?? 0),
            'lab_name'               => (string)($lab->name ?? ($booking->items->first()->lab_name ?? 'Diagnostic Lab')),
            'lab_contact'            => (string)($lab->contact_number ?? ''),
            'lab_address'            => (string)($lab->address ?? ''),
            'price'                  => (float)$booking->total_amount,
            'customer_name'          => (string)$booking->customer_name,
            'customer_phone'         => (string)$booking->customer_phone,
            'customer_email'         => (string)($booking->customer_email ?? ''),
            'customer_address'       => (string)$booking->full_address,
            'address_details'        => [
                'address' => $booking->address,
                'city'    => $booking->city,
                'state'   => $booking->state,
                'pincode' => $booking->pincode,
            ],
            'booking_date'           => (string)$booking->booking_date->format('Y-m-d'),
            'booking_date_formatted' => (string)$booking->booking_date->format('d M, Y'),
            'booking_time'           => (string)$booking->booking_time,
            'booking_status'         => (string)$booking->status,
            'status'                 => (string)$booking->status,
            'status_badge'           => $booking->status_badge,
            'sample_collection_type' => (string)$booking->sample_collection_type,
            'payment_status'         => (string)$booking->payment_status,
            'payment_method'         => (string)$booking->payment_method,
            'additional_instructions'=> (string)($booking->additional_instructions ?? ''),
            'preparation_instructions'=> (string)($test->preparation_instructions ?? ''),
            'sample_type'            => (string)($test->sample_type ?? ''),
            'report_delivery_time'   => (string)($test->report_delivery_time ?? ''),
            'report_file'            => $booking->report_file ? asset($booking->report_file) : null,
            'items'                  => $booking->items->map(function ($item) {
                return [
                    'id'        => (int)$item->id,
                    'test_id'   => (int)$item->lab_test_id,
                    'test_name' => (string)$item->test_name,
                    'test_code' => (string)($item->test_code ?? ''),
                    'lab_name'  => (string)$item->lab_name,
                    'price'     => (float)$item->price,
                ];
            }),
            'created_at'             => (string)$booking->created_at->format('Y-m-d H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'status'  => true,
            'message' => 'Lab test booking details fetched successfully',
            'data'    => $payload,
        ], 200);
    }

    /**
     * API 13: Customer Cancel Lab Test Booking
     * POST /api/lab-test-bookings/{id}/cancel
     */
    public function cancelBooking(Request $request, $id)
    {
        $userId = auth('sanctum')->id() ?? auth()->id() ?? ($request->user_id ? (int)$request->user_id : null);

        $booking = LabTestBooking::where(function ($q) use ($id) {
            if (is_numeric($id)) {
                $q->where('id', $id)->orWhere('booking_id', $id);
            } else {
                $q->where('booking_id', $id);
            }
        })->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Lab test booking not found',
            ], 404);
        }

        if ($userId && $booking->user_id && (int)$booking->user_id !== (int)$userId) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Unauthorized. You cannot cancel another customer\'s booking.',
            ], 403);
        }

        if (in_array($booking->status, ['Sample Collected', 'Completed'])) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => "Cannot cancel booking. Current status is already '{$booking->status}'.",
            ], 400);
        }

        if ($booking->status === 'Cancelled') {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'This booking is already cancelled.',
            ], 400);
        }

        $booking->status = 'Cancelled';
        $booking->cancellation_reason = $request->input('reason', 'Cancelled by customer');
        $booking->save();

        // Also update mirrored record in lab_test_orders if present
        if (Schema::hasTable('lab_test_orders')) {
            LabTestOrder::where('order_id', $booking->booking_id)->update(['status' => 'cancelled']);
        }

        return response()->json([
            'success' => true,
            'status'  => true,
            'message' => 'Lab test booking cancelled successfully',
            'data'    => [
                'booking_id' => $booking->booking_id,
                'status'     => 'Cancelled',
            ],
        ], 200);
    }
}
