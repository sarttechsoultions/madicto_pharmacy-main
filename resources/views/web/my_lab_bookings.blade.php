<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Lab Test Bookings – Medicto Pharmacy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #d4155b;
            --secondary: #0f172a;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body, input, select, textarea, button, h1, h2, h3, h4, h5, h6, p, span, a, div {
            font-family: 'Poppins', sans-serif !important;
        }
        body { background-color: var(--bg-light); color: #1e293b; line-height: 1.5; }
        .container { max-width: 800px; margin: 0 auto; padding: 24px 16px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .header h1 { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; color: var(--secondary); }
        .btn-new { background: var(--primary); color: #fff; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 700; }
        
        .booking-card {
            background: #fff; border-radius: 14px; border: 1px solid var(--border-color); padding: 18px 20px;
            margin-bottom: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        .card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .test-title { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; color: var(--secondary); }
        .badge { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 12px; }

        .card-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px; color: #475569; margin: 10px 0; }
        .price { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 16px; color: var(--primary); }

        .phone-form {
            background: #fff; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px 20px;
            margin-bottom: 20px; display: flex; gap: 10px; align-items: center;
        }
        .phone-input { flex: 1; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; outline: none; }
        .btn-search { background: var(--secondary); color: #fff; padding: 10px 18px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>My Lab Test Bookings</h1>
            <a href="{{ route('customer.lab.tests') }}" class="btn-new">
                <i class="fa-solid fa-plus"></i> Book Lab Test
            </a>
        </div>

        <!-- Phone Lookup -->
        <form action="{{ route('customer.lab.my_bookings') }}" method="GET" class="phone-form">
            <input type="tel" name="phone" value="{{ $phone }}" placeholder="Enter your registered mobile number to view bookings..." class="phone-input">
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i> View Bookings
            </button>
        </form>

        @forelse($bookings as $b)
        @php
            $badge = $b->status_badge;
        @endphp
        <div class="booking-card">
            <div class="card-top">
                <div>
                    <span style="font-family:monospace;font-size:12px;font-weight:700;color:#2563eb;background:#eff6ff;padding:2px 8px;border-radius:4px;">
                        #{{ $b->booking_id }}
                    </span>
                    <h3 class="test-title" style="margin-top:6px;">
                        {{ $b->labTest->test_name ?? ($b->items->first()->test_name ?? 'Diagnostic Test') }}
                    </h3>
                    <div style="font-size:12px;color:var(--primary);font-weight:600;">
                        <i class="fa-solid fa-hospital"></i> {{ $b->lab->name ?? 'Diagnostic Lab' }}
                    </div>
                </div>
                <div>
                    <span class="badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border:1px solid {{ $badge['border'] }};">
                        {{ $b->status }}
                    </span>
                </div>
            </div>

            <div class="card-meta">
                <div>
                    <i class="fa-regular fa-calendar" style="color:#2563eb;"></i> <strong>Date:</strong> {{ $b->formatted_date }}
                </div>
                <div>
                    <i class="fa-regular fa-clock" style="color:#64748b;"></i> <strong>Slot:</strong> {{ $b->booking_time }}
                </div>
                <div>
                    <i class="fa-solid fa-location-dot" style="color:#ef4444;"></i> <strong>City:</strong> {{ $b->city }}, {{ $b->state }}
                </div>
                <div>
                    <i class="fa-solid fa-tag" style="color:#16a34a;"></i> <strong>Amount:</strong> <span class="price">₹{{ number_format($b->total_amount, 2) }}</span>
                </div>
            </div>

            @if($b->report_file)
            <div style="margin-top:10px;padding-top:10px;border-top:1px dashed var(--border-color);">
                <a href="{{ asset($b->report_file) }}" target="_blank" style="font-size:12px;font-weight:700;color:#16a34a;text-decoration:none;">
                    <i class="fa-solid fa-file-pdf"></i> Download Laboratory Report PDF
                </a>
            </div>
            @endif
        </div>
        @empty
        <div style="text-align:center;padding:50px 20px;background:#fff;border-radius:14px;border:1px solid var(--border-color);">
            <i class="fa-solid fa-receipt" style="font-size:42px;color:#cbd5e1;margin-bottom:12px;"></i>
            <h3 style="font-family:'Syne',sans-serif;font-size:17px;color:var(--secondary);margin-bottom:4px;">No Bookings Found</h3>
            <p style="font-size:13px;color:#64748b;margin-bottom:18px;">Enter your phone number above or book a new diagnostic lab test.</p>
            <a href="{{ route('customer.lab.tests') }}" class="btn-new">Browse Lab Tests</a>
        </div>
        @endforelse

        @if($bookings instanceof \Illuminate\Pagination\LengthAwarePaginator && $bookings->hasPages())
        <div style="margin-top:20px;">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

</body>
</html>
