@extends('admin.layouts.layout')

@section('content')
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-weight:700;font-size:24px;color:#111827;margin:0 0 6px 0;">
                <i class="fa-solid fa-list-check" style="color:#d4155b;margin-right:8px;"></i>All Lab Test Bookings
            </h1>
            <p style="color:#6b7280;font-size:14px;margin:0;">
                Manage customer test bookings, track collection schedules, confirm appointments, and update statuses.
            </p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('admin.lab.bookings.calendar') }}" style="background:#fff;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid #d1d5db;">
                <i class="fa-solid fa-calendar-days" style="color:#2563eb;"></i> Calendar View
            </a>
            <a href="{{ route('customer.lab.tests') }}" target="_blank" style="background:#f3f4f6;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid #d1d5db;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Patient Portal
            </a>
        </div>
    </div>

    <!-- SUMMARY CARDS (SECTION 3) -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <!-- Total -->
        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;">Total Bookings</div>
            <div style="font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#111827;margin-top:6px;">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>

        <!-- Pending -->
        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#b45309;text-transform:uppercase;">Pending</div>
            <div style="font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#d97706;margin-top:6px;">{{ number_format($stats['pending'] ?? 0) }}</div>
        </div>

        <!-- Confirmed -->
        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#4338ca;text-transform:uppercase;">Confirmed</div>
            <div style="font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#4f46e5;margin-top:6px;">{{ number_format($stats['confirmed'] ?? 0) }}</div>
        </div>

        <!-- Completed -->
        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#15803d;text-transform:uppercase;">Completed</div>
            <div style="font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#16a34a;margin-top:6px;">{{ number_format($stats['completed'] ?? 0) }}</div>
        </div>

        <!-- Cancelled -->
        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#b91c1c;text-transform:uppercase;">Cancelled</div>
            <div style="font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#dc2626;margin-top:6px;">{{ number_format($stats['cancelled'] ?? 0) }}</div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR (SECTION 4) -->
    <div style="background:#fff;padding:18px 20px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('admin.lab.bookings.index') }}" method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            
            <!-- Search Input: by Booking ID, Customer Name, Mobile Number -->
            <div style="position:relative;min-width:280px;flex:1;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Booking ID, Customer Name, Mobile Number..." style="width:100%;padding:10px 12px 10px 36px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
            </div>

            <!-- Status Filter -->
            <div style="min-width:180px;">
                <select name="status" onchange="this.form.submit()" style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                    <option value="all">All Booking Statuses</option>
                    @foreach($statuses as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Filter -->
            <div style="min-width:160px;">
                <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;color:#374151;">
            </div>

            <!-- Buttons -->
            <div style="display:flex;gap:8px;">
                <button type="submit" style="padding:10px 18px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'date', 'lab_id']))
                <a href="{{ route('admin.lab.bookings.index') }}" style="padding:10px 16px;background:#f3f4f6;color:#4b5563;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- BOOKINGS DATA TABLE (SECTION 4) -->
    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                <thead>
                    <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;color:#4b5563;">
                        <th style="padding:14px 16px;font-weight:600;">Booking ID</th>
                        <th style="padding:14px 16px;font-weight:600;">Customer Name</th>
                        <th style="padding:14px 16px;font-weight:600;">Mobile Number</th>
                        <th style="padding:14px 16px;font-weight:600;">Lab Test Name</th>
                        <th style="padding:14px 16px;font-weight:600;">Lab Name</th>
                        <th style="padding:14px 16px;font-weight:600;">Test Price</th>
                        <th style="padding:14px 16px;font-weight:600;">Booking Date</th>
                        <th style="padding:14px 16px;font-weight:600;">Booking Time</th>
                        <th style="padding:14px 16px;font-weight:600;">Customer Address</th>
                        <th style="padding:14px 16px;font-weight:600;text-align:center;">Booking Status</th>
                        <th style="padding:14px 16px;font-weight:600;text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                    @php $badge = $b->status_badge; @endphp
                    <tr style="border-bottom:1px solid #f3f4f6;transition:background 0.15s ease;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='#fff'">
                        
                        <!-- Booking ID -->
                        <td style="padding:14px 16px;white-space:nowrap;">
                            <a href="{{ route('admin.lab.bookings.show', $b->id) }}" style="font-family:monospace;font-weight:700;color:#1e40af;background:#eff6ff;padding:4px 8px;border-radius:6px;border:1px solid #bfdbfe;text-decoration:none;display:inline-block;">
                                #{{ $b->booking_id }}
                            </a>
                        </td>

                        <!-- Customer Name -->
                        <td style="padding:14px 16px;font-weight:700;color:#111827;white-space:nowrap;">
                            {{ $b->customer_name }}
                        </td>

                        <!-- Mobile Number -->
                        <td style="padding:14px 16px;color:#374151;white-space:nowrap;">
                            <i class="fa-solid fa-phone" style="font-size:10px;color:#9ca3af;margin-right:4px;"></i>{{ $b->customer_phone }}
                        </td>

                        <!-- Lab Test Name -->
                        <td style="padding:14px 16px;font-weight:600;color:#111827;min-width:180px;">
                            {{ $b->labTest->test_name ?? ($b->items->first()->test_name ?? 'Diagnostic Test') }}
                        </td>

                        <!-- Lab Name -->
                        <td style="padding:14px 16px;color:#d4155b;font-weight:500;white-space:nowrap;">
                            <i class="fa-solid fa-hospital" style="font-size:10px;margin-right:4px;"></i>{{ $b->lab->name ?? ($b->items->first()->lab_name ?? 'Diagnostic Lab') }}
                        </td>

                        <!-- Test Price -->
                        <td style="padding:14px 16px;font-family:'Syne',sans-serif;font-weight:700;color:#111827;white-space:nowrap;">
                            ₹{{ number_format($b->total_amount, 2) }}
                        </td>

                        <!-- Booking Date -->
                        <td style="padding:14px 16px;color:#111827;white-space:nowrap;">
                            <i class="fa-regular fa-calendar" style="color:#2563eb;margin-right:4px;"></i>{{ $b->formatted_date }}
                        </td>

                        <!-- Booking Time -->
                        <td style="padding:14px 16px;color:#4b5563;white-space:nowrap;">
                            <i class="fa-regular fa-clock" style="color:#6b7280;margin-right:4px;"></i>{{ $b->booking_time }}
                        </td>

                        <!-- Customer Address -->
                        <td style="padding:14px 16px;max-width:240px;color:#374151;font-size:12px;line-height:1.4;">
                            {{ $b->full_address }}
                        </td>

                        <!-- Booking Status -->
                        <td style="padding:14px 16px;text-align:center;white-space:nowrap;">
                            <span style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border:1px solid {{ $badge['border'] }};padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                                <i class="fa-solid {{ $badge['icon'] }}" style="font-size:10px;"></i> {{ $b->status }}
                            </span>
                        </td>

                        <!-- Actions (View Details, Confirm, Update Status, Cancel) -->
                        <td style="padding:14px 16px;text-align:center;white-space:nowrap;">
                            <div style="display:inline-flex;gap:6px;align-items:center;">
                                
                                <!-- View Details -->
                                <a href="{{ route('admin.lab.bookings.show', $b->id) }}" title="View Complete Details" style="padding:6px 12px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                    <i class="fa-solid fa-eye"></i> Details
                                </a>

                                <!-- Confirm Booking (Quick Action) -->
                                @if($b->status === 'Pending')
                                <form action="{{ route('admin.lab.bookings.confirm', $b->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" title="Confirm Booking" style="padding:6px 10px;background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                                        <i class="fa-solid fa-check"></i> Confirm
                                    </button>
                                </form>
                                @endif

                                <!-- Update Status Modal Trigger -->
                                <button onclick="openStatusModal({{ $b->id }}, '{{ $b->booking_id }}', '{{ $b->status }}')" title="Update Booking Status" style="padding:6px 10px;background:#f3f4f6;color:#374151;border:1px solid #d1d5db;border-radius:6px;font-size:12px;cursor:pointer;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <!-- Cancel Booking -->
                                @if(!in_array($b->status, ['Cancelled', 'Completed']))
                                <form action="{{ route('admin.lab.bookings.cancel', $b->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel Booking #{{ $b->booking_id }}?');" style="display:inline;">
                                    @csrf
                                    <button type="submit" title="Cancel Booking" style="padding:6px 10px;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;border-radius:6px;font-size:12px;cursor:pointer;">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="padding:48px 16px;text-align:center;color:#6b7280;">
                            <div style="font-size:40px;color:#d1d5db;margin-bottom:12px;">
                                <i class="fa-solid fa-clipboard-question"></i>
                            </div>
                            <div style="font-weight:600;font-size:16px;color:#374151;">No lab test bookings found</div>
                            <div style="font-size:13px;color:#9ca3af;margin-top:4px;">
                                Adjust your search or filters to locate bookings.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;">
            <div style="font-size:13px;color:#6b7280;">
                Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} bookings
            </div>
            <div>
                {{ $bookings->links() }}
            </div>
        </div>
        @endif
    </div>

</main>

<!-- QUICK STATUS UPDATE MODAL -->
<div id="statusModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;width:100%;max-width:500px;border-radius:14px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        
        <div style="padding:18px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#111827;margin:0;">
                Update Status: <span id="mModalBookingId" style="color:#d4155b;">#LB-XXXXX</span>
            </h3>
            <button onclick="closeStatusModal()" style="background:#f3f4f6;border:none;width:28px;height:28px;border-radius:50%;cursor:pointer;color:#4b5563;font-size:12px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="quickStatusForm" method="POST" enctype="multipart/form-data" style="padding:20px;">
            @csrf
            
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Select New Status *</label>
                <select name="status" id="mStatusSelect" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                    @foreach($statuses as $st)
                    <option value="{{ $st }}">{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Attach Report PDF (If Completed)</label>
                <input type="file" name="report_file" accept=".pdf,image/*" style="width:100%;padding:8px;border:1px dashed #d1d5db;border-radius:8px;font-size:12px;">
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Notes / Cancellation Reason</label>
                <input type="text" name="cancellation_reason" placeholder="Optional notes regarding status change..." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" onclick="closeStatusModal()" style="padding:9px 16px;background:#f3f4f6;color:#374151;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit" style="padding:9px 20px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    Update Status
                </button>
            </div>
        </form>

    </div>
</div>

<script>
function openStatusModal(id, bookingId, currentStatus) {
    document.getElementById('mModalBookingId').innerText = '#' + bookingId;
    document.getElementById('quickStatusForm').action = '/lab/bookings/' + id + '/status';
    document.getElementById('mStatusSelect').value = currentStatus;
    document.getElementById('statusModal').style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

window.onclick = function(e) {
    const modal = document.getElementById('statusModal');
    if (e.target === modal) closeStatusModal();
};
</script>

@endsection
