@extends('admin.layouts.layout')

@section('content')
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-weight:700;font-size:24px;color:#111827;margin:0 0 6px 0;">
                <i class="fa-solid fa-chart-pie" style="color:#d4155b;margin-right:8px;"></i>Lab Test Bookings Dashboard
            </h1>
            <p style="color:#6b7280;font-size:14px;margin:0;">
                Real-time operational overview of customer test bookings, appointments, and diagnostic workflows.
            </p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <a href="{{ route('admin.lab.bookings.calendar') }}" style="background:#fff;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid #d1d5db;box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                <i class="fa-solid fa-calendar-days" style="color:#2563eb;"></i> Booking Calendar
            </a>
            <a href="{{ route('admin.lab.bookings.reports') }}" style="background:#fff;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid #d1d5db;box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                <i class="fa-solid fa-file-invoice-dollar" style="color:#16a34a;"></i> Reports
            </a>
            <a href="{{ route('admin.lab.bookings.index') }}" style="background:#d4155b;color:#fff;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-list-check"></i> Manage All Bookings
            </a>
        </div>
    </div>

    <!-- SUMMARY CARDS (SECTION 3) -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;margin-bottom:24px;">
        
        <!-- Total Bookings -->
        <div style="background:#fff;padding:20px;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);position:relative;overflow:hidden;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Total Bookings</span>
                <span style="width:36px;height:36px;border-radius:10px;background:#f3f4f6;color:#374151;display:flex;align-items:center;justify-content:center;font-size:15px;">
                    <i class="fa-solid fa-clipboard-list"></i>
                </span>
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:800;color:#111827;margin-top:12px;">
                {{ number_format($stats['total'] ?? 0) }}
            </div>
            <div style="font-size:12px;color:#10b981;font-weight:600;margin-top:4px;">
                <i class="fa-solid fa-coins"></i> ₹{{ number_format($stats['revenue'] ?? 0, 2) }} Revenue
            </div>
        </div>

        <!-- Pending Bookings -->
        <div style="background:#fff;padding:20px;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);position:relative;overflow:hidden;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:#b45309;text-transform:uppercase;letter-spacing:0.5px;">Pending Bookings</span>
                <span style="width:36px;height:36px;border-radius:10px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;font-size:15px;">
                    <i class="fa-solid fa-clock"></i>
                </span>
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:800;color:#d97706;margin-top:12px;">
                {{ number_format($stats['pending'] ?? 0) }}
            </div>
            <div style="font-size:12px;color:#b45309;margin-top:4px;">Requires confirmation</div>
        </div>

        <!-- Confirmed Bookings -->
        <div style="background:#fff;padding:20px;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);position:relative;overflow:hidden;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:#4338ca;text-transform:uppercase;letter-spacing:0.5px;">Confirmed Bookings</span>
                <span style="width:36px;height:36px;border-radius:10px;background:#e0e7ff;color:#4f46e5;display:flex;align-items:center;justify-content:center;font-size:15px;">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:800;color:#4f46e5;margin-top:12px;">
                {{ number_format($stats['confirmed'] ?? 0) }}
            </div>
            <div style="font-size:12px;color:#6366f1;margin-top:4px;">Ready for sample collection</div>
        </div>

        <!-- Completed Bookings -->
        <div style="background:#fff;padding:20px;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);position:relative;overflow:hidden;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:0.5px;">Completed Bookings</span>
                <span style="width:36px;height:36px;border-radius:10px;background:#dcfce7;color:#16a34a;display:flex;align-items:center;justify-content:center;font-size:15px;">
                    <i class="fa-solid fa-check-double"></i>
                </span>
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:800;color:#16a34a;margin-top:12px;">
                {{ number_format($stats['completed'] ?? 0) }}
            </div>
            <div style="font-size:12px;color:#16a34a;margin-top:4px;">Reports generated &amp; delivered</div>
        </div>

        <!-- Cancelled Bookings -->
        <div style="background:#fff;padding:20px;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);position:relative;overflow:hidden;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:#b91c1c;text-transform:uppercase;letter-spacing:0.5px;">Cancelled Bookings</span>
                <span style="width:36px;height:36px;border-radius:10px;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:15px;">
                    <i class="fa-solid fa-ban"></i>
                </span>
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:800;color:#dc2626;margin-top:12px;">
                {{ number_format($stats['cancelled'] ?? 0) }}
            </div>
            <div style="font-size:12px;color:#dc2626;margin-top:4px;">Rejected / cancelled</div>
        </div>
    </div>

    <!-- MAIN TWO COLUMN GRID -->
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px;">
        
        <!-- COLUMN 1: RECENT BOOKINGS -->
        <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-family:'Syne',sans-serif;font-weight:700;font-size:16px;color:#111827;margin:0;">
                    <i class="fa-solid fa-clock-rotate-left" style="color:#d4155b;margin-right:6px;"></i> Recent Lab Bookings
                </h3>
                <a href="{{ route('admin.lab.bookings.index') }}" style="font-size:13px;font-weight:600;color:#2563eb;text-decoration:none;">
                    View All &rarr;
                </a>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;color:#6b7280;text-align:left;">
                            <th style="padding:10px 12px;font-weight:600;">Booking ID</th>
                            <th style="padding:10px 12px;font-weight:600;">Customer</th>
                            <th style="padding:10px 12px;font-weight:600;">Test</th>
                            <th style="padding:10px 12px;font-weight:600;">Date &amp; Slot</th>
                            <th style="padding:10px 12px;font-weight:600;">Amount</th>
                            <th style="padding:10px 12px;font-weight:600;text-align:center;">Status</th>
                            <th style="padding:10px 12px;font-weight:600;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $b)
                        @php $badge = $b->status_badge; @endphp
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:12px;">
                                <a href="{{ route('admin.lab.bookings.show', $b->id) }}" style="font-family:monospace;font-weight:700;color:#2563eb;text-decoration:none;">
                                    #{{ $b->booking_id }}
                                </a>
                            </td>
                            <td style="padding:12px;">
                                <div style="font-weight:600;color:#111827;">{{ $b->customer_name }}</div>
                                <div style="font-size:11px;color:#6b7280;">{{ $b->customer_phone }}</div>
                            </td>
                            <td style="padding:12px;">
                                <div style="font-weight:500;color:#111827;">{{ $b->labTest->test_name ?? 'Diagnostic Test' }}</div>
                                <div style="font-size:11px;color:#d4155b;">{{ $b->lab->name ?? 'Partner Lab' }}</div>
                            </td>
                            <td style="padding:12px;">
                                <div style="color:#111827;">{{ $b->formatted_date }}</div>
                                <div style="font-size:11px;color:#6b7280;">{{ $b->booking_time }}</div>
                            </td>
                            <td style="padding:12px;font-weight:700;color:#111827;">
                                ₹{{ number_format($b->total_amount, 2) }}
                            </td>
                            <td style="padding:12px;text-align:center;">
                                <span style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border:1px solid {{ $badge['border'] }};padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap;">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td style="padding:12px;text-align:center;">
                                <a href="{{ route('admin.lab.bookings.show', $b->id) }}" style="padding:5px 10px;background:#f3f4f6;color:#374151;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="padding:30px;text-align:center;color:#9ca3af;">
                                No recent bookings recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- COLUMN 2: TODAY'S APPOINTMENTS -->
        <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-family:'Syne',sans-serif;font-weight:700;font-size:16px;color:#111827;margin:0;">
                    <i class="fa-solid fa-calendar-day" style="color:#2563eb;margin-right:6px;"></i> Today's Schedule
                </h3>
                <span style="background:#eff6ff;color:#1d4ed8;font-size:12px;font-weight:700;padding:2px 8px;border-radius:6px;">
                    {{ count($todayAppointments) }} Appointments
                </span>
            </div>

            @forelse($todayAppointments as $app)
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:12px;margin-bottom:10px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span style="font-size:12px;font-weight:700;color:#2563eb;">
                        <i class="fa-regular fa-clock"></i> {{ $app->booking_time }}
                    </span>
                    <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;background:#dbeafe;color:#1e40af;">
                        {{ $app->status }}
                    </span>
                </div>
                <div style="font-weight:700;font-size:13px;color:#111827;">{{ $app->customer_name }}</div>
                <div style="font-size:12px;color:#4b5563;margin-top:2px;">
                    {{ $app->labTest->test_name ?? 'Test' }} &bull; {{ $app->city }}
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;padding-top:6px;border-top:1px dashed #e5e7eb;font-size:11px;">
                    <span style="color:#6b7280;"><i class="fa-solid fa-phone"></i> {{ $app->customer_phone }}</span>
                    <a href="{{ route('admin.lab.bookings.show', $app->id) }}" style="color:#d4155b;font-weight:600;text-decoration:none;">View &rarr;</a>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:40px 10px;color:#9ca3af;">
                <i class="fa-solid fa-calendar-check" style="font-size:36px;color:#d1d5db;margin-bottom:8px;"></i>
                <div style="font-weight:600;font-size:13px;color:#4b5563;">No sample collections scheduled for today</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Upcoming appointments will appear here</div>
            </div>
            @endforelse

            <div style="margin-top:16px;">
                <a href="{{ route('admin.lab.bookings.calendar') }}" style="display:block;text-align:center;padding:10px;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:8px;font-size:12px;font-weight:600;color:#475569;text-decoration:none;">
                    <i class="fa-solid fa-calendar-days"></i> Open Full Interactive Calendar
                </a>
            </div>
        </div>

    </div>

</main>
@endsection
