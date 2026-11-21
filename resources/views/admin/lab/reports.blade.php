@extends('admin.layouts.layout')

@section('content')
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-weight:700;font-size:24px;color:#111827;margin:0 0 6px 0;">
                <i class="fa-solid fa-file-invoice-dollar" style="color:#d4155b;margin-right:8px;"></i>Lab Test Booking Reports &amp; Analytics
            </h1>
            <p style="color:#6b7280;font-size:14px;margin:0;">
                Periodic performance metrics, completion rates, partner lab revenue, and top booked diagnostics.
            </p>
        </div>
        
        <div style="display:flex;gap:10px;align-items:center;">
            <button onclick="window.print()" style="background:#fff;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;border:1px solid #d1d5db;cursor:pointer;">
                <i class="fa-solid fa-print"></i> Print Report
            </button>
            <a href="{{ route('admin.lab.bookings.index') }}" style="background:#d4155b;color:#fff;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-list-check"></i> All Bookings
            </a>
        </div>
    </div>

    <!-- DATE RANGE FILTER BAR -->
    <div style="background:#fff;padding:18px 20px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('admin.lab.bookings.reports') }}" method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;margin-right:4px;">Period:</span>
                
                <a href="{{ route('admin.lab.bookings.reports', ['range' => 'today']) }}" style="padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;border:1px solid {{ $range === 'today' ? '#d4155b' : '#d1d5db' }};background:{{ $range === 'today' ? '#d4155b' : '#fff' }};color:{{ $range === 'today' ? '#fff' : '#374151' }};">
                    Today
                </a>
                
                <a href="{{ route('admin.lab.bookings.reports', ['range' => 'this_week']) }}" style="padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;border:1px solid {{ $range === 'this_week' ? '#d4155b' : '#d1d5db' }};background:{{ $range === 'this_week' ? '#d4155b' : '#fff' }};color:{{ $range === 'this_week' ? '#fff' : '#374151' }};">
                    This Week
                </a>

                <a href="{{ route('admin.lab.bookings.reports', ['range' => 'this_month']) }}" style="padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;border:1px solid {{ $range === 'this_month' ? '#d4155b' : '#d1d5db' }};background:{{ $range === 'this_month' ? '#d4155b' : '#fff' }};color:{{ $range === 'this_month' ? '#fff' : '#374151' }};">
                    This Month
                </a>

                <a href="{{ route('admin.lab.bookings.reports', ['range' => 'last_30_days']) }}" style="padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;border:1px solid {{ $range === 'last_30_days' ? '#d4155b' : '#d1d5db' }};background:{{ $range === 'last_30_days' ? '#d4155b' : '#fff' }};color:{{ $range === 'last_30_days' ? '#fff' : '#374151' }};">
                    Last 30 Days
                </a>
            </div>

            <!-- Custom Date Selector -->
            <div style="display:flex;gap:8px;align-items:center;">
                <input type="hidden" name="range" value="custom">
                <input type="date" name="from_date" value="{{ request('from_date', $fromDate->format('Y-m-d')) }}" style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:12px;outline:none;">
                <span style="color:#9ca3af;font-size:12px;">to</span>
                <input type="date" name="to_date" value="{{ request('to_date', $toDate->format('Y-m-d')) }}" style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:12px;outline:none;">
                <button type="submit" style="padding:8px 14px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">
                    Apply
                </button>
            </div>
        </form>
    </div>

    <!-- REPORT SUMMARY METRICS -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
        
        <!-- Total Bookings in Range -->
        <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;">Bookings in Period</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:#111827;margin-top:8px;">{{ number_format($totalCount) }}</div>
            <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ $rangeLabel }}</div>
        </div>

        <!-- Total Revenue -->
        <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#047857;text-transform:uppercase;">Revenue Realized</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:#059669;margin-top:8px;">₹{{ number_format($totalRevenue, 2) }}</div>
            <div style="font-size:12px;color:#10b981;margin-top:4px;">Confirmed &amp; Delivered tests</div>
        </div>

        <!-- Completion Rate -->
        <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#1e40af;text-transform:uppercase;">Completion Rate</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:#2563eb;margin-top:8px;">{{ $completionRate }}%</div>
            <div style="font-size:12px;color:#3b82f6;margin-top:4px;">{{ $completedCount }} tests delivered</div>
        </div>

        <!-- Cancellation Rate -->
        <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#b91c1c;text-transform:uppercase;">Cancellation Rate</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:#dc2626;margin-top:8px;">{{ $cancellationRate }}%</div>
            <div style="font-size:12px;color:#ef4444;margin-top:4px;">{{ $cancelledCount }} cancelled bookings</div>
        </div>
    </div>

    <!-- STATUS DISTRIBUTION BREAKDOWN -->
    <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#111827;margin:0 0 16px 0;">
            <i class="fa-solid fa-chart-pie" style="color:#2563eb;margin-right:6px;"></i> Booking Status Distribution
        </h3>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;text-align:center;">
            <div style="background:#fef3c7;padding:14px;border-radius:10px;border:1px solid #fde68a;">
                <div style="font-size:12px;font-weight:700;color:#92400e;">Pending</div>
                <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:#b45309;margin-top:4px;">{{ $pendingCount }}</div>
            </div>

            <div style="background:#e0e7ff;padding:14px;border-radius:10px;border:1px solid #c7d2fe;">
                <div style="font-size:12px;font-weight:700;color:#3730a3;">Confirmed</div>
                <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:#4338ca;margin-top:4px;">{{ $confirmedCount }}</div>
            </div>

            <div style="background:#dcfce7;padding:14px;border-radius:10px;border:1px solid #bbf7d0;">
                <div style="font-size:12px;font-weight:700;color:#166534;">Completed</div>
                <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:#15803d;margin-top:4px;">{{ $completedCount }}</div>
            </div>

            <div style="background:#fee2e2;padding:14px;border-radius:10px;border:1px solid #fecaca;">
                <div style="font-size:12px;font-weight:700;color:#991b1b;">Cancelled</div>
                <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:#b91c1c;margin-top:4px;">{{ $cancelledCount }}</div>
            </div>
        </div>
    </div>

    <!-- PARTNER LAB PERFORMANCE & TOP BOOKED TESTS -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">
        
        <!-- LAB PARTNERS PERFORMANCE -->
        <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#111827;margin:0 0 14px 0;">
                <i class="fa-solid fa-hospital" style="color:#d4155b;margin-right:6px;"></i> Partner Laboratory Breakdown
            </h3>

            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;text-align:left;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;color:#6b7280;">
                            <th style="padding:10px;font-weight:600;">Lab Name</th>
                            <th style="padding:10px;font-weight:600;text-align:center;">Bookings</th>
                            <th style="padding:10px;font-weight:600;text-align:right;">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($labPerformance as $lab)
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:10px;">
                                <div style="font-weight:700;color:#111827;">{{ $lab->name }}</div>
                                <div style="font-size:11px;color:#6b7280;">{{ $lab->city }}</div>
                            </td>
                            <td style="padding:10px;text-align:center;font-weight:700;color:#2563eb;">
                                {{ $lab->bookings_count }}
                            </td>
                            <td style="padding:10px;text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#111827;">
                                ₹{{ number_format($lab->bookings_sum_total_amount ?? 0, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding:20px;text-align:center;color:#9ca3af;">No partner lab activity in period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TOP BOOKED TESTS -->
        <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#111827;margin:0 0 14px 0;">
                <i class="fa-solid fa-fire" style="color:#f59e0b;margin-right:6px;"></i> Top Booked Diagnostic Tests
            </h3>

            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;text-align:left;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;color:#6b7280;">
                            <th style="padding:10px;font-weight:600;">Test Name</th>
                            <th style="padding:10px;font-weight:600;text-align:center;">Bookings</th>
                            <th style="padding:10px;font-weight:600;text-align:right;">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($testBreakdown as $t)
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:10px;">
                                <div style="font-weight:700;color:#111827;">{{ $t->test_name }}</div>
                                <div style="font-size:11px;color:#d4155b;">{{ $t->lab->name ?? 'Diagnostic Lab' }}</div>
                            </td>
                            <td style="padding:10px;text-align:center;font-weight:700;color:#2563eb;">
                                {{ $t->bookings_count }}
                            </td>
                            <td style="padding:10px;text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#111827;">
                                ₹{{ number_format($t->bookings_sum_total_amount ?? 0, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding:20px;text-align:center;color:#9ca3af;">No test bookings in period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</main>
@endsection
