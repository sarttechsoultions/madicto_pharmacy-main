@extends('admin.layouts.layout')

@section('content')
<div class="o-details-content">

    <div class="o-details-header">

        <div>
            <div class="o-details-back">← Back to Orders</div>

            <div class="o-details-title">
                Order #{{ $order->order_id }}
                <span class="o-details-badge">{{ $order->status }}</span>
            </div>

            <div class="o-details-subtitle">
                Placed on {{ $order->created_at->format('M j, Y') }} • Pharmacy Hub: Downtown Central
            </div>
        </div>


    </div>

    <div class="o-details-grid">

        <div>

            <div class="o-details-card">

                <div class="o-details-card-header">
                    <h3>Ordered Items</h3>
                    <span>3 Items</span>
                </div>

                <div class="o-details-table-wrap">
                    <table class="o-details-table">

                        <thead>
                            <tr>
                                <th>MEDICINE / PRODUCT</th>
                                <th>QTY</th>
                                <th>PRICE</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                            $subtotal = 0;
                            @endphp

                            @foreach($customerOrders as $order)

                            @php
                            $subtotal += $order->quantity * $order->medicine->price;
                            @endphp

                            <tr>
                                <td>
                                    <div class="o-details-product">
                                        <div class="med-thumb">
                                            <img src="{{ asset('uploads/medicine/' . $order->medicine->image) }}" alt="">
                                        </div>
                                        <div>
                                            <strong>{{ $order->medicine->name ?? 'N/A' }}</strong>
                                            <p>{{ $order->medicine->description ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $order->quantity }}</td>

                                <td>₹ {{ number_format($order->medicine->price, 2) }}</td>

                                <td>
                                    <strong>
                                        ₹ {{ number_format($order->quantity * $order->medicine->price, 2) }}
                                    </strong>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="o-details-total-box">

                    <div class="o-details-total">

                        <div class="o-details-total-row o-details-grand">
                            <span>Total</span>
                            <span>₹ {{ number_format($subtotal , 2) }}</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div>

            <div class="o-details-card">
                <div class="o-details-section">
                    <h3>Customer Details</h3>

                    <div class="o-details-user">
                        <div class="o-details-user-img">{{ substr($order->user->name ?? 'N/A', 0, 2) }}</div>
                        <div>
                            <h4>{{ $order->user->name ?? 'N/A' }}</h4>
                            <p>Patient ID: #PAT-{{ $order->user->id ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="o-details-info">
                        <h5>EMAIL ADDRESS</h5>
                        <p>{{ $order->user->email ?? 'N/A' }}</p>
                    </div>

                    <div class="o-details-info">
                        <h5>PHONE NUMBER</h5>
                        <p>+91 {{ $order->user->number ?? 'N/A' }}</p>
                    </div>

                    <div class="o-details-info">
                        <h5>SHIPPING ADDRESS</h5>
                        <p>
                            482 Willow Creek Drive <br>
                            Apt 4B, Health District <br>
                            Metropolis, NY 10012
                        </p>
                    </div>

                    <button class="o-details-history">View Patient History</button>

                </div>
            </div>

            @php
            $timeline = [
            [
            'title' => 'Order Received',
            'time' => $order->created_at,
            'done' => true
            ],
            [
            'title' => 'Payment Confirmed',
            'time' => $order->payment_status == 'Paid' ? $order->updated_at : null,
            'done' => $order->payment_status == 'Paid'
            ],
            [
            'title' => 'Processing & Fulfillment',
            'time' => $order->status == 'Pending' ? $order->updated_at : null,
            'done' => in_array($order->status, ['Processing','Shipped','Delivered'])
            ],
            [
            'title' => 'Out for Delivery',
            'time' => $order->status == 'Shipped' ? $order->updated_at : null,
            'done' => in_array($order->status, ['Shipped','Delivered'])
            ],
            [
            'title' => 'Delivered',
            'time' => $order->status == 'Delivered' ? $order->updated_at : null,
            'done' => $order->status == 'Delivered'
            ],
            ];
            @endphp

            <div class="o-details-card">
                <div class="o-details-section">
                    <h3>Tracking Timeline</h3>

                    @foreach($timeline as $step)

                    <div class="o-details-step">

                        <div class="o-details-circle"
                            style="background: {{ $step['done'] ? '#27ae60' : '#ddd' }};
                       color:#fff;">
                            {{ $step['done'] ? '✓' : '•' }}
                        </div>

                        <div>
                            <strong style="color: {{ $step['done'] ? '#000' : '#999' }}">
                                {{ $step['title'] }}
                            </strong>

                            <p style="color:#999;">
                                {{ $step['time'] ? $step['time']->format('M d, H:i A') : 'Pending' }}
                            </p>
                        </div>

                    </div>

                    @endforeach

                </div>
            </div>

        </div>

    </div>

</div>
<script src="{{ asset('js/main.js') }}"></script>

@endsection

<script>
    const oDetailsToggle = document.getElementById('oDetailsToggle');
    const oDetailsSidebar = document.getElementById('oDetailsSidebar');
    const oDetailsMain = document.getElementById('oDetailsMain');

    oDetailsToggle.addEventListener('click', () => {

        if (window.innerWidth <= 768) {

            oDetailsSidebar.classList.toggle('o-details-show');

        } else {

            oDetailsSidebar.classList.toggle('o-details-hide');
            oDetailsMain.classList.toggle('o-details-expand');

        }

    });

    // MOBILE OUTSIDE CLICK CLOSE

    document.addEventListener('click', function(e) {

        if (
            window.innerWidth <= 768 &&
            !oDetailsSidebar.contains(e.target) &&
            !oDetailsToggle.contains(e.target)
        ) {

            oDetailsSidebar.classList.remove('o-details-show');

        }

    });
</script>