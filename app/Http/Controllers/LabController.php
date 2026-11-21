<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use App\Models\LabTestOrder;
use App\Models\LabTestBooking;
use App\Models\LabTestBookingItem;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LabController extends Controller
{
    /**
     * Lab Test Bookings Dashboard: Summary cards, today's appointments, recent bookings.
     */
    public function dashboard()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Summary KPI cards requested
        $stats = [
            'total'             => LabTestBooking::count(),
            'pending'           => LabTestBooking::where('status', 'Pending')->count(),
            'confirmed'         => LabTestBooking::where('status', 'Confirmed')->count(),
            'scheduled'         => LabTestBooking::where('status', 'Sample Collection Scheduled')->count(),
            'sample_collected'  => LabTestBooking::where('status', 'Sample Collected')->count(),
            'completed'         => LabTestBooking::where('status', 'Completed')->count(),
            'cancelled'         => LabTestBooking::where('status', 'Cancelled')->count(),
            'today_count'       => LabTestBooking::whereDate('booking_date', $today)->count(),
            'revenue'           => (float)LabTestBooking::whereIn('status', ['Confirmed', 'Sample Collected', 'Completed'])->sum('total_amount'),
        ];

        // Today's scheduled appointments
        $todayAppointments = LabTestBooking::with(['labTest', 'lab'])
            ->whereDate('booking_date', $today)
            ->where('status', '!=', 'Cancelled')
            ->orderBy('booking_time')
            ->get();

        // Recent 8 bookings across all dates
        $recentBookings = LabTestBooking::with(['labTest.category', 'lab'])
            ->latest('id')
            ->take(8)
            ->get();

        // Upcoming appointments (next 7 days)
        $upcomingBookings = LabTestBooking::with(['labTest', 'lab'])
            ->whereDate('booking_date', '>', $today)
            ->where('status', '!=', 'Cancelled')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->take(6)
            ->get();

        return view('admin.lab.dashboard', compact(
            'stats',
            'todayAppointments',
            'recentBookings',
            'upcomingBookings'
        ));
    }

    /**
     * All Bookings Management: Search, filters, data table, and quick actions.
     */
    public function allBookings(Request $request)
    {
        $query = LabTestBooking::with(['labTest.category', 'lab', 'items', 'user']);

        // Search by Booking ID, Customer Name, Mobile Number, Email, Test Name, Lab Name
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->search($search);
        }

        // Filter by Status: Pending, Confirmed, Sample Collection Scheduled, Sample Collected, Completed, Cancelled
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by specific Date
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        // Filter by Lab Partner
        if ($request->filled('lab_id')) {
            $query->where('lab_id', $request->lab_id);
        }

        $bookings = $query->latest('id')->paginate(15)->withQueryString();

        // Dashboard Summary KPIs
        $stats = [
            'total'             => LabTestBooking::count(),
            'pending'           => LabTestBooking::where('status', 'Pending')->count(),
            'confirmed'         => LabTestBooking::where('status', 'Confirmed')->count(),
            'scheduled'         => LabTestBooking::where('status', 'Sample Collection Scheduled')->count(),
            'sample_collected'  => LabTestBooking::where('status', 'Sample Collected')->count(),
            'completed'         => LabTestBooking::where('status', 'Completed')->count(),
            'cancelled'         => LabTestBooking::where('status', 'Cancelled')->count(),
            'revenue'           => (float)LabTestBooking::whereIn('status', ['Confirmed', 'Sample Collected', 'Completed'])->sum('total_amount'),
        ];

        $statuses = [
            'Pending',
            'Confirmed',
            'Sample Collection Scheduled',
            'Sample Collected',
            'Completed',
            'Cancelled'
        ];

        $labs = Lab::where('status', 'active')->orderBy('name')->get();

        return view('admin.lab.all_bookings', compact('bookings', 'stats', 'statuses', 'labs'));
    }

    /**
     * Alias for allBookings to maintain backward compatibility with any /lab/orders references.
     */
    public function orders(Request $request)
    {
        return $this->allBookings($request);
    }

    /**
     * Dedicated Booking Details Page.
     */
    public function showBooking($id)
    {
        $booking = LabTestBooking::with(['labTest.category', 'lab', 'items', 'user'])
            ->where('id', $id)
            ->orWhere('booking_id', $id)
            ->firstOrFail();

        $statuses = [
            'Pending',
            'Confirmed',
            'Sample Collection Scheduled',
            'Sample Collected',
            'Completed',
            'Cancelled'
        ];

        return view('admin.lab.booking_details', compact('booking', 'statuses'));
    }

    /**
     * Quick Action: Confirm Booking.
     */
    public function confirmBooking($id)
    {
        $booking = LabTestBooking::findOrFail($id);
        $booking->status = 'Confirmed';
        $booking->save();

        if (Schema::hasTable('lab_test_orders')) {
            LabTestOrder::where('order_id', $booking->booking_id)->update(['status' => 'confirmed']);
        }

        return redirect()->back()->with('success', "Booking #{$booking->booking_id} has been Confirmed successfully!");
    }

    /**
     * Quick Action: Cancel Booking.
     */
    public function quickCancelBooking(Request $request, $id)
    {
        $booking = LabTestBooking::findOrFail($id);
        $booking->status = 'Cancelled';
        $booking->cancellation_reason = $request->input('reason', 'Cancelled by admin');
        $booking->save();

        if (Schema::hasTable('lab_test_orders')) {
            LabTestOrder::where('order_id', $booking->booking_id)->update(['status' => 'cancelled']);
        }

        return redirect()->back()->with('success', "Booking #{$booking->booking_id} has been Cancelled.");
    }

    /**
     * Update Booking Status, Payment Status, and Attach Lab Report.
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status'              => 'required|in:Pending,Confirmed,Sample Collection Scheduled,Sample Collected,Completed,Cancelled',
            'cancellation_reason' => 'nullable|string|max:500',
            'report_file'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            $booking = LabTestBooking::findOrFail($id);
            $booking->status = $request->status;

            if ($request->filled('cancellation_reason')) {
                $booking->cancellation_reason = $request->cancellation_reason;
            }

            if ($request->filled('payment_status')) {
                $booking->payment_status = $request->payment_status;
            }

            if ($request->hasFile('report_file')) {
                $file = $request->file('report_file');
                $filename = 'report_' . $booking->booking_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/reports');
                if (!File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true, true);
                }
                $file->move($destination, $filename);
                $booking->report_file = 'uploads/reports/' . $filename;
            }

            $booking->save();

            // Mirror status into lab_test_orders table if present
            if (Schema::hasTable('lab_test_orders')) {
                $mappedStatus = strtolower(str_replace(' ', '_', $request->status));
                LabTestOrder::where('order_id', $booking->booking_id)->update([
                    'status'         => $mappedStatus,
                    'payment_status' => strtolower($booking->payment_status),
                    'report_file'    => $booking->report_file,
                ]);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Booking status updated to '{$request->status}' successfully!",
                    'data'    => $booking,
                ]);
            }

            return redirect()->back()->with('success', "Booking #{$booking->booking_id} status updated to '{$request->status}' successfully!");

        } catch (Exception $e) {
            Log::error('Update booking status error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error updating booking: ' . $e->getMessage());
        }
    }

    /**
     * Booking Calendar View: Monthly calendar grid with time slot breakdown.
     */
    public function calendar(Request $request)
    {
        $year = (int)$request->input('year', Carbon::now()->year);
        $month = (int)$request->input('month', Carbon::now()->month);

        $selectedDate = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = $selectedDate->copy()->startOfMonth();
        $endOfMonth = $selectedDate->copy()->endOfMonth();

        // Fetch all bookings for the month
        $monthBookings = LabTestBooking::with(['labTest', 'lab'])
            ->whereDate('booking_date', '>=', $startOfMonth)
            ->whereDate('booking_date', '<=', $endOfMonth)
            ->get();

        // Group by date string (Y-m-d)
        $bookingsByDate = [];
        foreach ($monthBookings as $b) {
            $dateStr = $b->booking_date->format('Y-m-d');
            if (!isset($bookingsByDate[$dateStr])) {
                $bookingsByDate[$dateStr] = [
                    'count'     => 0,
                    'pending'   => 0,
                    'confirmed' => 0,
                    'completed' => 0,
                    'bookings'  => [],
                ];
            }
            $bookingsByDate[$dateStr]['count']++;
            if ($b->status === 'Pending') $bookingsByDate[$dateStr]['pending']++;
            if (in_array($b->status, ['Confirmed', 'Sample Collection Scheduled', 'Sample Collected'])) $bookingsByDate[$dateStr]['confirmed']++;
            if ($b->status === 'Completed') $bookingsByDate[$dateStr]['completed']++;
            $bookingsByDate[$dateStr]['bookings'][] = $b;
        }

        // Active selected day for details (defaults to today if in this month, else 1st of month)
        $activeDay = $request->input('day', (Carbon::now()->month === $month && Carbon::now()->year === $year) ? Carbon::today()->format('Y-m-d') : $startOfMonth->format('Y-m-d'));
        
        $dayBookings = LabTestBooking::with(['labTest', 'lab'])
            ->whereDate('booking_date', $activeDay)
            ->orderBy('booking_time')
            ->get();

        return view('admin.lab.calendar', compact(
            'year',
            'month',
            'selectedDate',
            'startOfMonth',
            'endOfMonth',
            'bookingsByDate',
            'activeDay',
            'dayBookings'
        ));
    }

    /**
     * Booking Reports: Detailed analytics, revenue, status breakdown, lab partner performance.
     */
    public function reports(Request $request)
    {
        $range = $request->input('range', 'this_month');
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $fromDate = $now->copy()->startOfDay();
                $toDate = $now->copy()->endOfDay();
                $rangeLabel = "Today (" . $now->format('d M, Y') . ")";
                break;
            case 'this_week':
                $fromDate = $now->copy()->startOfWeek();
                $toDate = $now->copy()->endOfWeek();
                $rangeLabel = "This Week (" . $fromDate->format('d M') . " - " . $toDate->format('d M, Y') . ")";
                break;
            case 'last_30_days':
                $fromDate = $now->copy()->subDays(30)->startOfDay();
                $toDate = $now->copy()->endOfDay();
                $rangeLabel = "Last 30 Days";
                break;
            case 'custom':
                $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : $now->copy()->startOfMonth();
                $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : $now->copy()->endOfDay();
                $rangeLabel = "Custom (" . $fromDate->format('d M, Y') . " - " . $toDate->format('d M, Y') . ")";
                break;
            case 'this_month':
            default:
                $fromDate = $now->copy()->startOfMonth();
                $toDate = $now->copy()->endOfMonth();
                $rangeLabel = "This Month (" . $now->format('F Y') . ")";
                break;
        }

        $query = LabTestBooking::whereBetween('booking_date', [$fromDate, $toDate]);

        $totalCount = (clone $query)->count();
        $totalRevenue = (float)(clone $query)->whereIn('status', ['Confirmed', 'Sample Collected', 'Completed'])->sum('total_amount');
        $completedCount = (clone $query)->where('status', 'Completed')->count();
        $pendingCount = (clone $query)->where('status', 'Pending')->count();
        $confirmedCount = (clone $query)->where('status', 'Confirmed')->count();
        $cancelledCount = (clone $query)->where('status', 'Cancelled')->count();

        $completionRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 1) : 0;
        $cancellationRate = $totalCount > 0 ? round(($cancelledCount / $totalCount) * 100, 1) : 0;

        // Breakdown by Partner Lab
        $labPerformance = Lab::withCount(['bookings' => function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('booking_date', [$fromDate, $toDate]);
        }])
        ->withSum(['bookings' => function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('booking_date', [$fromDate, $toDate])
              ->whereIn('status', ['Confirmed', 'Sample Collected', 'Completed']);
        }], 'total_amount')
        ->orderByDesc('bookings_count')
        ->get();

        // Breakdown by Top Booked Tests
        $testBreakdown = LabTest::withCount(['bookings' => function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('booking_date', [$fromDate, $toDate]);
        }])
        ->withSum(['bookings' => function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('booking_date', [$fromDate, $toDate])
              ->whereIn('status', ['Confirmed', 'Sample Collected', 'Completed']);
        }], 'total_amount')
        ->with('lab')
        ->orderByDesc('bookings_count')
        ->take(10)
        ->get();

        return view('admin.lab.reports', compact(
            'range',
            'rangeLabel',
            'fromDate',
            'toDate',
            'totalCount',
            'totalRevenue',
            'completedCount',
            'pendingCount',
            'confirmedCount',
            'cancelledCount',
            'completionRate',
            'cancellationRate',
            'labPerformance',
            'testBreakdown'
        ));
    }

    /**
     * Get Complete Booking Details JSON for Modal View.
     */
    public function bookingDetails($id)
    {
        $booking = LabTestBooking::with(['labTest.category', 'lab', 'items', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                      => $booking->id,
                'booking_id'              => $booking->booking_id,
                'customer_name'           => $booking->customer_name,
                'customer_phone'          => $booking->customer_phone,
                'customer_email'          => $booking->customer_email,
                'address'                 => $booking->address,
                'city'                    => $booking->city,
                'state'                   => $booking->state,
                'pincode'                 => $booking->pincode,
                'full_address'            => $booking->full_address,
                'booking_date'            => $booking->formatted_date,
                'booking_time'            => $booking->booking_time,
                'total_amount'            => number_format($booking->total_amount, 2),
                'status'                  => $booking->status,
                'status_badge'            => $booking->status_badge,
                'payment_status'          => $booking->payment_status,
                'payment_method'          => $booking->payment_method,
                'sample_collection_type'  => $booking->sample_collection_type,
                'additional_instructions' => $booking->additional_instructions,
                'cancellation_reason'     => $booking->cancellation_reason,
                'report_file_url'         => $booking->report_file ? asset($booking->report_file) : null,
                'test_name'               => $booking->labTest->test_name ?? ($booking->items->first()->test_name ?? 'N/A'),
                'test_code'               => $booking->labTest->test_code ?? '',
                'category_name'           => $booking->labTest->category->name ?? 'General',
                'sample_type'             => $booking->labTest->sample_type ?? 'Blood / Specimen',
                'preparation_instructions'=> $booking->labTest->preparation_instructions ?? 'No special preparation needed.',
                'lab_name'                => $booking->lab->name ?? 'Diagnostic Lab',
                'lab_phone'               => $booking->lab->contact_number ?? '',
                'lab_address'             => $booking->lab->address ?? '',
                'created_at'              => $booking->created_at ? $booking->created_at->format('d M, Y h:i A') : '',
            ]
        ]);
    }

    /**
     * Soft Delete a Booking (Admin).
     */
    public function destroyBooking($id)
    {
        try {
            $booking = LabTestBooking::findOrFail($id);
            $bookingId = $booking->booking_id;
            $booking->delete();

            if (Schema::hasTable('lab_test_orders')) {
                LabTestOrder::where('order_id', $bookingId)->delete();
            }

            return redirect()->back()->with('success', "Booking #{$bookingId} deleted successfully!");
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error deleting booking: ' . $e->getMessage());
        }
    }

    /**
     * Customer Mobile-Friendly Web View: Browse Lab Tests.
     */
    public function customerIndex(Request $request)
    {
        $categories = LabTestCategory::where('status', 'active')->orderBy('name')->get();
        $labs = Lab::where('status', 'active')->orderBy('name')->get();

        $query = LabTest::with(['lab', 'category'])->where('status', 'active')
            ->whereHas('lab', function ($q) {
                $q->where('status', 'active');
            });

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('lab_id')) {
            $query->where('lab_id', $request->lab_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('test_name', 'LIKE', "%{$search}%")
                  ->orWhere('test_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $tests = $query->latest('id')->paginate(12)->withQueryString();

        return view('web.lab_tests', compact('tests', 'categories', 'labs'));
    }

    /**
     * Customer Booking Web Handler (Accepts form post and books test).
     */
    public function customerBook(Request $request)
    {
        // Re-use API logic for uniform validation and creation
        $apiController = app(\App\Http\Controllers\Api\LabApiController::class);
        $response = $apiController->createBooking($request);

        $result = $response->getData(true);

        if ($response->getStatusCode() === 201 && ($result['success'] ?? false)) {
            $bookingId = $result['data']['booking_id'];
            return redirect()->route('customer.lab.confirmation', ['id' => $bookingId])
                ->with('success', 'Your lab test booking has been confirmed successfully!');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $result['message'] ?? 'Validation error during booking.');
    }

    /**
     * Customer Booking Confirmation Screen.
     */
    public function customerConfirmation($id)
    {
        $booking = LabTestBooking::with(['labTest.category', 'lab', 'items'])
            ->where('booking_id', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        return view('web.booking_confirmation', compact('booking'));
    }

    /**
     * Customer Booking History Screen.
     */
    public function customerBookings(Request $request)
    {
        $phone = $request->input('phone') ?? (auth()->user() ? auth()->user()->phone : null);
        $bookings = collect();

        if ($phone || auth()->id()) {
            $query = LabTestBooking::with(['labTest', 'lab']);
            if (auth()->id()) {
                $query->where('user_id', auth()->id());
            } elseif ($phone) {
                $query->where('customer_phone', $phone);
            }
            $bookings = $query->latest('id')->paginate(10);
        }

        return view('web.my_lab_bookings', compact('bookings', 'phone'));
    }
}
