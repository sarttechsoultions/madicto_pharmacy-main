@extends('admin.layouts.layout')

@section('content')
<style>
    body, input, select, textarea, button, table, th, td, h1, h2, h3, h4, h5, h6, .content, .page-header {
        font-family: 'Poppins', sans-serif !important;
    }
    .customer-card {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .phone-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f1f5f9;
        color: #1e293b;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        width: fit-content;
        border: 1px solid #e2e8f0;
    }
    .phone-pill:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .address-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 12px;
        color: #334155;
        line-height: 1.45;
        max-width: 280px;
    }
    .copy-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #64748b;
        font-size: 11px;
        padding: 2px 4px;
        border-radius: 4px;
        transition: all 0.2s;
    }
    .copy-btn:hover {
        color: #0f172a;
        background: #e2e8f0;
    }
</style>

<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
        <div>
            <h1 style="font-weight:700;font-size:24px;color:#111827;margin:0 0 6px 0;display:flex;align-items:center;gap:10px;">
                <i class="fa-solid fa-list-check" style="color:#d4155b;"></i> All Lab Test Bookings
            </h1>
            <p style="color:#6b7280;font-size:14px;margin:0;">
                Comprehensive patient bookings list with customer contact numbers, sample collection addresses, and order workflows.
            </p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('admin.lab.tests') }}" style="background:#fff;color:#d4155b;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid #fbcfe8;">
                <i class="fa-solid fa-vials"></i> Manage Tests Catalog
            </a>
            <a href="{{ route('admin.lab.bookings.calendar') }}" style="background:#fff;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid #d1d5db;">
                <i class="fa-solid fa-calendar-days" style="color:#2563eb;"></i> Calendar View
            </a>
            <a href="{{ route('customer.lab.tests') }}" target="_blank" style="background:#f3f4f6;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:1px solid #d1d5db;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Patient Portal
            </a>
        </div>
    </div>

    <!-- SUMMARY METRIC CARDS -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;">Total Bookings</div>
            <div style="font-size:24px;font-weight:800;color:#111827;margin-top:6px;">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>

        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#b45309;text-transform:uppercase;">Pending</div>
            <div style="font-size:24px;font-weight:800;color:#d97706;margin-top:6px;">{{ number_format($stats['pending'] ?? 0) }}</div>
        </div>

        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#4338ca;text-transform:uppercase;">Confirmed</div>
            <div style="font-size:24px;font-weight:800;color:#4f46e5;margin-top:6px;">{{ number_format($stats['confirmed'] ?? 0) }}</div>
        </div>

        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#15803d;text-transform:uppercase;">Completed</div>
            <div style="font-size:24px;font-weight:800;color:#16a34a;margin-top:6px;">{{ number_format($stats['completed'] ?? 0) }}</div>
        </div>

        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="font-size:12px;font-weight:700;color:#b91c1c;text-transform:uppercase;">Cancelled</div>
            <div style="font-size:24px;font-weight:800;color:#dc2626;margin-top:6px;">{{ number_format($stats['cancelled'] ?? 0) }}</div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div style="background:#fff;padding:18px 20px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('admin.lab.bookings.index') }}" method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            
            <!-- Search Input: by Booking ID, Customer Name, Mobile Number, City -->
            <div style="position:relative;min-width:300px;flex:1;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Customer Name, Mobile Number, Address, Booking ID..." style="width:100%;padding:10px 12px 10px 36px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
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

    <!-- BOOKINGS DATA TABLE WITH PROMINENT CUSTOMER, NUMBER, AND ADDRESS -->
    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                <thead>
                    <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;color:#4b5563;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;">
                        <th style="padding:14px 16px;font-weight:600;">Booking ID</th>
                        <th style="padding:14px 16px;font-weight:600;">Customer &amp; Mobile Number</th>
                        <th style="padding:14px 16px;font-weight:600;">Sample Collection Address</th>
                        <th style="padding:14px 16px;font-weight:600;">Diagnostic Test &amp; Lab</th>
                        <th style="padding:14px 16px;font-weight:600;">Price &amp; Payment</th>
                        <th style="padding:14px 16px;font-weight:600;">Appointment Slot</th>
                        <th style="padding:14px 16px;font-weight:600;text-align:center;">Status</th>
                        <th style="padding:14px 16px;font-weight:600;text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                    @php $badge = $b->status_badge; @endphp
                    <tr style="border-bottom:1px solid #f3f4f6;transition:background 0.15s ease;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='#fff'">
                        
                        <!-- Booking ID -->
                        <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                            <a href="{{ route('admin.lab.bookings.show', $b->id) }}" style="font-family:monospace;font-weight:700;color:#1e40af;background:#eff6ff;padding:4px 8px;border-radius:6px;border:1px solid #bfdbfe;text-decoration:none;display:inline-block;">
                                #{{ $b->booking_id }}
                            </a>
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                                {{ $b->created_at ? $b->created_at->format('d M, h:i A') : '' }}
                            </div>
                        </td>

                        <!-- Customer Name & Mobile Number (PROMINENT) -->
                        <td style="padding:14px 16px;vertical-align:top;min-width:200px;">
                            <div class="customer-card">
                                <div style="font-weight:700;color:#0f172a;font-size:14px;display:flex;align-items:center;gap:6px;">
                                    <i class="fa-solid fa-user" style="color:#64748b;font-size:12px;"></i>
                                    {{ $b->customer_name }}
                                </div>
                                <div style="margin-top:3px;">
                                    <a href="tel:{{ $b->customer_phone }}" class="phone-pill" title="Click to call customer">
                                        <i class="fa-solid fa-phone" style="color:#0284c7;font-size:10px;"></i>
                                        <span>{{ $b->customer_phone }}</span>
                                    </a>
                                    <button class="copy-btn" onclick="copyText('{{ $b->customer_phone }}', 'Phone number copied!')" title="Copy Phone Number">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                                @if($b->customer_email)
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">
                                    <i class="fa-regular fa-envelope" style="font-size:10px;"></i> {{ $b->customer_email }}
                                </div>
                                @endif
                            </div>
                        </td>

                        <!-- Customer Address (PROMINENT) -->
                        <td style="padding:14px 16px;vertical-align:top;min-width:240px;max-width:300px;">
                            <div class="address-box">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:3px;">
                                    <span style="font-weight:700;color:#d4155b;font-size:11px;display:flex;align-items:center;gap:4px;">
                                        <i class="fa-solid fa-location-dot"></i> Sample Collection Address
                                    </span>
                                    <button class="copy-btn" onclick="copyText('{{ addslashes($b->full_address) }}', 'Address copied!')" title="Copy Address">
                                        <i class="fa-regular fa-copy"></i> Copy
                                    </button>
                                </div>
                                <div style="font-weight:500;color:#1e293b;">
                                    {{ $b->address }}
                                </div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">
                                    {{ $b->city }}{{ $b->state ? ', ' . $b->state : '' }} - <strong style="color:#0f172a;">{{ $b->pincode }}</strong>
                                </div>
                            </div>
                        </td>

                        <!-- Diagnostic Test & Lab -->
                        <td style="padding:14px 16px;vertical-align:top;min-width:180px;">
                            <div style="font-weight:700;color:#111827;display:flex;align-items:center;gap:6px;">
                                <i class="fa-solid fa-vial" style="color:#d4155b;font-size:12px;"></i>
                                {{ $b->labTest->test_name ?? ($b->items->first()->test_name ?? 'Diagnostic Test') }}
                            </div>
                            <div style="font-size:11px;color:#0284c7;font-weight:600;margin-top:3px;">
                                <i class="fa-solid fa-hospital" style="font-size:10px;"></i> {{ $b->lab->name ?? ($b->items->first()->lab_name ?? 'Diagnostic Partner Lab') }}
                            </div>
                            <div style="font-size:11px;color:#64748b;margin-top:2px;">
                                Type: <span style="font-weight:600;color:#374151;">{{ ucfirst($b->sample_collection_type ?? 'Home Collection') }}</span>
                            </div>
                        </td>

                        <!-- Price & Payment -->
                        <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                            <div style="font-weight:800;color:#0f172a;font-size:15px;">
                                ₹{{ number_format($b->total_amount, 2) }}
                            </div>
                            <div style="margin-top:3px;">
                                @if(strtolower($b->payment_status) === 'paid')
                                    <span style="background:#ecfdf5;color:#059669;padding:2px 7px;border-radius:4px;font-size:10px;font-weight:700;border:1px solid #a7f3d0;">
                                        PAID ({{ ucfirst($b->payment_method ?? 'Online') }})
                                    </span>
                                @else
                                    <span style="background:#fffbeb;color:#b45309;padding:2px 7px;border-radius:4px;font-size:10px;font-weight:700;border:1px solid #fde68a;">
                                        {{ strtoupper($b->payment_status ?? 'PENDING') }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Appointment Schedule -->
                        <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                            <div style="color:#111827;font-weight:600;">
                                <i class="fa-regular fa-calendar" style="color:#2563eb;margin-right:4px;"></i>{{ $b->formatted_date }}
                            </div>
                            <div style="font-size:12px;color:#475569;margin-top:3px;">
                                <i class="fa-regular fa-clock" style="color:#64748b;margin-right:4px;"></i>{{ $b->booking_time }}
                            </div>
                        </td>

                        <!-- Booking Status -->
                        <td style="padding:14px 16px;vertical-align:top;text-align:center;white-space:nowrap;">
                            <span style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border:1px solid {{ $badge['border'] }};padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                                <i class="fa-solid {{ $badge['icon'] }}" style="font-size:10px;"></i> {{ $b->status }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td style="padding:14px 16px;vertical-align:top;text-align:right;white-space:nowrap;">
                            <div style="display:inline-flex;gap:6px;align-items:center;">
                                
                                <!-- View Details -->
                                <a href="{{ route('admin.lab.bookings.show', $b->id) }}" title="View Complete Details" style="padding:6px 12px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                    <i class="fa-solid fa-eye"></i> Details
                                </a>

                                <!-- Quick Confirm -->
                                @if($b->status === 'Pending')
                                <form action="{{ route('admin.lab.bookings.confirm', $b->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" title="Confirm Booking" style="padding:6px 10px;background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                                        <i class="fa-solid fa-check"></i>
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
                        <td colspan="8" style="padding:48px 16px;text-align:center;color:#6b7280;">
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

        <!-- UNIVERSAL NUMBERED PAGINATION -->
        @if($bookings->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #e5e7eb;background:#fff;">
            {{ $bookings->appends(request()->query())->links('pagination.admin') }}
        </div>
        @endif
    </div>

</main>

<!-- QUICK STATUS UPDATE MODAL -->
<div id="statusModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;width:100%;max-width:500px;border-radius:14px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);position:relative;overflow:hidden;">
        
        <div style="padding:18px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;background:#f8fafc;">
            <h3 style="font-size:16px;font-weight:700;color:#111827;margin:0;">
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
function copyText(text, successMsg) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            alert(successMsg || 'Copied to clipboard!');
        });
    } else {
        const temp = document.createElement('textarea');
        temp.value = text;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        alert(successMsg || 'Copied to clipboard!');
    }
}

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
