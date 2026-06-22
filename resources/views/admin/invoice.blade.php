<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicto Invoice</title>
    <link rel="icon" type="image/png" href="{{ asset('image/favicon.webp') }}" />

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f7fb;
            padding: 30px 15px;
        }

        .invoice {
            position: relative;
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .08);
        }

        /* Watermark */

        .watermark {
            position: absolute;
            inset: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 0;
        }

        .watermark img {
            width: 900px;
            max-width: 90%;
            opacity: 0.1;
            transform: rotate(-20deg);
        }

        /* Content Above Watermark */

        .invoice-header,
        .invoice-info,
        table,
        .summary,
        .invoice-footer {
            position: relative;
            z-index: 2;
        }

        /* Header */

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #eee;
            padding-bottom: 25px;
        }

        .logo {
            width: 220px;
        }

        .right-section {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 15px;
        }

        .print-btn {
            background: #E91E63;
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: .3s;
        }

        .print-btn:hover {
            transform: translateY(-2px);
        }

        .company-details {
            text-align: right;
        }

        .company-details h1 {
            color: #E91E63;
            font-size: 34px;
            margin-bottom: 8px;
        }

        .company-details p {
            color: #666;
            line-height: 1.8;
        }

        /* Invoice Details */

        .invoice-info {
            display: flex;
            gap: 20px;
            margin: 30px 0;
        }

        .info-box {
            flex: 1;

            padding: 20px;
            border-radius: 12px;
        }

        .info-box h3 {
            color: #E91E63;
            margin-bottom: 15px;
        }

        .info-box p {
            margin-bottom: 10px;
            color: #555;
        }

        /* Table */

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #E91E63;
            color: #fff;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        tbody tr {
            border-bottom: 1px solid #eee;
        }

        tbody tr:hover {
            background: #fff5f8;
        }

        /* Summary */

        .summary {
            width: 350px;
            margin-left: auto;
            margin-top: 30px;
            background: #fafafa;
            padding: 25px;
            border-radius: 15px;
        }

        .summary p,
        .summary h2 {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .summary h2 {
            color: #E91E63;
            border-top: 2px dashed #ddd;
            padding-top: 15px;
        }

        /* Footer */

        .invoice-footer {
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 20px;
            text-align: center;
        }

        .invoice-footer p {
            color: #666;
            margin-bottom: 8px;
        }

        /* Responsive */

        @media(max-width:768px) {

            .invoice {
                padding: 20px;
            }

            .invoice-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 20px;
            }

            .right-section {
                align-items: center;
            }

            .company-details {
                text-align: center;
            }

            .invoice-info {
                flex-direction: column;
            }

            .summary {
                width: 100%;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .watermark img {
                width: 500px;
            }
        }

        /* Print */

        @media print {

            .print-btn {
                display: none;
            }

            body {
                background: #fff;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice {
                max-width: 100%;
                width: 100%;
                margin: 0;
                box-shadow: none;
                border: none;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

    <div class="invoice">

        <!-- Watermark -->
        <div class="watermark">
            <img src="{{ asset('image/medicto logo-1.webp') }}" alt="Watermark">
        </div>

        <!-- Header -->
        <div class="invoice-header">

            <div>
                <img src="{{ asset('image/medicto logo-1.webp') }}" alt="Logo" class="logo">
            </div>

            <div class="right-section">

                <button class="print-btn" onclick="window.print()">
                    🖨 Print Invoice
                </button>

                <div class="company-details">
                    <h1>MEDICTO PHARMACY</h1>
                    <p>Jaipur, Rajasthan</p>
                    <p>GSTIN : 08ABCDE1234F1Z5</p>
                    <p>+91 9876543210</p>
                </div>

            </div>

        </div>

        <!-- Details -->
        <div class="invoice-info">

            <div class="info-box">
                <h3>Invoice Details</h3>
                <p><strong>Invoice No:</strong> INV-{{ $order->id }}</p>
                <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
                <p><strong>Payment:</strong> {{ $order->payment_method ?? 'Cash On Delivery' }}</p>
                <p><strong>Payment Status:</strong>
                    {{ $order->payment_status }}
                </p>
            </div>

            <div class="info-box">
                <h3>Customer Details</h3>
                <p>
                    <strong>Name:</strong>
                    {{ $order->address->full_name }}
                </p>

                <p>
                    <strong>Phone:</strong>
                    {{ $order->address->phone_number }}
                </p>

                <p>
                    <strong>Address:</strong>

                    {{ $order->delivery_street_address }},

                    {{ $order->delivery_landmark }},

                    {{ $order->delivery_city }},

                    {{ $order->delivery_state }}

                    - {{ $order->delivery_pin_code }}
                </p>
            </div>

        </div>

        <!-- Products -->
        <table>

            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>GST</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>

                @php
                $subtotal = 0;
                @endphp

                @foreach($order->items as $item)

                @php
                $total = $item->price * $item->quantity;
                $subtotal += $total;
                @endphp

                <tr>

                    <td>{{ $item->medicine_name }}</td>

                    <td>{{ $item->quantity }}</td>

                    <td>₹{{ number_format($item->price,2) }}</td>

                    <td>0%</td>

                    <td>₹{{ number_format($total,2) }}</td>

                </tr>

                @endforeach

            </tbody>

        </table>

        <!-- Summary -->
        @php

        $delivery = $order->delivery_charge ?? 0;

        $discount = $order->discount_amount ?? 0;

        $gst = 0;

        @endphp

        <div class="summary">

            <p>
                <span>Subtotal</span>
                <span>₹{{ number_format($subtotal,2) }}</span>
            </p>

            <p>
                <span>GST</span>
                <span>₹{{ number_format($gst,2) }}</span>
            </p>

            <p>
                <span>Delivery</span>
                <span>₹{{ number_format($delivery,2) }}</span>
            </p>

            <p>
                <span>Discount</span>
                <span>-₹{{ number_format($discount,2) }}</span>
            </p>

            <h2>
                <span>Grand Total</span>
                <span>
                    ₹{{ number_format($order->total_amount,2) }}
                </span>
            </h2>

        </div>

        <!-- Footer -->

        <div class="invoice-footer">
            <p>Thank You For Shopping With Medicto Pharmacy</p>
            <p>Medicines once sold cannot be returned.</p>
        </div>

    </div>

</body>

</html>