<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Medicto Pharmacy – Medicines Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/article-header.css') }}">

    <style>
        body, input, select, textarea, button, table, th, td, h1, h2, h3, h4, h5, h6, .nav-item, .page-header, .btn-primary {
            font-family: 'Poppins', sans-serif !important;
        }
    </style>

    <link rel="icon" type="image/png" href="{{ asset('image/favicon.webp') }}" />


</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('image/medicto-logo.jpg.webp') }}" alt="Medicto Logo" class="logo-img" />
            </a>
            <button class="sidebar-close-btn" id="sidebarCloseBtn" title="Collapse sidebar">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Main</div>
            <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
            </a>
            <a class="nav-item {{ request()->routeIs('medicine.index') ? 'active' : '' }}"
                href="{{ route('medicine.index') }}">

                <i class="fa-solid fa-pills"></i>
                <span>Medicines</span>
            </a>

            <a class="nav-item {{ request()->routeIs('category.index') ? 'active' : '' }}"
                href="{{ route('category.index') }}">

                <i class="fa-solid fa-tags"></i>
                <span>Categories</span>
            </a>
            <a class="nav-item {{ request()->routeIs('orders.index') ? 'active' : '' }}"
                href="{{ route('orders.index') }}">
                <i class="fa-solid fa-bag-shopping"></i><span>Orders</span>
            </a>

            <div class="nav-label" style="margin-top:10px;">Lab Test Management</div>
            <a class="nav-item {{ request()->routeIs('admin.lab.tests*') ? 'active' : '' }}" href="{{ route('admin.lab.tests') }}">
                <i class="fa-solid fa-vials"></i><span>Lab Tests Catalog</span>
            </a>
            <a class="nav-item {{ request()->routeIs('admin.lab.bookings.dashboard') || request()->routeIs('admin.lab.dashboard') ? 'active' : '' }}" href="{{ route('admin.lab.bookings.dashboard') }}">
                <i class="fa-solid fa-chart-pie"></i><span>Lab Test Bookings</span>
            </a>
            <a class="nav-item {{ request()->routeIs('admin.lab.bookings.index') || request()->routeIs('admin.lab.bookings.show') || request()->routeIs('admin.lab.orders') ? 'active' : '' }}" href="{{ route('admin.lab.bookings.index') }}">
                <i class="fa-solid fa-list-check"></i><span>All Bookings</span>
            </a>
            <a class="nav-item {{ request()->routeIs('admin.lab.bookings.calendar') ? 'active' : '' }}" href="{{ route('admin.lab.bookings.calendar') }}">
                <i class="fa-solid fa-calendar-days"></i><span>Booking Calendar</span>
            </a>
            <a class="nav-item {{ request()->routeIs('admin.lab.bookings.reports') ? 'active' : '' }}" href="{{ route('admin.lab.bookings.reports') }}">
                <i class="fa-solid fa-file-invoice-dollar"></i><span>Booking Reports</span>
            </a>

            <div class="nav-label" style="margin-top:10px;">Management</div>
            <a class="nav-item {{ request()->routeIs('user.index') ? 'active' : '' }}" href="{{ route('user.index') }}">
                <i class="fa-solid fa-users"></i><span>Users</span>
                <span class="nav-badge">{{ \App\Models\User::count() }}</span>
            </a>
            <a class="nav-item" href="#">
                <i class="fa-solid fa-boxes-stacked"></i><span>Inventory</span>
            </a>
            <a class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
                <i class="fa-solid fa-credit-card"></i><span>Payments</span>
            </a>
            <a class="nav-item {{ request()->routeIs('banner.index') ? 'active' : '' }}" href="{{ route('banner.index') }}">
                <i class="fa-solid fa-rectangle-ad"></i><span>Banners</span>
            </a>

            <div class="nav-label" style="margin-top:10px;">Others</div>
            <a class="nav-item {{ request()->routeIs('notifications') ? 'active' : ''  }}" href="{{ route('notifications') }}">
                <i class="fa-solid fa-bell"></i><span>Notifications</span>
            </a>
            <a class="nav-item {{ request()->routeIs('reviews.index') ? 'active' : '' }}" href="{{ route('reviews.index') }}">
                <i class="fa-solid fa-star"></i><span>Reviews</span>
            </a>
            <a class="nav-item {{ request()->routeIs('coustmer.medicine.index') ? 'active' : '' }}" href="{{ route('coustmer.medicine.index') }}">
                <i class="fa-solid fa-chart-bar"></i><span>Coustmer Medicines</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a class="nav-item" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </aside>

    <!-- OVERLAY (mobile) -->
    <div class="overlay" id="overlay"></div>

    <!-- MAIN -->
    <div class="main-wrap" id="mainWrap">

        <!-- TOPBAR -->
        <header class="topbar">
            <button class="topbar-toggle" id="sidebarToggle" title="Toggle sidebar">
                <i class="fa-solid fa-bars" id="toggleIcon"></i>
            </button>

            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search medicines, batches, or SKU…" oninput="globalSearch(this.value)" />
            </div>

            <div class="topbar-actions">
                @php
                    $adminTodayNotifs = \App\Models\notification::whereIn('target_role', ['admin', 'all'])
                        ->whereDate('created_at', today())
                        ->count();
                @endphp
                <a href="{{ route('notifications') }}" class="icon-btn" title="Notification Center ({{ $adminTodayNotifs }} today)" style="position:relative; text-decoration:none;">
                    <i class="fa-solid fa-bell"></i>
                    @if($adminTodayNotifs > 0)
                        <span class="notif-dot" style="position:absolute; top:2px; right:2px; background:#e11d48; color:#fff; font-size:10px; font-weight:700; border-radius:10px; min-width:16px; height:16px; display:inline-flex; align-items:center; justify-content:center; padding:0 3px;">{{ $adminTodayNotifs }}</span>
                    @endif
                </a>
                <button class="icon-btn" title="Help" onclick="showToast('Help docs opening…')">
                    <i class="fa-regular fa-circle-question"></i>
                </button>
                <a href="{{ route('settings.profile') }}">
                    <div class="profile-pill">
                        <div class="avatar">{{ Auth::check() ? (Auth::user()->name[0] ?? 'A') : 'A' }}</div>
                        <div class="profile-info">
                            <strong>{{ Auth::check() ? (Auth::user()->name ?? 'Admin User') : 'Admin' }}</strong>
                            <small>{{ Auth::check() ? (Auth::user()->email ?? 'admin@medicto.com') : 'admin@medicto.com' }}</small>
                        </div>
                    </div>
                </a>
            </div>
        </header>


        @if(session('success'))
        <div class="custom-alert success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="custom-alert error">
            {{ session('error') }}
        </div>
        @endif

        @if(isset($errors) && $errors->any())
        <div class="custom-alert error">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif