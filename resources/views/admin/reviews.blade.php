@extends('admin.layouts.layout')

@section('content')

<div class="oder-c-content">

    <h1 class="oder-c-title">
        Customer Review
    </h1>

    <div class="oder-c-cart-box">

        <h2 class="oder-c-cart-title">
            Review Details
        </h2>

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>#</th>

                    <th>Coustmer</th>

                    <th>medicine</th>

                    <th>rating</th>

                    <th>review</th>

                </tr>

            </thead>

            <tbody>

                @forelse ($reviews as $cart)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>

                        {{ $cart->coustmer->name ?? 'N/A' }}
                    </td>
                    <td>
                        {{ $cart->medicine->name ?? 'N/A' }}

                    </td>
                    <td>

                        @php
                        $rating = $cart->rating ?? 0;

                        $fullStars = floor($rating);

                        $halfStar = ($rating - $fullStars) >= 0.5;

                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                        @endphp

                        <span style="color:#f5b301; font-size:18px;">

                            {{-- FULL STARS --}}
                            @for($i = 0; $i < $fullStars; $i++)
                                ★
                                @endfor

                                {{-- HALF STAR --}}
                                @if($halfStar)
                                ⯨
                                @endif

                                {{-- EMPTY STARS --}}
                                @for($i=0; $i < $emptyStars; $i++)
                                ☆
                                @endfor

                                </span>

                                <small style="color:#666;">
                                    ({{ number_format($rating, 1) }})
                                </small>

                    </td>

                    <td>

                        {{ $cart->review ?? 'N/A' }}

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="7" class="text-center">

                        No Review Added Yet

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