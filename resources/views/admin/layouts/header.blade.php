<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Medicto Pharmacy – Medicines Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/maim.css') }}">
    <link rel="stylesheet" href="{{ asset('css/category.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order-detaails.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/banner.css') }}">
    <link rel="stylesheet" href="{{ asset('css/coustmer-medicine.css') }}">
    <link rel="stylesheet" href="{{ asset('css/setting.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/review.css') }}">

</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            <div class="logo-text">
                <h2>MEDICTO™<br>PHARMACY</h2>
                <span>Health Management</span>
            </div>
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
                <span class="nav-badge">0</span>
            </a>

            <a class="nav-item {{ request()->routeIs('category.index') ? 'active' : '' }}"
                href="{{ route('category.index') }}">

                <i class="fa-solid fa-tags"></i>
                <span>Categories</span>
            </a>
            <a class="nav-item {{ request()->routeIs('orders.index') ? 'active' : '' }}"
                href="{{ route('orders.index') }}">
                <i class="fa-solid fa-bag-shopping"></i><span>Orders</span>
                <span class="nav-badge">8</span>
            </a>

            <div class="nav-label" style="margin-top:10px;">Management</div>
            <a class="nav-item {{ request()->routeIs('user.index') ? 'active' : '' }}" href="{{ route('user.index') }}">
                <i class="fa-solid fa-users"></i><span>Users</span>
            </a>
            <a class="nav-item" href="#">
                <i class="fa-solid fa-boxes-stacked"></i><span>Inventory</span>
            </a>
            <a class="nav-item" href="#">
                <i class="fa-solid fa-credit-card"></i><span>Payments</span>
            </a>
            <a class="nav-item {{ request()->routeIs('banner.index') ? 'active' : '' }}" href="{{ route('banner.index') }}">
                <i class="fa-solid fa-rectangle-ad"></i><span>Banners</span>
            </a>

            <div class="nav-label" style="margin-top:10px;">Others</div>
            <a class="nav-item" href="#">
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
                <button class="icon-btn" title="Notifications">
                    <i class="fa-solid fa-bell"></i>
                    <span class="notif-dot"></span>
                </button>
                <button class="icon-btn" title="Help" onclick="showToast('Help docs opening…')">
                    <i class="fa-regular fa-circle-question"></i>
                </button>
                <a href="{{ route('settings.profile') }}">
                    <div class="profile-pill">
                        <div class="avatar">SA</div>
                        <div class="profile-info">
                            <strong>Admin Profile</strong>
                            <small>Super Admin</small>
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

        @if ($errors->any())
        <div class="custom-alert error">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif