@extends('admin.layouts.layout')

@section('content')
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1>Lab Test Orders &amp; Bookings</h1>
            <p>Manage patient test bookings, sample collection appointments, and report delivery workflows.</p>
        </div>
        <div class="page-header-btns">
            <span style="background:#e0e7ff;color:#4338ca;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-clock-rotate-left"></i> Prepared For Mobile App Checkout
            </span>
        </div>
    </div>

    <!-- NOTICE BANNER -->
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:flex-start;gap:14px;">
        <i class="fa-solid fa-circle-info" style="color:#2563eb;font-size:20px;margin-top:2px;"></i>
        <div>
            <div style="font-weight:700;color:#1e40af;font-size:14px;">Lab Bookings Integration Ready</div>
            <div style="font-size:13px;color:#3b82f6;margin-top:4px;line-height:1.5;">
                The lab test orders database table (<code>lab_test_orders</code>) and order status lifecycles (<code>pending</code>, <code>confirmed</code>, <code>sample_collected</code>, <code>in_process</code>, <code>completed</code>, <code>cancelled</code>) are fully set up. When mobile app users book tests, their appointments will appear directly here.
            </div>
        </div>
    </div>

    <!-- FILTER TABS & SEARCH -->
    <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('admin.lab.orders') }}" method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            <div style="position:relative;min-width:240px;flex:1;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient name, phone, order number..." style="width:100%;padding:9px 12px 9px 36px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" style="padding:9px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                    <option value="all">All Order Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="sample_collected" {{ request('status') == 'sample_collected' ? 'selected' : '' }}>Sample Collected</option>
                    <option value="in_process" {{ request('status') == 'in_process' ? 'selected' : '' }}>In Process</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" style="padding:9px 16px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.lab.orders') }}" style="padding:9px 14px;background:#f3f4f6;color:#4b5563;border-radius:8px;font-size:13px;text-decoration:none;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- ORDERS TABLE -->
    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                <thead>
                    <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;color:#4b5563;">
                        <th style="padding:12px 16px;font-weight:600;">Booking ID</th>
                        <th style="padding:12px 16px;font-weight:600;">Patient Info</th>
                        <th style="padding:12px 16px;font-weight:600;">Lab Test &amp; Partner</th>
                        <th style="padding:12px 16px;font-weight:600;">Schedule</th>
                        <th style="padding:12px 16px;font-weight:600;">Amount</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:14px 16px;font-family:monospace;font-weight:600;color:#111827;">
                            #{{ $order->order_number }}
                        </td>
                        <td style="padding:14px 16px;">
                            <div style="font-weight:600;color:#111827;">{{ $order->patient_name }}</div>
                            <div style="font-size:11px;color:#6b7280;">{{ $order->patient_phone }}</div>
                        </td>
                        <td style="padding:14px 16px;">
                            <div style="font-weight:600;color:#111827;">{{ $order->test->test_name ?? 'Test #'.$order->lab_test_id }}</div>
                            <div style="font-size:11px;color:#6b7280;">{{ $order->lab->name ?? 'Lab #'.$order->lab_id }}</div>
                        </td>
                        <td style="padding:14px 16px;">
                            <div style="color:#111827;">{{ $order->scheduled_date }}</div>
                            <div style="font-size:11px;color:#6b7280;">{{ $order->time_slot ?? 'Flexible' }}</div>
                        </td>
                        <td style="padding:14px 16px;font-weight:700;color:#111827;">
                            ₹{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td style="padding:14px 16px;text-align:center;">
                            <span style="background:#eff6ff;color:#2563eb;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;text-transform:capitalize;">
                                {{ str_replace('_', ' ', $order->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding:40px;text-align:center;color:#9ca3af;">
                            <i class="fa-solid fa-clipboard-list" style="font-size:36px;margin-bottom:10px;display:block;color:#d1d5db;"></i>
                            No lab test orders placed yet. Orders booked by patients via mobile application or web will be listed here.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #e5e7eb;">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</main>
@endsection
