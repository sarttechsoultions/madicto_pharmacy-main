@extends('admin.layouts.layout')

@section('content')

<div class="bm-content">

    <!-- HEADER -->
    <div class="bm-header">
        <div>
            <h1 class="bm-title">Banner Management</h1>
            <p class="bm-subtitle">Design and manage promotional banners</p>
        </div>
    </div>

    <div class="bm-grid">

        <!-- LEFT SIDE -->
        <div>

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bm-card">

                    <h3 class="bm-card-title">Upload New Banner</h3>

                    <!-- UPLOAD -->
                    <div class="bm-upload-box" id="bmUploadBox">

                        <input type="file" id="bmFileInput" name="img" hidden accept="image/*">

                        <div class="bm-upload-icon">☁</div>

                        <h3 id="bmUploadText">Click or drag image</h3>

                        <p>PNG, JPG (max 1200×400)</p>

                        <img id="bmPreview" style="
                        display:none;
                        width:100%;
                        height:100%;
                        object-fit:cover;
                        border-radius:12px;
                        cursor:pointer;
                    ">
                    </div>

                    <!-- TITLE -->
                    <div class="bm-field">
                        <label>Banner Title</label>
                        <input class="bm-input" type="text" name="title" placeholder="Enter title...">
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="bm-field">
                        <label>Description</label>
                        <textarea class="bm-input" name="description" style="height:120px"></textarea>
                    </div>

                    <!-- DATES -->
                    <div class="bm-date-grid">

                        <div class="bm-field">
                            <label>Start Date</label>
                            <input class="bm-input" type="date" name="start_date">
                        </div>

                        <div class="bm-field">
                            <label>End Date</label>
                            <input class="bm-input" type="date" name="end_date">
                        </div>

                    </div>

                    <button class="bm-btn bm-btn-primary" type="submit">Upload Banner</button>

                </div>

        </div>

        <!-- RIGHT SIDE (QUEUE) -->
        <div class="bm-card">

            <div class="bm-queue-header">
                <div class="bm-queue-title">CURRENT QUEUE</div>
            </div>

            @foreach ($banners as $banner)

            <!-- ITEM -->
            <div class="bm-banner-item">

                <div class="bm-banner-left">

                    <div class="bm-drag">⋮⋮</div>

                    <div class="bm-banner-img">
                        <img src="{{ asset('uploads/banners/' . $banner->img) }}" class="bm-thumb">
                    </div>

                    <div class="bm-banner-info">
                        <h3>{{ $banner->title }}</h3>
                        <small>📅 {{ \Carbon\Carbon::parse($banner->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($banner->end_date)->format('M d') }} | 👁 {{ rand(1000, 2000) }} views</small>
                    </div>

                </div>

                <div class="bm-banner-actions">

                    <div class="bm-switch {{ $banner->status == 'active' ? 'active' : '' }}"
                        onclick="toggleBannerStatus(this, {{ $banner->id }})"></div>

                    <div class="bm-status">
                        {{ strtoupper($banner->status) }}
                    </div>

                    <div class="bm-trash">🗑</div>

                </div>

            </div>

            @endforeach

        </div>

    </div>

</div>


<!-- ================= MODAL ================= -->

<!-- ================= JS ================= -->

<script>
    function toggleBannerStatus(el, id) {

        fetch('/banner/toggle-status/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {

                if (data.status == 'active') {
                    el.classList.add('active');
                    el.nextElementSibling.innerText = 'ACTIVE';
                } else {
                    el.classList.remove('active');
                    el.nextElementSibling.innerText = 'INACTIVE';
                }

            });

    }
</script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    const bmUploadBox = document.getElementById('bmUploadBox');
    const bmFileInput = document.getElementById('bmFileInput');
    const bmPreview = document.getElementById('bmPreview');
    const bmUploadText = document.getElementById('bmUploadText');

    const bmModal = document.getElementById('bmImageModal');
    const bmModalImg = document.getElementById('bmModalImg');

    /* OPEN FILE */
    bmUploadBox.addEventListener('click', () => bmFileInput.click());

    /* FILE LOAD */
    bmFileInput.addEventListener('change', function() {

        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                bmPreview.src = e.target.result;
                bmPreview.style.display = 'block';
                bmUploadText.innerText = file.name;
            }

            reader.readAsDataURL(file);
        }
    });

    /* DROP */
    bmUploadBox.addEventListener('drop', function(e) {
        e.preventDefault();

        const file = e.dataTransfer.files[0];

        if (file) {
            bmFileInput.files = e.dataTransfer.files;

            const reader = new FileReader();

            reader.onload = function(e) {
                bmPreview.src = e.target.result;
                bmPreview.style.display = 'block';
            }

            reader.readAsDataURL(file);
        }
    });

    /* MODAL OPEN */
    bmPreview.addEventListener('click', function() {
        if (this.src) {
            bmModalImg.src = this.src;
            bmModal.style.display = 'flex';
        }
    });

    /* CLOSE */
    function closeImageModal() {
        bmModal.style.display = 'none';
    }

    /* OUTSIDE CLICK */
    bmModal.addEventListener('click', function(e) {
        if (e.target === bmModal) closeImageModal();
    });
</script>

@endsection