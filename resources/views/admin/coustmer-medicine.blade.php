@extends('admin.layouts.layout')

@section('content')

<div class="oder-c-content">

    <h1 class="oder-c-title">
        Customer Medicine Orders
    </h1>

    <!-- ORDER GRID -->

    <!-- <div class="oder-c-orders-grid">

        @foreach ($cstmedcine as $item)

        <div class="oder-c-order-card">


            <div class="oder-c-order-header">

                <div class="oder-c-order-id">
                    #CST-{{ $item->coustmer->id }}
                </div>

                <div class="oder-c-status">
                    Ready
                </div>

            </div>


            <p>
                <strong>Customer:</strong>
                {{ $item->coustmer->name ?? 'N/A' }}
            </p>


            <div class="oder-c-doctor-slip-box">

                <img
                    src="{{ asset('uploads/cstmedicine/' . $item->img) }}"
                    class="oder-c-doctor-slip"
                    onclick="oderCOpenImage(this.src)">

                <div class="oder-c-hover-preview">

                    <img
                        src="{{ asset('uploads/cstmedicine/' . $item->img) }}">

                </div>

            </div>


            <div class="oder-c-medicine-section">


                <div class="oder-c-dropdown-wrapper">

                    <button
                        type="button"
                        class="oder-c-dropdown-btn"
                        onclick="toggleMedicineDropdown({{ $item->id }})">

                        Select Medicines

                    </button>


                    <div
                        class="oder-c-dropdown-list"
                        id="oder-c-dropdown-list-{{ $item->id }}">

                        @foreach ($medicine as $med)

                        <div class="oder-c-medicine-option">


                            <input
                                type="checkbox"
                                class="oder-c-checkbox"
                                id="medicine-{{ $item->id }}-{{ $med->id }}"
                                value="{{ $med->id }}"

                                data-name="{{ $med->name }}"
                                data-price="{{ $med->price }}"
                                data-company="{{ $med->company }}"
                                data-stock="{{ $med->stock }}"
                                data-description="{{ $med->description }}"

                                onchange="oderCShowSelectedMedicine(
                                    {{ $item->id }}
                                )">


                            <label
                                for="medicine-{{ $item->id }}-{{ $med->id }}">

                                {{ $med->name }}

                                —
                                ₹{{ $med->price }}

                            </label>


                            <input
                                type="number"
                                min="1"
                                value="1"
                                class="oder-c-qty-input"
                                id="qty-{{ $item->id }}-{{ $med->id }}">

                        </div>

                        @endforeach

                    </div>

                </div>


                <div
                    class="oder-c-medicine-details"
                    id="oder-c-details-{{ $item->id }}">

                    <p>
                        Selected medicines details will appear here
                    </p>

                </div>


                <button
                    class="oder-c-btn"
                    onclick="oderCAddToCart(
                        {{ $item->coustmer->id }},
                        {{ $item->id }}
                    )">

                    Add To Cart

                </button>

            </div>

        </div>

        @endforeach

    </div> -->

    <!-- CART TABLE -->

    <div class="oder-c-cart-box">

        <h2 class="oder-c-cart-title">
            Medicine Cart
        </h2>

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>#</th>

                    <th>Image</th>

                    <th>Customer</th>

                    <th>Number</th>

                    <th>Address</th>

                    <th>DATE & TIME</th>

                </tr>

            </thead>

            <tbody>

                @forelse ($cstmedcine as $cart)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        <div class="med-thumb">
                            <img
                                src="{{ asset('uploads/cstmedicine/' . $cart->img) }}"
                                alt="Medicine Image"
                                style="cursor:pointer"
                                onclick="openImage(this.src)">
                        </div>
                    </td>

                    <td>

                        <strong>
                            {{ $cart->coustmer->name ?? 'N/A' }}
                        </strong>

                        <br>

                        {{ $cart->coustmer->email ?? '' }}

                    </td>

                    <td>

                        {{ $cart->coustmer->number ?? 'N/A' }}

                    </td>

                    <td>

                        {{ $cart->coustmer->address ?? 'N/A' }}

                    </td>

                    <td>

                        {{ $cart->created_at->format('d M, Y h:i A') }}

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="7" class="text-center">

                        No Medicines Added Yet

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>
<style>

</style>

<div id="imgModal" class="img-modal">
    <span class="close-btn" onclick="closeImage()">&times;</span>
    <img id="modalImg" class="modal-img">
</div>

<!-- IMAGE MODAL -->

<div id="oder-c-image-modal">

    <span
        id="oder-c-close-modal"
        onclick="oderCCloseImage()">

        ×

    </span>

    <img id="oder-c-modal-image">

</div>

<!-- SCRIPT -->

<script>
    /* DROPDOWN */

    function toggleMedicineDropdown(id) {

        let dropdown = document.getElementById(
            'oder-c-dropdown-list-' + id
        );

        if (dropdown.style.display === 'block') {

            dropdown.style.display = 'none';

        } else {

            dropdown.style.display = 'block';

        }

    }

    /* SHOW DETAILS */

    function oderCShowSelectedMedicine(cardId) {

        let details = document.getElementById(
            'oder-c-details-' + cardId
        );

        let checkboxes = document.querySelectorAll(
            '#oder-c-dropdown-list-' + cardId + ' .oder-c-checkbox'
        );

        let html = '';

        checkboxes.forEach((checkbox) => {

            if (checkbox.checked) {

                html += `

                    <div class="mb-3 p-2 border rounded">

                        <h5>${checkbox.dataset.name}</h5>

                        <p>
                            <strong>Price:</strong>
                            ₹${checkbox.dataset.price}
                        </p>

                        <p>
                            <strong>Company:</strong>
                            ${checkbox.dataset.company}
                        </p>

                        <p>
                            <strong>Stock:</strong>
                            ${checkbox.dataset.stock}
                        </p>

                        <p>
                            <strong>Description:</strong>
                            ${checkbox.dataset.description}
                        </p>

                    </div>

                `;

            }

        });

        if (html === '') {

            html = `
                <p>
                    Selected medicines details will appear here
                </p>
            `;

        }

        details.innerHTML = html;

    }

    /* ADD TO CART */

    function oderCAddToCart(customerId, cardId) {

        let checkboxes = document.querySelectorAll(
            '#oder-c-dropdown-list-' + cardId + ' .oder-c-checkbox'
        );

        let medicines = [];

        checkboxes.forEach((checkbox) => {

            if (checkbox.checked) {

                let qty = document.getElementById(
                    'qty-' + cardId + '-' + checkbox.value
                ).value;

                medicines.push({

                    medicine_id: checkbox.value,

                    quantity: qty

                });

            }

        });

        if (medicines.length === 0) {

            alert('Please Select Medicine');

            return;

        }

        fetch("{{ route('admin.add.cart') }}", {

                method: "POST",

                headers: {

                    "Content-Type": "application/json",

                    "X-CSRF-TOKEN": "{{ csrf_token() }}"

                },

                body: JSON.stringify({

                    coustmer_id: customerId,

                    medicines: medicines

                })

            })

            .then(res => res.json())

            .then(data => {

                alert(data.message);

                location.reload();

            })

            .catch(error => {

                console.log(error);

            });

    }

    /* IMAGE OPEN */

    function oderCOpenImage(src) {

        document.getElementById(
            'oder-c-image-modal'
        ).style.display = 'flex';

        document.getElementById(
            'oder-c-modal-image'
        ).src = src;

    }

    /* IMAGE CLOSE */

    function oderCCloseImage() {

        document.getElementById(
            'oder-c-image-modal'
        ).style.display = 'none';

    }

    /* OUTSIDE CLICK */

    window.onclick = function(e) {

        let modal = document.getElementById(
            'oder-c-image-modal'
        );

        if (e.target === modal) {

            oderCCloseImage();

        }

    }
</script>
<script>
    function openImage(src) {
        document.getElementById('imgModal').style.display = 'flex';
        document.getElementById('modalImg').src = src;
    }

    function closeImage() {
        document.getElementById('imgModal').style.display = 'none';
    }

    /* Optional: ESC key se close */
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeImage();
        }
    });
</script>

@endsection