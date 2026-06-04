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
                    <!-- <span class="stat-badge badge-up">+12.5%</span> -->
                </div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ number_format($totalorders) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon-wrap">💊</div>
                    <!-- <span class="stat-badge badge-up">+8.2%</span> -->
                </div>
                <div class="stat-label">Coustmer Medicine</div>
                <div class="stat-value">{{ number_format($totalcoustmermedicine) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon-wrap">👤</div>
                    <!-- <span class="stat-badge badge-down">-2.4%</span> -->
                </div>
                <div class="stat-label">Active Users</div>
                <div class="stat-value">{{ number_format($useractive) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon-wrap">💊</div>
                    <span class="stat-badge badge-neutral">Static</span>
                </div>
                <div class="stat-label">Medicines</div>
                <div class="stat-value">{{ number_format($totalmedicine) }}</div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="chart-card">

            <div class="chart-header">

                <div class="chart-title">
                    Revenue Trends
                </div>

                <div class="toggle-group">

                    <button class="toggle-btn active"
                        onclick="showChart('weekly')">
                        Weekly
                    </button>

                    <button class="toggle-btn"
                        onclick="showChart('monthly')">
                        Monthly
                    </button>

                </div>

            </div>

            <div class="chart-area">

                <canvas id="revenueChart"></canvas>

            </div>

        </div>

        <!-- Recent Orders -->
        <div class="orders-card">
            <div class="orders-header">
                <div class="orders-title">Recent Orders</div>
                <a class="view-all" href="{{ route('orders.index') }}">View All</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient Name</th>
                        <th>Medicine</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentorders as $order)
                    <tr>
                        <td><span class="order-id">{{ $order->order_id }}</span></td>
                        <td><span class="patient-name">{{ $order->user->name ?? 'N/A' }}</span></td>
                        <td><span class="medicine-name">@foreach($order->items->take(2) as $item)

                                {{ $item->medicine_name }}

                                <br>

                                @endforeach

                                @if($order->items->count() > 2)

                                +{{ $order->items->count() - 2 }} more

                                @endif</span></td>
                        <td><span class="status-badge {{ $order->status === 'Shipped' ? 'status-shipped' : ($order->status === 'Pending' ? 'status-pending' : 'status-cancelled') }}">{{ $order->status ?? 'N/A' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div><!-- /content-left -->
    <br>

    <br>

    <!-- ── RIGHT PANEL ── -->
    <div class="content-right">

        <!-- Low Stock Alerts -->
        <div class="panel-card">

            <div class="panel-title">
                <span class="alert-icon">⚠️</span>
                Low Stock Alerts
            </div>

            @forelse($lowstockmedicines as $medicine)

            <div class="stock-item">

                <div>

                    <div class="stock-name">
                        {{ $medicine->name }}
                    </div>

                    <div class="stock-qty">
                        {{ $medicine->quantity }} units remaining
                    </div>

                </div>

                <div class="stock-dot"></div>

            </div>

            @empty

            <div class="stock-item">

                <div>

                    <div class="stock-name">
                        No low stock medicines
                    </div>

                    <div class="stock-qty">
                        Inventory levels are good
                    </div>

                </div>

            </div>

            @endforelse

            <button class="btn-restock">
                Restock All
            </button>

        </div>

        <!-- Top Selling -->
        <div class="panel-card">

            <div class="panel-title">
                Top Selling Medicines
            </div>

            @forelse($topSellingMedicines as $item)

            <div class="med-item">

                <div class="med-img">💊</div>

                <div class="med-info">

                    <div class="med-name">
                        {{ $item->medicine_name }}
                    </div>

                    <div class="progress-bar-bg">

                        <div class="progress-bar-fill"
                            style="width:{{ round($item->percentage) }}%">
                        </div>

                    </div>

                </div>

                <div class="med-pct">
                    {{ round($item->percentage) }}%
                </div>

            </div>

            @empty

            <div class="med-item">

                <div class="med-info">

                    <div class="med-name">
                        No sales data found
                    </div>

                </div>

            </div>

            @endforelse

        </div>
    </div><!-- /content-left -->
</div><!-- /content -->
</div><!-- /main -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/main.js') }}"></script>

<script>
    const weeklyLabels = @json($weeklyLabels);

    const weeklyData = @json($weeklyData);

    const monthlyLabels = @json($monthlyLabels);

    const monthlyData = @json($monthlyData);

    const ctx = document.getElementById('revenueChart');

    const revenueChart = new Chart(ctx, {

        type: 'line',

        data: {

            labels: weeklyLabels,

            datasets: [{

                label: 'Revenue',

                data: weeklyData,

                borderColor: '#E91E8C',

                backgroundColor: 'rgba(233,30,140,0.1)',

                fill: true,

                tension: 0.4,

                pointRadius: 4,

                pointBackgroundColor: '#E91E8C'

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {
                    display: false
                }

            },

            scales: {

                y: {
                    beginAtZero: true
                }

            }

        }

    });

    function showChart(type) {

        document.querySelectorAll('.toggle-btn')
            .forEach(btn => btn.classList.remove('active'));

        if (type === 'weekly') {

            document.querySelectorAll('.toggle-btn')[0]
                .classList.add('active');

            revenueChart.data.labels = weeklyLabels;

            revenueChart.data.datasets[0].data = weeklyData;

        } else {

            document.querySelectorAll('.toggle-btn')[1]
                .classList.add('active');

            revenueChart.data.labels = monthlyLabels;

            revenueChart.data.datasets[0].data = monthlyData;

        }

        revenueChart.update();
    }
</script>

</body>

</html>

@endsection