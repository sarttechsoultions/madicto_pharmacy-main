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
                    <div class="bm-field">
                        <label>Main Banner Image</label>
                        <input type="file" name="img" class="bm-input" accept="image/*">
                    </div>

                    <h3 class="bm-card-title">Upload New Banner</h3>

                    <!-- UPLOAD -->
                    <div class="bm-upload-box" id="bmUploadBox">

                        <input type="file" id="bmFileInput" name="banners_images[]" hidden accept="image/*" multiple>

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

                    <div class="bm-field">
                        <label>Discount (%)</label>
                        <input type="number" name="discount" class="bm-input" placeholder="10" min="0" max="100">
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
            </form>
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
                        <img src="{{ asset( $banner->img) }}" class="bm-thumb">
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

                    <div class="bm-edit" style="cursor: pointer;"
                        onclick='openEditBanner(
        @json($banner->id),
        @json($banner->title),
        @json($banner->description),
        @json($banner->discount),
        @json($banner->start_date),
        @json($banner->end_date)
    )'>
                        ✏️
                    </div>
                    <div class="bm-trash"
                        onclick="openConfirms({{ $banner->id }}, '{{ $banner->title }}')">
                        🗑
                    </div>

                </div>

            </div>

            @endforeach

        </div>

    </div>

</div>


<!-- ================= MODAL ================= -->

<!-- ══════════════════════════════════════════════
     CONFIRM DELETE MODAL
═══════════════════════════════════════════════ -->
<div class="confirm-modal" id="confirmModalbanners">

    <div class="confirm-box">

        <div class="confirm-box-header">

            <div class="confirm-icon-wrap">
                <i class="fa-solid fa-trash"></i>
            </div>

            <h3>Delete Banner?</h3>

            <p id="confirmMsg">
                This action cannot be undone.
            </p>

        </div>

        <div class="confirm-box-footer">

            <button type="button"
                class="btn-draft"
                onclick="closeConfirms()">
                Cancel
            </button>

            <form id="deleteFormbanners" method="POST">
                @csrf

                <button type="submit" class="btn-danger">
                    <i class="fa-solid fa-trash"></i>
                    Delete
                </button>
            </form>

        </div>

    </div>

</div>

<div class="edit-modal" id="editBannerModal">

    <form id="editBannerForm" method="POST" enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="edit-box">

            <div class="edit-box-header">
                <h3>Edit Banner</h3>

                <button type="button"
                    class="icon-btn"
                    onclick="closeEditBanner()">
                    ✕
                </button>
            </div>

            <div class="edit-box-body">

                <div class="bm-field">
                    <label>Banner Image</label>
                    <input type="file" name="img" class="bm-input">
                </div>

                <div class="bm-field">
                    <label>Title</label>
                    <input type="text"
                        name="title"
                        id="edit_title"
                        class="bm-input">
                </div>

                <div class="bm-field">
                    <label>Discount</label>
                    <input type="number"
                        name="discount"
                        id="edit_discount"
                        class="bm-input">
                </div>

                <div class="bm-field">
                    <label>Description</label>

                    <textarea
                        name="description"
                        id="edit_description"
                        class="bm-input"
                        style="height:120px"></textarea>
                </div>

                <div class="bm-date-grid">

                    <div class="bm-field">
                        <label>Start Date</label>

                        <input type="date"
                            name="start_date"
                            id="edit_start_date"
                            class="bm-input">
                    </div>

                    <div class="bm-field">
                        <label>End Date</label>

                        <input type="date"
                            name="end_date"
                            id="edit_end_date"
                            class="bm-input">
                    </div>

                </div>

            </div>

            <div class="edit-box-footer">

                <button type="button"
                    class="btn-draft"
                    onclick="closeEditBanner()">
                    Cancel
                </button>

                <button type="submit"
                    class="bm-btn bm-btn-primary">

                    Update Banner
                </button>

            </div>

        </div>

    </form>

</div>

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

<script>
    function openConfirms(id, title) {
        document.getElementById('confirmModalbanners').style.display = 'flex';

        document.getElementById('confirmMsg').innerHTML =
            'Are you sure you want to delete <strong>' + title + '</strong>?';

        document.getElementById('deleteFormbanners').action =
            '/bannersd/' + id; // delete route
    }

    function closeConfirms() {
        document.getElementById('confirmModalbanners').style.display = 'none';
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

<script>
    function openEditBanner(
        id,
        title,
        description,
        discount,
        start_date,
        end_date,
        status
    ) {

        document.getElementById('editBannerForm').action =
            '/banners/' + id;

        document.getElementById('edit_title').value =
            title ?? '';

        document.getElementById('edit_description').value =
            description ?? '';

        document.getElementById('edit_discount').value =
            discount ?? '';

        document.getElementById('edit_start_date').value =
            start_date ?? '';

        document.getElementById('edit_end_date').value =
            end_date ?? '';

        document.getElementById('edit_status').value =
            status ?? 'inactive';

        document.getElementById('editBannerModal').style.display =
            'flex';
    }

    function closeEditBanner() {
        document.getElementById('editBannerModal').style.display =
            'none';
    }
</script>

@endsection