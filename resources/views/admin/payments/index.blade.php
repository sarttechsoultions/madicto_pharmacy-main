@extends('admin.layouts.layout')

@section('content')
<div class="st-content" style="padding: 24px; max-width: 1400px; margin: 0 auto;">

    <!-- TOP HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-credit-card" style="color: #0284c7;"></i>
                Razorpay Payments & Transactions
            </h1>
            <p style="font-size: 14px; color: #64748b; margin-top: 4px;">
                Monitor real-time mobile app and online checkout payments, capture statuses, and issue refunds.
            </p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <button id="btnTestConn" onclick="runTestConnection()" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <i class="fa-solid fa-bolt" style="color: #eab308;"></i>
                Test Connection
            </button>
            <a href="{{ route('admin.payments.settings') }}" style="background: #0284c7; color: #ffffff; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);">
                <i class="fa-solid fa-gear"></i>
                Gateway Settings
            </a>
        </div>
    </div>

    <!-- CONNECTION TEST ALERT BANNER (HIDDEN INITIALLY) -->
    <div id="connAlert" style="display: none; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; align-items: center; justify-content: space-between;">
        <span id="connAlertText"></span>
        <button onclick="document.getElementById('connAlert').style.display='none'" style="background: none; border: none; font-size: 16px; cursor: pointer; color: inherit;">&times;</button>
    </div>

    <!-- SUMMARY METRIC CARDS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase;">Total Online Volume</span>
                <span style="background: #ecfdf5; color: #059669; padding: 4px 8px; border-radius: 6px; font-size: 12px;"><i class="fa-solid fa-indian-rupee-sign"></i></span>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 10px;">
                ₹{{ number_format($totalRevenue, 2) }}
            </div>
            <div style="font-size: 12px; color: #10b981; margin-top: 4px;">
                <i class="fa-solid fa-shield-check"></i> Captured via Razorpay
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase;">Successful</span>
                <span style="background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 6px; font-size: 12px;"><i class="fa-solid fa-check"></i></span>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 10px;">
                {{ $successfulCount }}
            </div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                Paid & Verified Transactions
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase;">Pending / Initiated</span>
                <span style="background: #fffbeb; color: #d97706; padding: 4px 8px; border-radius: 6px; font-size: 12px;"><i class="fa-regular fa-clock"></i></span>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 10px;">
                {{ $pendingCount }}
            </div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                Checkout Orders in Progress
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase;">Failed / Refunded</span>
                <span style="background: #fef2f2; color: #dc2626; padding: 4px 8px; border-radius: 6px; font-size: 12px;"><i class="fa-solid fa-arrow-rotate-left"></i></span>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 10px;">
                {{ $failedCount + $refundedCount }}
            </div>
            <div style="font-size: 12px; color: #ef4444; margin-top: 4px;">
                {{ $failedCount }} Failed &bull; {{ $refundedCount }} Refunded
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH TOOLBAR -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('admin.payments.index') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: center;">
            <div style="flex: 1; min-width: 260px;">
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Order ID, Payment ID, Customer name or email..." style="width: 100%; padding: 9px 12px 9px 36px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="min-width: 150px;">
                <select name="status" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; background: #ffffff; outline: none;">
                    <option value="all">All Statuses</option>
                    <option value="captured" {{ request('status') === 'captured' ? 'selected' : '' }}>Captured / Paid</option>
                    <option value="created" {{ request('status') === 'created' ? 'selected' : '' }}>Created / Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <div style="min-width: 140px;">
                <select name="type" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; background: #ffffff; outline: none;">
                    <option value="all">All Types</option>
                    <option value="order" {{ request('type') === 'order' ? 'selected' : '' }}>Medicine Orders</option>
                    <option value="lab_booking" {{ request('type') === 'lab_booking' ? 'selected' : '' }}>Lab Bookings</option>
                    <option value="wallet" {{ request('type') === 'wallet' ? 'selected' : '' }}>Wallet</option>
                </select>
            </div>

            <button type="submit" style="background: #0284c7; color: #ffffff; border: none; padding: 9px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> Apply Filter
            </button>

            @if(request()->hasAny(['search', 'status', 'type', 'method', 'date_from', 'date_to']))
            <a href="{{ route('admin.payments.index') }}" style="color: #64748b; font-size: 13px; text-decoration: none; padding: 9px 12px; display: flex; align-items: center; gap: 4px;">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
            @endif
        </form>
    </div>

    <!-- PAYMENTS TABLE -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">
                        <th style="padding: 14px 18px;">Payment ID & Method</th>
                        <th style="padding: 14px 18px;">Reference</th>
                        <th style="padding: 14px 18px;">Customer</th>
                        <th style="padding: 14px 18px;">Amount</th>
                        <th style="padding: 14px 18px;">Status</th>
                        <th style="padding: 14px 18px;">Date & Time</th>
                        <th style="padding: 14px 18px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                        <td style="padding: 14px 18px;">
                            <div style="font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-bolt" style="color: #0284c7; font-size: 11px;"></i>
                                {{ $payment->razorpay_payment_id ?: ($payment->razorpay_order_id ?: 'Pending ID') }}
                            </div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 6px;">
                                @if($payment->method === 'upi')
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-weight: 600;">UPI</span>
                                    <span>{{ $payment->vpa ?: 'UPI Payment' }}</span>
                                @elseif($payment->method === 'card')
                                    <span style="background: #ede9fe; color: #6d28d9; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Card</span>
                                @elseif($payment->method === 'netbanking')
                                    <span style="background: #fef3c7; color: #b45309; padding: 2px 6px; border-radius: 4px; font-weight: 600;">NetBanking</span>
                                    <span>{{ $payment->bank }}</span>
                                @else
                                    <span style="background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px;">{{ ucfirst($payment->method ?: 'Razorpay') }}</span>
                                @endif
                            </div>
                        </td>

                        <td style="padding: 14px 18px;">
                            @if($payment->order_id)
                                <span style="font-weight: 600; color: #0284c7;">#{{ $payment->order_id }}</span>
                                <div style="font-size: 11px; color: #64748b;">Medicine Order</div>
                            @elseif($payment->booking_id)
                                <span style="font-weight: 600; color: #7c3aed;">#{{ $payment->booking_id }}</span>
                                <div style="font-size: 11px; color: #64748b;">Lab Booking</div>
                            @else
                                <span style="color: #64748b;">{{ ucfirst($payment->payment_type) }}</span>
                            @endif
                        </td>

                        <td style="padding: 14px 18px;">
                            <div style="font-weight: 600; color: #1e293b;">
                                {{ $payment->user->name ?? ($payment->contact ? 'Customer' : 'Guest') }}
                            </div>
                            <div style="font-size: 11px; color: #64748b;">
                                {{ $payment->email ?: ($payment->user->email ?? 'No email') }}
                            </div>
                            @if($payment->contact || ($payment->user && $payment->user->number))
                            <div style="font-size: 11px; color: #94a3b8;">
                                <i class="fa-solid fa-phone" style="font-size: 10px;"></i> {{ $payment->contact ?: $payment->user->number }}
                            </div>
                            @endif
                        </td>

                        <td style="padding: 14px 18px;">
                            <div style="font-weight: 700; color: #0f172a; font-size: 14px;">
                                ₹{{ number_format($payment->amount, 2) }}
                            </div>
                            <div style="font-size: 11px; color: #64748b;">{{ $payment->currency }}</div>
                        </td>

                        <td style="padding: 14px 18px;">
                            @if($payment->status === 'captured')
                                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-circle-check" style="font-size: 10px;"></i> Captured
                                </span>
                            @elseif($payment->status === 'created')
                                <span style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-regular fa-clock" style="font-size: 10px;"></i> Initiated
                                </span>
                            @elseif($payment->status === 'refunded')
                                <span style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-arrow-rotate-left" style="font-size: 10px;"></i> Refunded
                                </span>
                            @elseif($payment->status === 'failed')
                                <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;" title="{{ $payment->error_description }}">
                                    <i class="fa-solid fa-circle-xmark" style="font-size: 10px;"></i> Failed
                                </span>
                            @else
                                <span style="background: #f8fafc; color: #64748b; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 12px;">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            @endif
                        </td>

                        <td style="padding: 14px 18px; color: #64748b;">
                            <div>{{ $payment->created_at->format('d M, Y') }}</div>
                            <div style="font-size: 11px; color: #94a3b8;">{{ $payment->created_at->format('h:i A') }}</div>
                        </td>

                        <td style="padding: 14px 18px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                <button onclick="viewDetails({{ $payment->id }})" title="View Details" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                @if($payment->razorpay_payment_id)
                                <button onclick="syncPayment({{ $payment->id }})" title="Sync from Razorpay" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #0284c7; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </button>

                                @if($payment->status === 'captured')
                                <button onclick="openRefundModal({{ $payment->id }}, '{{ $payment->razorpay_payment_id }}', {{ $payment->amount }})" title="Issue Refund" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                    Refund
                                </button>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #94a3b8;">
                            <i class="fa-solid fa-credit-card" style="font-size: 40px; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
                            <div style="font-weight: 600; color: #64748b;">No payment records found</div>
                            <div style="font-size: 12px; margin-top: 4px;">Transactions created from the mobile app will automatically show here in real-time.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- UNIVERSAL NUMBERED PAGINATION -->
        @if($payments->hasPages())
        <div style="padding: 16px; border-top: 1px solid #e2e8f0; background: #ffffff;">
            {{ $payments->links('pagination.admin') }}
        </div>
        @endif
    </div>
</div>

<!-- PAYMENT DETAILS MODAL -->
<div id="modalDetails" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #ffffff; width: 100%; max-width: 650px; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="background: #f8fafc; padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">Payment Transaction Details</h3>
            <button onclick="document.getElementById('modalDetails').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <div id="modalDetailsContent" style="padding: 20px; max-height: 70vh; overflow-y: auto; font-size: 13px;">
            Loading details...
        </div>
        <div style="background: #f8fafc; padding: 12px 20px; border-top: 1px solid #e2e8f0; text-align: right;">
            <button onclick="document.getElementById('modalDetails').style.display='none'" style="background: #e2e8f0; color: #334155; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">Close</button>
        </div>
    </div>
</div>

<!-- REFUND MODAL -->
<div id="modalRefund" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #ffffff; width: 100%; max-width: 480px; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="background: #fef2f2; padding: 16px 20px; border-bottom: 1px solid #fee2e2; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #991b1b;">Process Razorpay Refund</h3>
            <button onclick="document.getElementById('modalRefund').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <form id="refundForm" onsubmit="submitRefund(event)" style="padding: 20px; font-size: 13px;">
            <input type="hidden" id="refundPaymentId" name="payment_id">
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 4px;">Razorpay Payment ID</label>
                <input type="text" id="refundRzpId" readonly style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 6px; font-size: 13px;">
            </div>
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 4px;">Refund Amount (₹)</label>
                <input type="number" step="0.01" id="refundAmount" name="amount" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                <span style="font-size: 11px; color: #64748b;">Leave as full amount or specify lower for partial refund.</span>
            </div>
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 4px;">Reason for Refund</label>
                <textarea id="refundReason" name="reason" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;" placeholder="e.g. Order cancelled by customer"></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('modalRefund').style.display='none'" style="background: #e2e8f0; color: #334155; border: none; padding: 9px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" id="btnSubmitRefund" style="background: #dc2626; color: #ffffff; border: none; padding: 9px 18px; border-radius: 6px; font-weight: 600; cursor: pointer;">Confirm Refund</button>
            </div>
        </form>
    </div>
</div>

<script>
function runTestConnection() {
    const btn = document.getElementById('btnTestConn');
    const alertBox = document.getElementById('connAlert');
    const alertText = document.getElementById('connAlertText');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Testing...';

    fetch('{{ route("admin.payments.test_connection") }}')
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-bolt" style="color: #eab308;"></i> Test Connection';
            alertBox.style.display = 'flex';
            if (data.success) {
                alertBox.style.background = '#ecfdf5';
                alertBox.style.color = '#065f46';
                alertBox.style.border = '1px solid #a7f3d0';
                alertText.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + data.message + ' (Key ID: ' + data.key_id + ')';
            } else {
                alertBox.style.background = '#fef2f2';
                alertBox.style.color = '#991b1b';
                alertBox.style.border = '1px solid #fecaca';
                alertText.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> ' + data.message;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-bolt" style="color: #eab308;"></i> Test Connection';
            alertBox.style.display = 'flex';
            alertBox.style.background = '#fef2f2';
            alertBox.style.color = '#991b1b';
            alertBox.style.border = '1px solid #fecaca';
            alertText.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Connection error: ' + err.message;
        });
}

function viewDetails(paymentId) {
    const modal = document.getElementById('modalDetails');
    const content = document.getElementById('modalDetailsContent');
    modal.style.display = 'flex';
    content.innerHTML = '<div style="text-align: center; padding: 30px;"><i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; color: #0284c7;"></i></div>';

    fetch('/admin/payments/' + paymentId)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.payment) {
                const p = data.payment;
                content.innerHTML = `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div><strong>Razorpay Payment ID:</strong><br><span style="color: #0284c7;">${p.razorpay_payment_id || 'N/A'}</span></div>
                        <div><strong>Razorpay Order ID:</strong><br><span>${p.razorpay_order_id || 'N/A'}</span></div>
                        <div><strong>Medicto Order / Booking:</strong><br><span>#${p.order_id || p.booking_id || 'N/A'}</span></div>
                        <div><strong>Amount:</strong><br><span style="font-size: 16px; font-weight: 700; color: #0f172a;">₹${parseFloat(p.amount).toFixed(2)} ${p.currency}</span></div>
                        <div><strong>Payment Method:</strong><br><span style="text-transform: uppercase;">${p.method || 'N/A'} ${p.vpa ? '(' + p.vpa + ')' : ''}</span></div>
                        <div><strong>Status:</strong><br><span style="font-weight: 700;">${p.status}</span></div>
                        <div><strong>Payer Name:</strong><br><span>${(p.user && p.user.name) ? p.user.name : 'Customer'}</span></div>
                        <div><strong>Payer Contact:</strong><br><span>${p.contact || (p.user ? p.user.number : 'N/A')}</span></div>
                    </div>
                    ${p.error_description ? `<div style="background: #fef2f2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 12px;"><strong>Error:</strong> [${p.error_code}] ${p.error_description}</div>` : ''}
                    ${p.raw_response ? `<div style="margin-top: 14px;"><strong>Raw Razorpay Response:</strong><pre style="background: #0f172a; color: #e2e8f0; padding: 12px; border-radius: 6px; font-size: 11px; max-height: 200px; overflow: auto; margin-top: 6px;">${JSON.stringify(p.raw_response, null, 2)}</pre></div>` : ''}
                `;
            } else {
                content.innerHTML = '<div style="color: #dc2626;">Payment not found.</div>';
            }
        });
}

function syncPayment(paymentId) {
    if (!confirm('Fetch latest status from Razorpay API for this transaction?')) return;
    fetch('/admin/payments/' + paymentId + '/sync', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    });
}

function openRefundModal(paymentId, rzpId, amount) {
    document.getElementById('refundPaymentId').value = paymentId;
    document.getElementById('refundRzpId').value = rzpId;
    document.getElementById('refundAmount').value = amount;
    document.getElementById('modalRefund').style.display = 'flex';
}

function submitRefund(e) {
    e.preventDefault();
    const paymentId = document.getElementById('refundPaymentId').value;
    const amount = document.getElementById('refundAmount').value;
    const reason = document.getElementById('refundReason').value;
    const btn = document.getElementById('btnSubmitRefund');
    btn.disabled = true;
    btn.innerText = 'Processing...';

    fetch('/admin/payments/' + paymentId + '/refund', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ amount: amount, reason: reason })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = 'Confirm Refund';
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerText = 'Confirm Refund';
        alert('Refund error: ' + err.message);
    });
}
</script>
@endsection
