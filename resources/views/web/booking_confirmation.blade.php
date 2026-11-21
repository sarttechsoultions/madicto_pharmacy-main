<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed – #{{ $booking->booking_id }} – Medicto Diagnostics</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #d4155b;
            --secondary: #0f172a;
            --success: #16a34a;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background-color: var(--bg-light); color: #1e293b; line-height: 1.5; padding: 20px 16px; }
        .card {
            max-width: 620px; margin: 30px auto; background: #fff; border-radius: 20px; border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06); overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 32px 24px; text-align: center;
            border-bottom: 1px solid #bbf7d0;
        }
        .check-icon {
            width: 60px; height: 60px; border-radius: 50%; background: var(--success); color: #fff;
            display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.35);
        }
        .header h1 { font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 800; color: #14532d; margin-bottom: 4px; }
        .header p { color: #166534; font-size: 14px; }
        .body { padding: 24px; }

        .booking-id-banner {
            background: #eff6ff; border: 1px dashed #93c5fd; border-radius: 12px; padding: 14px 18px;
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .booking-id-label { font-size: 12px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; }
        .booking-id-val { font-family: monospace; font-size: 18px; font-weight: 800; color: #1e3a8a; }

        .info-group { margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; }
        .info-title { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .info-val-primary { font-family: 'Syne', sans-serif; font-size: 17px; font-weight: 700; color: var(--secondary); }
        .info-val-sub { font-size: 13px; color: #475569; margin-top: 2px; }

        .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; }
        
        .status-pill {
            background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 4px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;
        }

        .actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn {
            flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 700; text-align: center;
            text-decoration: none; display: inline-block; cursor: pointer; transition: all 0.2s;
        }
        .btn-outline { background: #fff; color: var(--secondary); border: 1px solid var(--border-color); }
        .btn-primary { background: var(--primary); color: #fff; border: none; }
    </style>
</head>
<body>

    <div class="card">
        <div class="header">
            <div class="check-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1>Booking Confirmed!</h1>
            <p>Your lab test appointment has been registered. Our phlebotomist will contact you before collection.</p>
        </div>

        <div class="body">
            <!-- Unique Booking ID -->
            <div class="booking-id-banner">
                <div>
                    <div class="booking-id-label">Unique Booking Reference</div>
                    <div class="booking-id-val">#{{ $booking->booking_id }}</div>
                </div>
                <div>
                    <span class="status-pill">
                        <i class="fa-solid fa-clock"></i> {{ $booking->status }}
                    </span>
                </div>
            </div>

            <!-- Test Details -->
            <div class="info-group">
                <div class="info-title">Booked Diagnostic Test</div>
                <div class="info-val-primary">{{ $booking->labTest->test_name ?? ($booking->items->first()->test_name ?? 'Diagnostic Test') }}</div>
                <div class="info-val-sub">
                    <i class="fa-solid fa-hospital-user" style="color:var(--primary);margin-right:4px;"></i>
                    {{ $booking->lab->name ?? 'Diagnostic Partner Lab' }}
                </div>
            </div>

            <!-- Schedule & Price -->
            <div class="summary-grid">
                <div>
                    <div class="info-title">Appointment Date &amp; Time</div>
                    <div style="font-weight:700;color:var(--secondary);font-size:14px;">
                        <i class="fa-regular fa-calendar" style="color:#2563eb;margin-right:4px;"></i>{{ $booking->formatted_date }}
                    </div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;">
                        <i class="fa-regular fa-clock" style="margin-right:4px;"></i>{{ $booking->booking_time }}
                    </div>
                </div>
                <div>
                    <div class="info-title">Total Test Price</div>
                    <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:var(--primary);">
                        ₹{{ number_format($booking->total_amount, 2) }}
                    </div>
                    <div style="font-size:11px;color:#16a34a;font-weight:600;">
                        {{ $booking->payment_status }} ({{ $booking->payment_method }})
                    </div>
                </div>
            </div>

            <!-- Customer & Address -->
            <div class="info-group">
                <div class="info-title">Sample Collection Address &amp; Contact</div>
                <div style="font-weight:700;color:var(--secondary);font-size:14px;">{{ $booking->customer_name }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">
                    <i class="fa-solid fa-phone" style="margin-right:4px;"></i>{{ $booking->customer_phone }}
                    @if($booking->customer_email)
                    &bull; {{ $booking->customer_email }}
                    @endif
                </div>
                <div style="font-size:13px;color:#334155;margin-top:6px;line-height:1.4;">
                    <i class="fa-solid fa-location-dot" style="color:#ef4444;margin-right:4px;"></i>
                    {{ $booking->full_address }}
                </div>
            </div>

            @if($booking->additional_instructions)
            <div class="info-group">
                <div class="info-title">Additional Instructions</div>
                <div style="font-size:13px;color:#475569;">{{ $booking->additional_instructions }}</div>
            </div>
            @endif

            <div class="actions">
                <a href="{{ route('customer.lab.tests') }}" class="btn btn-outline">
                    <i class="fa-solid fa-flask"></i> Book Another Test
                </a>
                <a href="{{ route('customer.lab.my_bookings', ['phone' => $booking->customer_phone]) }}" class="btn btn-primary">
                    <i class="fa-solid fa-list-check"></i> View My Bookings
                </a>
            </div>
        </div>
    </div>

</body>
</html>
