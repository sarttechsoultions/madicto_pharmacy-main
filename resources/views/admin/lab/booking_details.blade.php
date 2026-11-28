@extends('admin.layouts.layout')

@section('content')
<style>
    body, input, select, textarea, button, table, th, td, h1, h2, h3, h4, h5, h6, .content {
        font-family: 'Poppins', sans-serif !important;
    }
    .copy-btn-mini {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0 4px;
        color: #9ca3af;
        font-size: 11px;
        transition: color 0.15s;
    }
    .copy-btn-mini:hover {
        color: #2563eb;
    }
    .badge-off {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
</style>

<main class="content">

    <!-- BREADCRUMBS & HEADER -->
    <div style="margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:8px;">
            <a href="{{ route('admin.lab.bookings.dashboard') }}" style="color:#6b7280;text-decoration:none;">Lab Test Management</a>
            <i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>
            <a href="{{ route('admin.lab.bookings.index') }}" style="color:#6b7280;text-decoration:none;">All Bookings</a>
            <i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>
            <span style="color:#111827;font-weight:600;">#{{ $booking->booking_id }}</span>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;">
            <div>
                <h1 style="font-weight:800;font-size:24px;color:#111827;margin:0 0 6px 0;">
                    Booking Reference: <span style="color:#d4155b;">#{{ $booking->booking_id }}</span>
                </h1>
                <p style="color:#6b7280;font-size:14px;margin:0;">
                    Created on {{ $booking->created_at ? $booking->created_at->format('d M, Y \a\t h:i A') : 'N/A' }}
                </p>
            </div>

            <div style="display:flex;gap:10px;align-items:center;">
                @php $badge = $booking->status_badge; @endphp
                <span style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border:1px solid {{ $badge['border'] }};padding:7px 16px;border-radius:20px;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fa-solid {{ $badge['icon'] }}"></i> {{ $booking->status }}
                </span>

                <a href="{{ route('admin.lab.bookings.index') }}" style="padding:8px 14px;background:#f3f4f6;color:#374151;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    &larr; Back to Bookings
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN GRID: DETAILS (LEFT 2 COLS) & ACTIONS/STATUS (RIGHT 1 COL) -->
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

        <!-- LEFT COLUMN: 3 MAIN SECTIONS -->
        <div style="display:flex;flex-direction:column;gap:20px;">

            <!-- SECTION 1: CUSTOMER INFORMATION -->
            <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:16px;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <h3 style="font-weight:700;font-size:16px;color:#111827;margin:0;">Customer Information</h3>
                        <p style="font-size:12px;color:#6b7280;margin:0;">Patient contact and physical sample collection address</p>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:13px;">
                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Customer / Patient Name</div>
                        <div style="font-weight:700;color:#111827;font-size:15px;margin-top:2px;display:flex;align-items:center;gap:6px;">
                            {{ $booking->customer_name }}
                            <button type="button" class="copy-btn-mini" onclick="copyToClipboard('{{ addslashes($booking->customer_name) }}', this)" title="Copy name">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Mobile Number</div>
                        <div style="font-weight:700;color:#111827;font-size:14px;margin-top:2px;display:flex;align-items:center;gap:6px;">
                            <span style="background:#eff6ff;color:#1d4ed8;padding:2px 8px;border-radius:6px;border:1px solid #bfdbfe;display:inline-flex;align-items:center;gap:6px;">
                                <i class="fa-solid fa-phone" style="font-size:11px;"></i>{{ $booking->customer_phone }}
                            </span>
                            <button type="button" class="copy-btn-mini" onclick="copyToClipboard('{{ $booking->customer_phone }}', this)" title="Copy phone number">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div style="grid-column:1/-1;">
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Email Address</div>
                        <div style="color:#374151;margin-top:2px;">
                            {{ $booking->customer_email ?: 'No email address provided' }}
                        </div>
                    </div>

                    <div style="grid-column:1/-1;background:#f9fafb;padding:14px;border-radius:10px;border:1px solid #e5e7eb;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                            <div style="color:#1e40af;font-size:12px;font-weight:700;text-transform:uppercase;">
                                <i class="fa-solid fa-location-dot" style="margin-right:4px;color:#ef4444;"></i> Complete Collection Address
                            </div>
                            <button type="button" class="copy-btn-mini" onclick="copyToClipboard('{{ addslashes($booking->address . ', ' . $booking->city . ', ' . $booking->state . ' - ' . $booking->pincode) }}', this)" title="Copy full collection address" style="font-size:12px;color:#2563eb;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fa-regular fa-copy"></i> Copy Address
                            </button>
                        </div>
                        <div style="font-weight:600;color:#111827;line-height:1.5;">{{ $booking->address }}</div>
                        <div style="margin-top:6px;display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:#4b5563;">
                            <span><strong>City:</strong> {{ $booking->city }}</span>
                            <span><strong>State:</strong> {{ $booking->state }}</span>
                            <span><strong>PIN Code:</strong> {{ $booking->pincode }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: TEST INFORMATION -->
            <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#fdf2f8;color:#d4155b;display:flex;align-items:center;justify-content:center;font-size:16px;">
                        <i class="fa-solid fa-flask"></i>
                    </div>
                    <div>
                        <h3 style="font-weight:700;font-size:16px;color:#111827;margin:0;">Test Information &amp; Pricing</h3>
                        <p style="font-size:12px;color:#6b7280;margin:0;">Diagnostic test ordered, laboratory partner, regular &amp; cut pricing</p>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:13px;">
                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Test Name</div>
                        <div style="font-weight:700;color:#111827;font-size:15px;margin-top:2px;">
                            {{ $booking->labTest->test_name ?? ($booking->items->first()->test_name ?? 'Diagnostic Test') }}
                        </div>
                        @if($booking->labTest && $booking->labTest->description)
                        <div style="font-size:12px;color:#6b7280;margin-top:4px;line-height:1.4;">
                            {{ Str::limit($booking->labTest->description, 140) }}
                        </div>
                        @endif
                    </div>

                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Category</div>
                        <div style="font-weight:600;color:#2563eb;margin-top:2px;">
                            {{ $booking->labTest->category->name ?? 'General Health' }}
                        </div>
                    </div>

                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Laboratory Partner</div>
                        <div style="font-weight:700;color:#d4155b;font-size:14px;margin-top:2px;">
                            <i class="fa-solid fa-hospital" style="font-size:11px;margin-right:4px;"></i>
                            {{ $booking->lab->name ?? ($booking->items->first()->lab_name ?? 'Partner Diagnostic Lab') }}
                        </div>
                    </div>

                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Sample Specimen Type</div>
                        <div style="color:#374151;margin-top:2px;">
                            <i class="fa-solid fa-vial" style="font-size:11px;color:#7c3aed;margin-right:4px;"></i>
                            {{ $booking->labTest->sample_type ?? 'Blood / Specimen' }}
                        </div>
                    </div>

                    @php
                        $testModel = $booking->labTest;
                        $cutPrice = $testModel ? $testModel->cut_price : null;
                        $offPct = $testModel ? $testModel->discount_percentage : null;
                    @endphp

                    <div style="grid-column:1/-1;border-top:1px dashed #e5e7eb;padding-top:14px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                        <div>
                            <span style="font-size:13px;color:#6b7280;">Test Regular / Cut Price:</span>
                            @if($cutPrice && $cutPrice > $booking->total_amount)
                            <span style="text-decoration:line-through;color:#9ca3af;font-size:14px;margin-left:6px;">₹{{ number_format($cutPrice, 2) }}</span>
                            @if($offPct)
                            <span class="badge-off" style="margin-left:6px;"><i class="fa-solid fa-arrow-down"></i> {{ $offPct }}% OFF</span>
                            @endif
                            @else
                            <strong style="margin-left:6px;color:#111827;">₹{{ number_format($booking->total_amount, 2) }}</strong>
                            @endif
                        </div>
                        <div>
                            <span style="font-size:13px;color:#6b7280;">Total Amount Payable:</span>
                            <span style="font-size:22px;font-weight:800;color:#111827;margin-left:8px;">
                                ₹{{ number_format($booking->total_amount, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: BOOKING INFORMATION -->
            <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;font-size:16px;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 style="font-weight:700;font-size:16px;color:#111827;margin:0;">Booking Information</h3>
                        <p style="font-size:12px;color:#6b7280;margin:0;">Scheduling coordinates, instructions, and lifecycle timestamps</p>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:13px;">
                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Booking Reference ID</div>
                        <div style="font-family:monospace;font-weight:700;color:#111827;font-size:15px;margin-top:2px;">#{{ $booking->booking_id }}</div>
                    </div>

                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Current Booking Status</div>
                        <div style="margin-top:4px;">
                            <span style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border:1px solid {{ $badge['border'] }};padding:4px 10px;border-radius:12px;font-size:11px;font-weight:700;">
                                {{ $booking->status }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Scheduled Booking Date</div>
                        <div style="font-weight:700;color:#111827;margin-top:2px;">
                            <i class="fa-regular fa-calendar" style="color:#2563eb;margin-right:4px;"></i>{{ $booking->formatted_date }}
                        </div>
                    </div>

                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Preferred Time Slot</div>
                        <div style="font-weight:700;color:#111827;margin-top:2px;">
                            <i class="fa-regular fa-clock" style="color:#6b7280;margin-right:4px;"></i>{{ $booking->booking_time }}
                        </div>
                    </div>

                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Booking Created Date</div>
                        <div style="color:#374151;margin-top:2px;">
                            {{ $booking->created_at ? $booking->created_at->format('d M, Y h:i A') : 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <div style="color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;">Payment Status &amp; Method</div>
                        <div style="color:#16a34a;font-weight:600;margin-top:2px;">
                            {{ $booking->payment_status }} ({{ $booking->payment_method }})
                        </div>
                    </div>

                    @if($booking->additional_instructions)
                    <div style="grid-column:1/-1;background:#fffbeb;border:1px solid #fef3c7;padding:12px 14px;border-radius:8px;">
                        <strong style="color:#92400e;font-size:12px;"><i class="fa-solid fa-comment-dots"></i> Additional Instructions:</strong>
                        <div style="color:#78350f;margin-top:4px;">{{ $booking->additional_instructions }}</div>
                    </div>
                    @endif

                    @if($booking->cancellation_reason)
                    <div style="grid-column:1/-1;background:#fee2e2;border:1px solid #fecaca;padding:12px 14px;border-radius:8px;">
                        <strong style="color:#991b1b;font-size:12px;"><i class="fa-solid fa-triangle-exclamation"></i> Cancellation Remarks:</strong>
                        <div style="color:#7f1d1d;margin-top:4px;">{{ $booking->cancellation_reason }}</div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: STATUS UPDATE & REPORT UPLOAD -->
        <div style="display:flex;flex-direction:column;gap:20px;">
            
            <!-- STATUS MANAGEMENT CARD -->
            <div style="background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                <h3 style="font-weight:700;font-size:16px;color:#111827;margin:0 0 16px 0;">
                    <i class="fa-solid fa-sliders" style="color:#2563eb;margin-right:6px;"></i> Update Booking Status
                </h3>

                <!-- Quick Action Buttons -->
                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:18px;">
                    @if($booking->status === 'Pending')
                    <form action="{{ route('admin.lab.bookings.confirm', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" style="width:100%;padding:10px;background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                            <i class="fa-solid fa-check"></i> One-Click Confirm
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Comprehensive Status Form -->
                <form action="{{ route('admin.lab.bookings.status', $booking->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Change Status *</label>
                        <select name="status" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ $booking->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Payment Status</label>
                        <select name="payment_status" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="Pending" {{ $booking->payment_status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Paid" {{ $booking->payment_status === 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Failed" {{ $booking->payment_status === 'Failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Attach Patient Lab Report (PDF)</label>
                        <input type="file" name="report_file" accept=".pdf,image/*" style="width:100%;padding:8px;border:1px dashed #d1d5db;border-radius:8px;font-size:12px;background:#f9fafb;">
                        @if($booking->report_file)
                        <div style="margin-top:6px;">
                            <a href="{{ asset($booking->report_file) }}" target="_blank" style="font-size:12px;color:#16a34a;font-weight:700;text-decoration:none;">
                                <i class="fa-solid fa-file-pdf"></i> Download Attached Report
                            </a>
                        </div>
                        @endif
                    </div>

                    <div style="margin-bottom:18px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Remarks / Cancellation Reason</label>
                        <input type="text" name="cancellation_reason" value="{{ $booking->cancellation_reason }}" placeholder="Reason or operational notes..." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <button type="submit" style="width:100%;padding:12px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Status Update
                    </button>
                </form>

                @if(!in_array($booking->status, ['Cancelled', 'Completed']))
                <div style="margin-top:16px;padding-top:16px;border-top:1px dashed #e5e7eb;">
                    <form action="{{ route('admin.lab.bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Cancel this lab booking permanently?');">
                        @csrf
                        <button type="submit" style="width:100%;padding:9px;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">
                            <i class="fa-solid fa-ban"></i> Cancel Booking
                        </button>
                    </form>
                </div>
                @endif
            </div>

        </div>

    </div>

</main>

<script>
function copyToClipboard(text, btn) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check" style="color:#10b981;"></i>';
            setTimeout(() => { btn.innerHTML = originalHtml; }, 1500);
        }).catch(err => fallbackCopy(text, btn));
    } else {
        fallbackCopy(text, btn);
    }
}
function fallbackCopy(text, btn) {
    const el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-check" style="color:#10b981;"></i>';
    setTimeout(() => { btn.innerHTML = originalHtml; }, 1500);
}
</script>
@endsection
