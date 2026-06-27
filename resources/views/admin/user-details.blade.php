@extends('admin.layouts.layout')

@section('content')

<div class="o-details-content">

    <div class="o-details-header">

        <div>

            <div class="o-details-back">

                <a href="{{ route('user.index') }}">

                    ← Back to Users

                </a>

            </div>

            <div class="o-details-title">

                {{ $user->name }}

                <span class="o-details-badge">

                    {{ $user->status }}

                </span>

            </div>

            <div class="o-details-subtitle">

                {{ $user->email }}

            </div>

        </div>

    </div>

    <div class="o-details-grid">

        <div>

            <div class="o-details-card">

                <div class="o-details-card-header">

                    <h3>User Information</h3>

                </div>

                <div class="row">

                    <div class="col-md-4 text-center">

                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center mx-auto"

                            style="width:130px;height:130px;font-size:48px;">

                            {{ strtoupper(substr($user->name,0,1)) }}

                        </div>

                        <h4 class="mt-3">

                            {{ $user->name }}

                        </h4>

                        <p>

                            {{ $user->email }}

                        </p>

                    </div>

                    <div class="col-md-8">

                        <table class="table table-bordered">

                            <tr>

                                <th>Name</th>

                                <td>{{ $user->name }}</td>

                            </tr>

                            <tr>

                                <th>Email</th>

                                <td>{{ $user->email }}</td>

                            </tr>

                            <tr>

                                <th>Phone</th>

                                <td>{{ $user->number }}</td>

                            </tr>

                            <tr>

                                <th>Status</th>

                                <td>{{ $user->status }}</td>

                            </tr>

                            <tr>

                                <th>Wallet</th>

                                <td>

                                    ₹ {{ number_format($user->wallet,2) }}

                                </td>

                            </tr>

                            <tr>

                                <th>Joined</th>

                                <td>

                                    {{ $user->created_at->format('d M Y') }}

                                </td>

                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT SIDE -->

        <div>

            <!-- Statistics -->

            <div class="o-details-card">

                <div class="o-details-section">

                    <h3>Account Statistics</h3>

                    <div class="row g-3">

                        <div class="col-6">

                            <div class="card border-0 shadow-sm">

                                <div class="card-body text-center">

                                    <h3 class="text-primary">

                                        {{ $totalOrders }}

                                    </h3>

                                    <small>Total Orders</small>

                                </div>

                            </div>

                        </div>

                        <div class="col-6">

                            <div class="card border-0 shadow-sm">

                                <div class="card-body text-center">

                                    <h3 class="text-success">

                                        {{ $completedOrders }}

                                    </h3>

                                    <small>Completed</small>

                                </div>

                            </div>

                        </div>

                        <div class="col-6">

                            <div class="card border-0 shadow-sm">

                                <div class="card-body text-center">

                                    <h3 class="text-warning">

                                        {{ $pendingOrders }}

                                    </h3>

                                    <small>Pending</small>

                                </div>

                            </div>

                        </div>

                        <div class="col-6">

                            <div class="card border-0 shadow-sm">

                                <div class="card-body text-center">

                                    <h3 class="text-danger">

                                        {{ $cancelledOrders }}

                                    </h3>

                                    <small>Cancelled</small>

                                </div>

                            </div>

                        </div>

                    </div>

                    <hr>

                    <div class="o-details-info">

                        <h5>Total Spent</h5>

                        <p class="fw-bold text-success">

                            ₹ {{ number_format($totalSpent,2) }}

                        </p>

                    </div>

                    <div class="o-details-info">

                        <h5>Wallet Balance</h5>

                        <p>

                            ₹ {{ number_format($user->wallet,2) }}

                        </p>

                    </div>

                </div>

            </div>

            <!-- Addresses -->

            <div class="o-details-card mt-4">

                <div class="o-details-section">

                    <h3>Saved Addresses</h3>

                    @forelse($user->addresses as $address)

                    <div class="border rounded p-3 mb-3">

                        <strong>

                            {{ $address->full_name }}

                        </strong>

                        <br>

                        {{ $address->phone_number }}

                        <br><br>

                        {{ $address->street_address }}

                        <br>

                        {{ $address->landmark }}

                        <br>

                        {{ $address->city }}

                        ,

                        {{ $address->state }}

                        -

                        {{ $address->pin_code }}

                    </div>

                    @empty

                    <div class="text-muted">

                        No Address Found

                    </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

    <!-- Recent Orders -->

    <div class="o-details-card mt-4">

        <div class="o-details-card-header">

            <h3>Recent Orders</h3>

            <span>

                {{ $totalOrders }} Orders

            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>Order ID</th>

                        <th>Date</th>

                        <th>Status</th>

                        <th>Payment</th>

                        <th>Total</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($user->orders as $order)

                    <tr>

                        <td>

                            #{{ $order->order_id }}

                        </td>

                        <td>

                            {{ $order->created_at->format('d M Y') }}

                        </td>

                        <td>

                            <span class="badge bg-primary">

                                {{ $order->status }}

                            </span>

                        </td>

                        <td>

                            {{ $order->payment_status }}

                        </td>

                        <td>

                            ₹ {{ number_format($order->total_amount,2) }}

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="5" class="text-center">

                            No Orders Found

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <!-- Customer Reviews -->

    <div class="o-details-card mt-4">

        <div class="o-details-card-header">

            <h3>Customer Reviews</h3>

            <span>{{ $user->reviews->count() }} Reviews</span>

        </div>

        <div class="p-4">

            @forelse($user->reviews as $review)

            <div class="card shadow-sm border-0 mb-3">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h5 class="mb-1">

                                {{ $review->medicine->name ?? 'Medicine Deleted' }}

                            </h5>

                            <small class="text-muted">

                                {{ $review->created_at->format('d M Y h:i A') }}

                            </small>

                        </div>

                        <div>

                            @for($i=1;$i<=5;$i++)

                                @if($i <=$review->rating)

                                <span class="text-warning fs-5">★</span>

                                @else

                                <span class="text-secondary fs-5">☆</span>

                                @endif

                                @endfor

                        </div>

                    </div>

                    @if($review->review)

                    <div class="mt-3">

                        {{ $review->review }}

                    </div>

                    @endif

                </div>

            </div>

            @empty

            <div class="text-center py-5">

                <h5>No Reviews Available</h5>

            </div>

            @endforelse

        </div>

    </div>


    <!-- Favourite Medicines -->

    <div class="o-details-card mt-4">

        <div class="o-details-card-header">

            <h3>Favourite Medicines</h3>

            <span>{{ $user->favorites->count() }}</span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>Image</th>

                        <th>Name</th>

                        <th>Price</th>

                        <th>Stock</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($user->favorites as $fav)

                    <tr>

                        <td width="80">

                            <img src="{{ asset(optional($fav->medicine)->image ?? 'images/no-image.png') }}"
                                width="60"
                                class="rounded">

                        </td>

                        <td>

                            {{ $fav->medicine->name ?? 'N/A' }}

                        </td>

                        <td>

                            ₹ {{ $fav->medicine ? number_format($fav->medicine->price, 2) : 'N/A' }}

                        </td>

                        <td>

                            {{ $fav->medicine->status ?? 'N/A' }}

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="4" class="text-center">

                            No Favourite Medicines

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>



    <!-- Recently Ordered Medicines -->

    <div class="o-details-card mt-4">

        <div class="o-details-card-header">

            <h3>Recently Ordered Medicines</h3>

        </div>

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Image</th>

                        <th>Medicine</th>

                        <th>Qty</th>

                        <th>Price</th>

                        <th>Order</th>

                    </tr>

                </thead>

                <tbody>

                    @php

                    $items = collect();

                    foreach($user->orders as $order){

                    foreach($order->items as $item){

                    $items->push($item);

                    }

                    }

                    @endphp

                    @forelse($items->take(10) as $item)

                    <tr>

                        <td width="80">

                            <img src="{{ asset('uploads/medicine/'.$item->medicine_image) }}"
                                width="60"
                                class="rounded">

                        </td>

                        <td>

                            {{ $item->medicine_name }}

                        </td>

                        <td>

                            {{ $item->quantity }}

                        </td>

                        <td>

                            ₹ {{ number_format($item->price,2) }}

                        </td>

                        <td>

                            #{{ $item->order_id }}

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="5" class="text-center">

                            No Medicines Ordered

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection