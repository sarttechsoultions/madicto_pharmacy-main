@extends('admin.layouts.layout')

@section('content')


<!-- CONTENT -->
<div class="content">
    <div class="content-left">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Dashboard Overview</h1>
                <p>Real-time clinical performance and inventory metrics.</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline">⬇ Export Report</button>
                <button class="btn btn-primary">＋ Add New Order</button>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon-wrap">🛒</div>
                    <span class="stat-badge badge-up">+12.5%</span>
                </div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">1,284</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon-wrap">💰</div>
                    <span class="stat-badge badge-up">+8.2%</span>
                </div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">$42,920</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon-wrap">👤</div>
                    <span class="stat-badge badge-down">-2.4%</span>
                </div>
                <div class="stat-label">Active Users</div>
                <div class="stat-value">3,502</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon-wrap">💊</div>
                    <span class="stat-badge badge-neutral">Static</span>
                </div>
                <div class="stat-label">Medicines</div>
                <div class="stat-value">842</div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">Revenue Trends</div>
                <div class="toggle-group">
                    <button class="toggle-btn active">Weekly</button>
                    <button class="toggle-btn">Monthly</button>
                </div>
            </div>
            <div class="chart-area">
                <svg class="chart-svg" viewBox="0 0 760 180" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#E91E8C" stop-opacity="0.15" />
                            <stop offset="100%" stop-color="#E91E8C" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <!-- Grid lines -->
                    <line x1="0" y1="30" x2="760" y2="30" stroke="#EEEEF4" stroke-width="1" />
                    <line x1="0" y1="80" x2="760" y2="80" stroke="#EEEEF4" stroke-width="1" />
                    <line x1="0" y1="130" x2="760" y2="130" stroke="#EEEEF4" stroke-width="1" />
                    <!-- Fill -->
                    <path d="M 0 160 C 60 155, 110 140, 150 120 C 200 95, 240 85, 300 60 C 360 35, 400 32, 450 38 C 500 43, 530 50, 580 44 C 630 37, 700 25, 760 18 L 760 180 L 0 180 Z"
                        fill="url(#chartGrad)" />
                    <!-- Line -->
                    <path d="M 0 160 C 60 155, 110 140, 150 120 C 200 95, 240 85, 300 60 C 360 35, 400 32, 450 38 C 500 43, 530 50, 580 44 C 630 37, 700 25, 760 18"
                        fill="none" stroke="#E91E8C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    <!-- Dots -->
                    <circle cx="0" cy="160" r="5" fill="#E91E8C" />
                    <circle cx="150" cy="120" r="5" fill="#E91E8C" />
                    <circle cx="300" cy="60" r="5" fill="#E91E8C" />
                    <circle cx="450" cy="38" r="5" fill="#E91E8C" />
                    <circle cx="580" cy="44" r="5" fill="#E91E8C" />
                    <circle cx="760" cy="18" r="5" fill="#E91E8C" />
                </svg>
            </div>
            <div class="x-labels">
                <span class="x-label">MON</span>
                <span class="x-label">TUE</span>
                <span class="x-label">WED</span>
                <span class="x-label">THU</span>
                <span class="x-label">FRI</span>
                <span class="x-label">SAT</span>
                <span class="x-label">SUN</span>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="orders-card">
            <div class="orders-header">
                <div class="orders-title">Recent Orders</div>
                <a class="view-all" href="#">View All</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient Name</th>
                        <th>Medicine</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="order-id">#MF-8291</span></td>
                        <td><span class="patient-name">Sarah Johnson</span></td>
                        <td><span class="medicine-name">Amoxicillin 500mg</span></td>
                        <td><span class="status-badge status-shipped">Shipped</span></td>
                        <td><span class="total-amount">$42.00</span></td>
                    </tr>
                    <tr>
                        <td><span class="order-id">#MF-8290</span></td>
                        <td><span class="patient-name">Robert Smith</span></td>
                        <td><span class="medicine-name">Lisinopril 10mg</span></td>
                        <td><span class="status-badge status-processing">Processing</span></td>
                        <td><span class="total-amount">$15.50</span></td>
                    </tr>
                    <tr>
                        <td><span class="order-id">#MF-8289</span></td>
                        <td><span class="patient-name">Elena Gomez</span></td>
                        <td><span class="medicine-name">Metformin 500mg</span></td>
                        <td><span class="status-badge status-cancelled">Cancelled</span></td>
                        <td><span class="total-amount">$8.75</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div><!-- /content-left -->

    <!-- ── RIGHT PANEL ── -->
    <div class="content-right">

        <!-- Low Stock Alerts -->
        <div class="panel-card">
            <div class="panel-title">
                <span class="alert-icon">⚠️</span> Low Stock Alerts
            </div>
            <div class="stock-item">
                <div>
                    <div class="stock-name">Paracetamol 500mg</div>
                    <div class="stock-qty">12 units remaining</div>
                </div>
                <div class="stock-dot"></div>
            </div>
            <div class="stock-item">
                <div>
                    <div class="stock-name">Ibuprofen 400mg</div>
                    <div class="stock-qty">5 units remaining</div>
                </div>
                <div class="stock-dot"></div>
            </div>
            <button class="btn-restock">Restock All</button>
        </div>

        <!-- Top Selling -->
        <div class="panel-card">
            <div class="panel-title">Top Selling Medicines</div>
            <div class="med-item">
                <div class="med-img">💊</div>
                <div class="med-info">
                    <div class="med-name">Amoxicillin 500mg</div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:85%"></div>
                    </div>
                </div>
                <div class="med-pct">85%</div>
            </div>
            <div class="med-item">
                <div class="med-img">🔬</div>
                <div class="med-info">
                    <div class="med-name">Atorvastatin 20mg</div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:72%"></div>
                    </div>
                </div>
                <div class="med-pct">72%</div>
            </div>
            <div class="med-item" style="margin-bottom:0">
                <div class="med-img">☀️</div>
                <div class="med-info">
                    <div class="med-name">Vitamin D3</div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:64%"></div>
                    </div>
                </div>
                <div class="med-pct">64%</div>
            </div>
        </div>

        <!-- Help -->
        <div class="help-card">
            <div class="help-title">Need Help?</div>
            <div class="help-text">Access our technical documentation or contact system support.</div>
            <button class="btn-help">Open Support Portal</button>
        </div>

    </div><!-- /content-right -->
</div><!-- /content -->
</div><!-- /main -->

</body>

</html>

@endsection