@extends('admin.layouts.layout')

@section('content')
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-weight:700;font-size:24px;color:#111827;margin:0 0 6px 0;">
                <i class="fa-solid fa-calendar-days" style="color:#d4155b;margin-right:8px;"></i>Lab Booking Schedule Calendar
            </h1>
            <p style="color:#6b7280;font-size:14px;margin:0;">
                Track diagnostic sample collections by scheduled dates and appointment time slots.
            </p>
        </div>
        
        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('admin.lab.bookings.index') }}" style="background:#fff;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid #d1d5db;">
                <i class="fa-solid fa-list-check"></i> List View
            </a>
            <a href="{{ route('admin.lab.bookings.dashboard') }}" style="background:#d4155b;color:#fff;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- CALENDAR CONTROLS & MONTH SELECTOR -->
    @php
        $prevMonthDate = $selectedDate->copy()->subMonth();
        $nextMonthDate = $selectedDate->copy()->addMonth();
    @endphp
    <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('admin.lab.bookings.calendar', ['year' => $prevMonthDate->year, 'month' => $prevMonthDate->month]) }}" style="width:36px;height:36px;border-radius:8px;background:#f3f4f6;color:#374151;display:flex;align-items:center;justify-content:center;text-decoration:none;">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            
            <h2 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:#111827;margin:0;">
                {{ $selectedDate->format('F Y') }}
            </h2>

            <a href="{{ route('admin.lab.bookings.calendar', ['year' => $nextMonthDate->year, 'month' => $nextMonthDate->month]) }}" style="width:36px;height:36px;border-radius:8px;background:#f3f4f6;color:#374151;display:flex;align-items:center;justify-content:center;text-decoration:none;">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('admin.lab.bookings.calendar', ['year' => now()->year, 'month' => now()->month, 'day' => now()->format('Y-m-d')]) }}" style="padding:8px 14px;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">
                Today
            </a>
        </div>
    </div>

    <!-- CALENDAR MAIN TWO COLUMN LAYOUT: MONTH GRID (LEFT) & DAY TIMELINE (RIGHT) -->
    <div style="display:grid;grid-template-columns:3fr 2fr;gap:20px;align-items:start;">
        
        <!-- MONTH GRID -->
        <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            
            <!-- DAY NAMES -->
            <div style="display:grid;grid-template-columns:repeat(7, 1fr);text-align:center;font-weight:700;color:#6b7280;font-size:12px;margin-bottom:12px;text-transform:uppercase;">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>

            <!-- DAYS CELLS -->
            @php
                $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 for Sunday
                $daysInMonth = $startOfMonth->daysInMonth;
            @endphp
            <div style="display:grid;grid-template-columns:repeat(7, 1fr);gap:8px;">
                
                <!-- Blank padding days for start of month -->
                @for($i = 0; $i < $firstDayOfWeek; $i++)
                <div style="min-height:75px;background:#fafafa;border-radius:8px;border:1px solid #f3f4f6;opacity:0.4;"></div>
                @endfor

                <!-- Month Days -->
                @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $currentDate = Carbon\Carbon::createFromDate($year, $month, $day);
                    $dateStr = $currentDate->format('Y-m-d');
                    $isToday = $currentDate->isToday();
                    $isSelected = ($dateStr === $activeDay);
                    $dayData = $bookingsByDate[$dateStr] ?? null;
                    $count = $dayData['count'] ?? 0;
                @endphp
                <a href="{{ route('admin.lab.bookings.calendar', ['year' => $year, 'month' => $month, 'day' => $dateStr]) }}" 
                   style="min-height:75px;padding:8px;border-radius:10px;text-decoration:none;border:1px solid {{ $isSelected ? '#d4155b' : ($isToday ? '#2563eb' : '#e5e7eb') }};background:{{ $isSelected ? '#fdf2f8' : ($isToday ? '#eff6ff' : '#fff') }};display:flex;flex-direction:column;justify-content:space-between;transition:all 0.15s ease;"
                   onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-weight:700;font-size:13px;color:{{ $isSelected ? '#d4155b' : ($isToday ? '#1d4ed8' : '#111827') }};">
                            {{ $day }}
                        </span>
                        @if($isToday)
                        <span style="font-size:9px;background:#2563eb;color:#fff;padding:1px 4px;border-radius:4px;font-weight:700;">TODAY</span>
                        @endif
                    </div>

                    @if($count > 0)
                    <div style="margin-top:4px;">
                        <span style="background:#dbeafe;color:#1e40af;font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;display:inline-block;white-space:nowrap;">
                            {{ $count }} Booked
                        </span>
                    </div>
                    @else
                    <div style="height:14px;"></div>
                    @endif
                </a>
                @endfor
            </div>

            <!-- Legend -->
            <div style="display:flex;gap:14px;margin-top:20px;padding-top:14px;border-top:1px solid #f3f4f6;font-size:12px;color:#6b7280;flex-wrap:wrap;">
                <span style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:12px;height:12px;background:#eff6ff;border:1px solid #2563eb;border-radius:3px;"></span> Current Day
                </span>
                <span style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:12px;height:12px;background:#fdf2f8;border:1px solid #d4155b;border-radius:3px;"></span> Selected Date
                </span>
                <span style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:12px;height:12px;background:#dbeafe;border-radius:3px;"></span> Scheduled Collections
                </span>
            </div>
        </div>

        <!-- RIGHT COLUMN: SCHEDULED APPOINTMENTS FOR SELECTED DATE -->
        <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                <div>
                    <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#111827;margin:0;">
                        Schedule for {{ Carbon\Carbon::parse($activeDay)->format('d M, Y') }}
                    </h3>
                    <p style="font-size:12px;color:#6b7280;margin:0;">Time slot appointment breakdown</p>
                </div>
                <span style="background:#eff6ff;color:#1e40af;font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;">
                    {{ count($dayBookings) }} Appointments
                </span>
            </div>

            @forelse($dayBookings as $b)
            @php $badge = $b->status_badge; @endphp
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:14px;margin-bottom:12px;transition:border-color 0.15s;" onmouseover="this.style.borderColor='#93c5fd'" onmouseout="this.style.borderColor='#e5e7eb'">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span style="font-family:monospace;font-weight:700;color:#2563eb;font-size:12px;">
                        #{{ $b->booking_id }}
                    </span>
                    <span style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border:1px solid {{ $badge['border'] }};padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;">
                        {{ $b->status }}
                    </span>
                </div>

                <div style="font-weight:700;color:#111827;font-size:14px;">{{ $b->customer_name }}</div>
                <div style="font-size:12px;color:#d4155b;font-weight:600;margin-top:2px;">
                    {{ $b->labTest->test_name ?? 'Diagnostic Test' }}
                </div>
                
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;font-size:12px;color:#4b5563;">
                    <span><i class="fa-regular fa-clock" style="color:#6b7280;margin-right:4px;"></i><strong>{{ $b->booking_time }}</strong></span>
                    <span><i class="fa-solid fa-phone" style="color:#6b7280;margin-right:4px;"></i>{{ $b->customer_phone }}</span>
                </div>

                <div style="font-size:11px;color:#6b7280;margin-top:6px;line-height:1.4;">
                    <i class="fa-solid fa-location-dot" style="color:#ef4444;margin-right:4px;"></i>{{ $b->address }}, {{ $b->city }}
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:8px;border-top:1px dashed #e5e7eb;">
                    <span style="font-family:'Syne',sans-serif;font-weight:700;color:#111827;font-size:13px;">
                        ₹{{ number_format($b->total_amount, 2) }}
                    </span>
                    <a href="{{ route('admin.lab.bookings.show', $b->id) }}" style="font-size:12px;font-weight:700;color:#2563eb;text-decoration:none;">
                        Manage Booking &rarr;
                    </a>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:50px 16px;color:#9ca3af;">
                <i class="fa-regular fa-calendar-xmark" style="font-size:38px;color:#d1d5db;margin-bottom:10px;"></i>
                <div style="font-weight:600;font-size:14px;color:#4b5563;">No sample collections on this date</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Select a highlighted day on the calendar to view appointments.</div>
            </div>
            @endforelse
        </div>

    </div>

</main>
@endsection
