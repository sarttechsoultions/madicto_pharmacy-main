@extends('admin.layouts.layout')

@section('content')

<div class="o-details-content">

    <div class="o-details-header">

        <div>

            <div class="o-details-back">
                <a href="{{ route('medicine.index') }}" class="text-decoration-none">
                    ← Back to Medicines
                </a>
            </div>

            <div class="o-details-title">

                {{ $medicine->name }}

                <span class="o-details-badge">

                    {{ $medicine->status }}

                </span>

            </div>

            <div class="o-details-subtitle">

                {{ $medicine->manufacturer }}

                •

                {{ optional($medicine->category)->name }}

            </div>

        </div>


    </div>



    <div class="o-details-grid">




        <div>



            <div class="o-details-card">


                <div class="o-details-card-header">

                    <h3>Medicine Information</h3>

                </div>


                <div class="row">



                    <div class="col-md-4 text-center">

                        <img src="{{ asset($medicine->image) }}"
                            class="img-fluid rounded shadow"
                            style="max-height:250px;">


                        @if($medicine->medicine_image)

                        <div class="row mt-3">

                            @foreach(json_decode($medicine->medicine_image,true) as $img)

                            <div class="col-4 mb-2">

                                <img src="{{ asset($img) }}"
                                    class="img-fluid rounded border">

                            </div>

                            @endforeach

                        </div>

                        @endif

                    </div>




                    <div class="col-md-8">


                        <table class="table table-bordered">


                            <tr>

                                <th width="35%">Medicine Name</th>

                                <td>{{ $medicine->name }}</td>

                            </tr>


                            <tr>

                                <th>Category</th>

                                <td>

                                    {{ optional($medicine->category)->name }}

                                </td>

                            </tr>


                            <tr>

                                <th>Manufacturer</th>

                                <td>{{ $medicine->manufacturer }}</td>

                            </tr>


                            <tr>

                                <th>Batch No</th>

                                <td>{{ $medicine->batch_no }}</td>

                            </tr>


                            <tr>

                                <th>Unit Type</th>

                                <td>{{ $medicine->unit_type }}</td>

                            </tr>


                            <tr>

                                <th>Pack Size</th>

                                <td>{{ $medicine->pack_size }}</td>

                            </tr>


                            <tr>

                                <th>Price</th>

                                <td>

                                    ₹ {{ number_format($medicine->price,2) }}

                                </td>

                            </tr>


                            <tr>

                                <th>Discount</th>

                                <td>

                                    {{ $medicine->discount }} %

                                </td>

                            </tr>


                            <tr>

                                <th>Quantity</th>

                                <td>

                                    {{ $medicine->quantity }}

                                </td>

                            </tr>


                            <tr>

                                <th>Stock</th>

                                <td>

                                    {{ $medicine->stock }}

                                </td>

                            </tr>

                        </table>

                    </div>

                </div>

            </div>


        </div>

        <!-- Right Side -->

        <div>

            <!-- Inventory Card -->

            <div class="o-details-card">

                <div class="o-details-section">

                    <h3>Inventory Details</h3>

                    <div class="o-details-info">
                        <h5>Status</h5>
                        <p>
                            <span class="badge bg-success">
                                {{ $medicine->status }}
                            </span>
                        </p>
                    </div>

                    <div class="o-details-info">
                        <h5>Manufacture Date</h5>
                        <p>
                            {{ $medicine->manufacture_date ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="o-details-info">
                        <h5>Expiry Date</h5>
                        <p>
                            {{ $medicine->expiry_date ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="o-details-info">
                        <h5>Reorder Level</h5>
                        <p>
                            {{ $medicine->reorder_level }}
                        </p>
                    </div>

                    <div class="o-details-info">
                        <h5>Available Quantity</h5>
                        <p>
                            {{ $medicine->quantity }}
                        </p>
                    </div>

                    <div class="o-details-info">
                        <h5>Stock</h5>
                        <p>
                            {{ $medicine->stock }}
                        </p>
                    </div>

                    <div class="o-details-info">
                        <h5>Price</h5>
                        <p class="fw-bold text-success">
                            ₹ {{ number_format($medicine->price,2) }}
                        </p>
                    </div>

                </div>

            </div>

            <!-- Rating Summary -->

            <div class="o-details-card mt-4">

                <div class="o-details-section">

                    <h3>Rating Summary</h3>

                    <div class="text-center mb-3">

                        <h1 class="text-warning">

                            {{ number_format($averageRating,1) }}

                        </h1>

                        <div>

                            @for($i=1;$i<=5;$i++)

                                @if($i <=round($averageRating))

                                ⭐

                                @else

                                ☆

                                @endif

                                @endfor

                                </div>

                                <small>

                                    {{ $totalReviews }} Reviews

                                </small>

                        </div>

                        @php

                        $ratings=[
                        5=>$five,
                        4=>$four,
                        3=>$three,
                        2=>$two,
                        1=>$one
                        ];

                        @endphp

                        @foreach($ratings as $star=>$count)

                        <div class="mb-3">

                            <div class="d-flex justify-content-between">

                                <span>{{ $star }} ★</span>

                                <span>{{ $count }}</span>

                            </div>

                            <div class="progress">

                                <div class="progress-bar bg-warning"

                                    style="width:
                                {{ $totalReviews>0 ? ($count/$totalReviews)*100 :0 }}%">

                                </div>

                            </div>

                        </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

        <!-- Description -->

        <div class="o-details-card mt-4">

            <div class="o-details-section">

                <h3>Description</h3>

                <p>

                    {{ $medicine->description }}

                </p>

            </div>

        </div>

        <!-- Usage Instructions -->

        <div class="o-details-card mt-4">

            <div class="o-details-section">

                <h3>Usage Instructions</h3>

                <p>

                    {{ $medicine->usage_instructions }}

                </p>

            </div>

        </div>


        <!-- Customer Reviews -->

        <div class="o-details-card mt-4">

            <div class="o-details-card-header">

                <h3>Customer Reviews</h3>

                <span>{{ $totalReviews }} Reviews</span>

            </div>

            <div class="p-4">

                @forelse($medicine->reviews as $review)

                <div class="card mb-3 border-0 shadow-sm">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                                style="width:55px;height:55px;font-size:20px;">

                                {{ strtoupper(substr($review->coustmer->name ?? 'U',0,1)) }}

                            </div>

                            <div class="ms-3 flex-grow-1">

                                <h5 class="mb-1">

                                    {{ $review->coustmer->name ?? 'Unknown User' }}

                                </h5>

                                <small class="text-muted">

                                    {{ $review->created_at->format('d M Y h:i A') }}

                                </small>

                            </div>

                            <div>

                                @for($i=1;$i<=5;$i++)

                                    @if($i <=$review->rating)

                                    <span style="color:#ffc107;font-size:18px;">★</span>

                                    @else

                                    <span style="color:#ddd;font-size:18px;">★</span>

                                    @endif

                                    @endfor

                            </div>

                        </div>

                        @if($review->review)

                        <div class="mt-3">

                            <p class="mb-0">

                                {{ $review->review }}

                            </p>

                        </div>

                        @endif

                    </div>

                </div>

                @empty

                <div class="text-center py-5">

                    <img src="{{ asset('images/no-review.png') }}"
                        width="120"
                        class="mb-3">

                    <h5>No Reviews Available</h5>

                    <p class="text-muted">

                        No customer has reviewed this medicine yet.

                    </p>

                </div>

                @endforelse

            </div>

        </div>

    </div>

    <script src="{{ asset('js/main.js') }}"></script>

    @endsection