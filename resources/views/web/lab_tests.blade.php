<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic Lab Tests &amp; Health Checkups – Medicto Pharmacy</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #d4155b;
            --primary-dark: #b01049;
            --primary-light: #fdf2f6;
            --secondary: #0f172a;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05);
            --hover-shadow: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background-color: var(--bg-light); color: var(--text-dark); line-height: 1.5; }
        
        .navbar {
            background: #fff; border-bottom: 1px solid var(--border-color); padding: 14px 20px;
            position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center;
        }
        .navbar-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text-dark); }
        .navbar-brand img { height: 36px; border-radius: 6px; }
        .navbar-brand span { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 18px; color: var(--primary); }
        .nav-links { display: flex; gap: 14px; align-items: center; }
        .nav-btn {
            font-size: 13px; font-weight: 600; text-decoration: none; padding: 8px 14px; border-radius: 8px;
            transition: all 0.2s;
        }
        .nav-btn-outline { color: var(--text-dark); border: 1px solid var(--border-color); background: #fff; }
        .nav-btn-primary { background: var(--primary); color: #fff; }
        
        .hero {
            background: linear-gradient(135deg, #fff 0%, var(--primary-light) 100%);
            padding: 36px 20px 24px; border-bottom: 1px solid var(--border-color); text-align: center;
        }
        .hero h1 { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; color: var(--secondary); margin-bottom: 8px; }
        .hero p { color: var(--text-muted); font-size: 15px; max-width: 600px; margin: 0 auto 20px; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }

        /* Search & Filter Bar */
        .filter-bar {
            background: #fff; border: 1px solid var(--border-color); border-radius: 14px; padding: 16px;
            margin-bottom: 24px; box-shadow: var(--card-shadow); display: flex; flex-wrap: wrap; gap: 12px; align-items: center;
        }
        .search-box { position: relative; flex: 1; min-width: 250px; }
        .search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .search-input {
            width: 100%; padding: 11px 14px 11px 40px; border: 1px solid var(--border-color); border-radius: 10px;
            font-size: 14px; outline: none; background: #fafafa;
        }
        .search-input:focus { border-color: var(--primary); background: #fff; }
        .select-filter {
            padding: 11px 14px; border: 1px solid var(--border-color); border-radius: 10px; font-size: 14px;
            background: #fff; color: var(--text-dark); outline: none; cursor: pointer;
        }
        
        /* Category Pills */
        .cat-pills { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 12px; margin-bottom: 24px; }
        .cat-pill {
            padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
            white-space: nowrap; border: 1px solid var(--border-color); background: #fff; color: var(--text-dark);
            transition: all 0.2s;
        }
        .cat-pill:hover, .cat-pill.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* Test Grid */
        .tests-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;
        }
        .test-card {
            background: #fff; border: 1px solid var(--border-color); border-radius: 16px; padding: 20px;
            box-shadow: var(--card-shadow); transition: all 0.2s ease; display: flex; flex-direction: column;
            justify-content: space-between; position: relative;
        }
        .test-card:hover { transform: translateY(-3px); box-shadow: var(--hover-shadow); border-color: #cbd5e1; }
        
        .test-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 10px; }
        .test-title { font-family: 'Syne', sans-serif; font-size: 17px; font-weight: 700; color: var(--secondary); line-height: 1.3; }
        .cat-badge {
            font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px;
            background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; white-space: nowrap;
        }
        
        .lab-name { font-size: 13px; color: var(--primary); font-weight: 600; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
        .test-desc { font-size: 13px; color: var(--text-muted); margin-bottom: 14px; line-height: 1.4; flex-grow: 1; }
        
        .meta-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; font-size: 12px; }
        .meta-item { display: inline-flex; align-items: center; gap: 4px; color: #475569; background: #f1f5f9; padding: 4px 8px; border-radius: 6px; }

        .pricing-section {
            display: flex; justify-content: space-between; align-items: center; padding-top: 14px;
            border-top: 1px dashed var(--border-color); margin-top: auto;
        }
        .price-box { display: flex; align-items: baseline; gap: 8px; }
        .effective-price { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; color: var(--secondary); }
        .mrp-price { font-size: 14px; text-decoration: line-through; color: #94a3b8; }
        .discount-tag { font-size: 11px; font-weight: 700; color: #16a34a; background: #dcfce7; padding: 2px 6px; border-radius: 4px; }
        
        .btn-book {
            padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none;
            background: var(--primary); color: #fff; border: none; cursor: pointer; transition: background 0.2s;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-book:hover { background: var(--primary-dark); }

        /* Booking Modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 9999;
            align-items: center; justify-content: center; padding: 16px;
        }
        .modal-content {
            background: #fff; width: 100%; max-width: 640px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            max-height: 92vh; overflow-y: auto; position: relative;
        }
        .modal-header {
            padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; background: #fff; z-index: 10;
        }
        .modal-header h3 { font-family: 'Syne', sans-serif; font-size: 18px; font-weight: 700; color: var(--secondary); margin: 0; }
        .btn-close { background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b; }
        .modal-body { padding: 24px; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control {
            width: 100%; padding: 11px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px;
            color: var(--text-dark); background: #fafafa; outline: none; font-family: inherit;
        }
        .form-control:focus { border-color: var(--primary); background: #fff; }

        .selected-test-banner {
            background: var(--primary-light); border: 1px solid #fbcfe8; border-radius: 12px; padding: 14px 16px;
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }

        .alert-error {
            background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px;
            margin-bottom: 16px; font-size: 13px;
        }
        .alert-success {
            background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px;
            margin-bottom: 16px; font-size: 13px;
        }
        
        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; gap: 0; }
            .hero h1 { font-size: 24px; }
            .tests-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <a href="{{ route('customer.lab.tests') }}" class="navbar-brand">
            <img src="{{ asset('image/medicto-logo.jpg.webp') }}" alt="Medicto Pharmacy Logo" onerror="this.style.display='none'">
            <span>Medicto Diagnostics</span>
        </a>
        <div class="nav-links">
            <a href="{{ route('customer.lab.my_bookings') }}" class="nav-btn nav-btn-outline">
                <i class="fa-solid fa-clock-rotate-left"></i> My Bookings
            </a>
            <a href="{{ route('dashboard') }}" class="nav-btn nav-btn-primary">
                <i class="fa-solid fa-lock"></i> Admin Panel
            </a>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="hero">
        <h1>Book Verified Lab Tests Online</h1>
        <p>Certified pathology laboratories, accurate home sample collection, and instant digital reports delivered to your Medicto App.</p>
    </section>

    <!-- MAIN CONTAINER -->
    <main class="container">

        @if(session('success'))
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
        @endif

        <!-- FILTER & SEARCH -->
        <div class="filter-bar">
            <form action="{{ route('customer.lab.tests') }}" method="GET" style="display:flex;flex:1;gap:12px;flex-wrap:wrap;align-items:center;">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search lab tests by name, code or condition..." class="search-input">
                </div>

                <select name="category_id" class="select-filter" onchange="this.form.submit()">
                    <option value="">All Test Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="lab_id" class="select-filter" onchange="this.form.submit()">
                    <option value="">All Diagnostic Labs</option>
                    @foreach($labs as $l)
                    <option value="{{ $l->id }}" {{ request('lab_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn-book" style="padding:11px 18px;">
                    <i class="fa-solid fa-sliders"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'category_id', 'lab_id']))
                <a href="{{ route('customer.lab.tests') }}" class="nav-btn nav-btn-outline" style="padding:11px 14px;">Reset</a>
                @endif
            </form>
        </div>

        <!-- CATEGORY PILLS -->
        <div class="cat-pills">
            <a href="{{ route('customer.lab.tests') }}" class="cat-pill {{ !request('category_id') ? 'active' : '' }}">
                All Tests
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('customer.lab.tests', ['category_id' => $cat->id]) }}" class="cat-pill {{ request('category_id') == $cat->id ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>

        <!-- TEST CARDS GRID -->
        <div class="tests-grid">
            @forelse($tests as $test)
            @php
                $mrp = (float)$test->price;
                $effective = $test->discount_price !== null ? (float)$test->discount_price : $mrp;
                $hasDiscount = ($test->discount_price !== null && $test->discount_price < $mrp);
                $discountPct = $hasDiscount && $mrp > 0 ? round((($mrp - $effective) / $mrp) * 100) : 0;
            @endphp
            <div class="test-card">
                <div>
                    <div class="test-header">
                        <h3 class="test-title">{{ $test->test_name }}</h3>
                        @if($test->category)
                        <span class="cat-badge">{{ $test->category->name }}</span>
                        @endif
                    </div>

                    <div class="lab-name">
                        <i class="fa-solid fa-hospital-user"></i> {{ $test->lab->name ?? 'Partner Diagnostic Lab' }}
                    </div>

                    <p class="test-desc">
                        {{ Str::limit($test->description ?? 'Accurate diagnostic test conducted by certified laboratory technicians with reliable computerized reporting.', 110) }}
                    </p>

                    <div class="meta-tags">
                        @if($test->sample_type)
                        <span class="meta-item"><i class="fa-solid fa-vial"></i> Sample: {{ $test->sample_type }}</span>
                        @endif
                        @if($test->report_delivery_time)
                        <span class="meta-item"><i class="fa-regular fa-clock"></i> Report: {{ $test->report_delivery_time }}</span>
                        @endif
                        @if($test->preparation_instructions)
                        <span class="meta-item" title="{{ $test->preparation_instructions }}"><i class="fa-solid fa-circle-info"></i> Fasting info</span>
                        @endif
                    </div>
                </div>

                <div class="pricing-section">
                    <div class="price-box">
                        <span class="effective-price">₹{{ number_format($effective, 2) }}</span>
                        @if($hasDiscount)
                        <span class="mrp-price">₹{{ number_format($mrp, 2) }}</span>
                        <span class="discount-tag">{{ $discountPct }}% OFF</span>
                        @endif
                    </div>

                    <button class="btn-book" onclick="openBookingModal({{ $test->id }}, '{{ addslashes($test->test_name) }}', '{{ addslashes($test->lab->name ?? 'Laboratory') }}', {{ $effective }})">
                        <i class="fa-solid fa-calendar-plus"></i> Book Now
                    </button>
                </div>
            </div>
            @empty
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;background:#fff;border-radius:16px;border:1px solid var(--border-color);">
                <i class="fa-solid fa-flask-vial" style="font-size:48px;color:#cbd5e1;margin-bottom:14px;"></i>
                <h3 style="font-family:'Syne',sans-serif;font-size:18px;color:var(--secondary);margin-bottom:6px;">No Lab Tests Found</h3>
                <p style="color:var(--text-muted);font-size:14px;">Try searching with a different test name or clearing filters.</p>
            </div>
            @endforelse
        </div>

        @if($tests->hasPages())
        <div style="margin-top:24px;">
            {{ $tests->links() }}
        </div>
        @endif

    </main>

    <!-- BOOKING MODAL -->
    <div class="modal-overlay" id="bookModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Schedule Lab Test Booking</h3>
                <button type="button" class="btn-close" onclick="closeBookingModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">
                <!-- Selected Test Summary Banner -->
                <div class="selected-test-banner">
                    <div>
                        <div id="mTestName" style="font-weight:700;color:var(--secondary);font-size:15px;">Selected Test</div>
                        <div id="mLabName" style="font-size:12px;color:var(--primary);margin-top:2px;">Laboratory Partner</div>
                    </div>
                    <div>
                        <div id="mPrice" style="font-family:'Syne',sans-serif;font-weight:800;font-size:20px;color:var(--primary);">₹0.00</div>
                    </div>
                </div>

                <!-- Booking Form -->
                <form id="labBookingForm" action="{{ route('customer.lab.book') }}" method="POST">
                    @csrf
                    <input type="hidden" name="lab_test_id" id="fLabTestId" value="">

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Patient / Customer Full Name *</label>
                            <input type="text" name="customer_name" required placeholder="e.g. Ramesh Kumar" class="form-control" value="{{ old('customer_name', auth()->user() ? auth()->user()->name : '') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mobile Number *</label>
                            <input type="tel" name="customer_phone" required placeholder="10-digit mobile number" class="form-control" value="{{ old('customer_phone', auth()->user() ? auth()->user()->phone : '') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address (Optional)</label>
                        <input type="email" name="customer_email" placeholder="patient@example.com" class="form-control" value="{{ old('customer_email', auth()->user() ? auth()->user()->email : '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Complete Address for Sample Collection *</label>
                        <textarea name="address" required rows="2" placeholder="House/Flat No., Building Name, Street / Landmark" class="form-control">{{ old('address') }}</textarea>
                    </div>

                    <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr;">
                        <div class="form-group">
                            <label class="form-label">City *</label>
                            <input type="text" name="city" required placeholder="City" class="form-control" value="{{ old('city', 'Mumbai') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" required placeholder="State" class="form-control" value="{{ old('state', 'Maharashtra') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">PIN Code *</label>
                            <input type="text" name="pincode" required placeholder="e.g. 400050" class="form-control" value="{{ old('pincode') }}">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Booking Date * (No Past Dates)</label>
                            <input type="date" name="booking_date" id="fBookingDate" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="form-control" onchange="loadAvailableSlots(this.value)">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Preferred Time Slot *</label>
                            <select name="booking_time" id="fBookingSlot" required class="form-control">
                                <option value="07:00 AM - 08:00 AM">07:00 AM - 08:00 AM (Early Fasting)</option>
                                <option value="08:00 AM - 09:00 AM" selected>08:00 AM - 09:00 AM (Morning Fasting)</option>
                                <option value="09:00 AM - 10:00 AM">09:00 AM - 10:00 AM</option>
                                <option value="10:00 AM - 11:00 AM">10:00 AM - 11:00 AM</option>
                                <option value="11:00 AM - 12:00 PM">11:00 AM - 12:00 PM</option>
                                <option value="02:00 PM - 03:00 PM">02:00 PM - 03:00 PM</option>
                                <option value="04:00 PM - 05:00 PM">04:00 PM - 05:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Additional Instructions / Landmarks (Optional)</label>
                        <input type="text" name="additional_instructions" placeholder="e.g. Ring bell twice, 2nd floor, patient is elderly" class="form-control" value="{{ old('additional_instructions') }}">
                    </div>

                    <div style="background:#f8fafc;padding:12px 14px;border-radius:10px;border:1px solid var(--border-color);margin-bottom:18px;display:flex;align-items:center;gap:10px;">
                        <i class="fa-solid fa-money-bill-wave" style="color:#16a34a;font-size:18px;"></i>
                        <div style="font-size:12px;color:#475569;">
                            <strong>Payment Option:</strong> Cash / UPI on Sample Collection. Free home collection included.
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:10px;">
                        <button type="button" class="nav-btn nav-btn-outline" onclick="closeBookingModal()">Cancel</button>
                        <button type="submit" class="btn-book" style="padding:12px 24px;font-size:14px;">
                            <i class="fa-solid fa-check"></i> Confirm &amp; Place Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openBookingModal(testId, testName, labName, price) {
            document.getElementById('fLabTestId').value = testId;
            document.getElementById('mTestName').innerText = testName;
            document.getElementById('mLabName').innerText = labName;
            document.getElementById('mPrice').innerText = '₹' + Number(price).toFixed(2);
            document.getElementById('bookModal').style.display = 'flex';
        }

        function closeBookingModal() {
            document.getElementById('bookModal').style.display = 'none';
        }

        function loadAvailableSlots(date) {
            if (!date) return;
            fetch('/api/lab-test-bookings/available-slots?date=' + date)
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.data && res.data.length > 0) {
                        const sel = document.getElementById('fBookingSlot');
                        sel.innerHTML = '';
                        res.data.forEach((slot, idx) => {
                            const opt = document.createElement('option');
                            opt.value = slot;
                            opt.text = slot;
                            if (idx === 0) opt.selected = true;
                            sel.appendChild(opt);
                        });
                    }
                })
                .catch(e => console.log('Slots fetch error:', e));
        }

        window.onclick = function(e) {
            const modal = document.getElementById('bookModal');
            if (e.target === modal) closeBookingModal();
        };
    </script>
</body>
</html>
