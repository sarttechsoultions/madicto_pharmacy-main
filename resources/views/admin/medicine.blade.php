@extends('admin.layouts.layout')
@section('content')

<!-- CONTENT -->
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1>Medicines Inventory</h1>
            <p>Manage and monitor your medical supplies and stock levels.</p>
        </div>
        <div class="page-header-btns">
            <button class="btn-primary" onclick="openModal()">
                <i class="fa-solid fa-plus"></i> Add Medicine
            </button>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card" onclick="filterByStatus('All Status')">
            <div class="stat-top">
                <div class="stat-icon blue"><i class="fa-solid fa-layer-group"></i></div>
            </div>
            <div class="stat-label">Total Items</div>
            <div class="stat-value" id="statTotal">{{ $medicenesall }}</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('Low Stock')">
            <div class="stat-top">
                <div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
            </div>
            <div class="stat-label">Low Stock</div>
            <div class="stat-value" id="statLow">{{ $mediceneslowstocks }}</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('In Stock')">
            <div class="stat-top">
                <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
            </div>
            <div class="stat-label">In Stock</div>
            <div class="stat-value" id="statIn">{{ $medicenesinstocks }}</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('Out of Stock')">
            <div class="stat-top">
                <div class="stat-icon pink"><i class="fa-solid fa-circle-xmark"></i></div>
            </div>
            <div class="stat-label">Out of Stock</div>
            <div class="stat-value" id="statOut">{{ $medicenesoutofstocks }}</div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <div class="quick-filter">
            <i class="fa-solid fa-filter" style="color:var(--muted);font-size:.8rem;"></i>
            <input type="text" id="filterInput" placeholder="Quick filter by name…" oninput="filterTable()" />
        </div>
        <div class="filter-group">
            <label>Category:</label>
            <select class="filter-select" id="catFilter" onchange="filterTable()">
                <option disabled selected>All Categories</option>
                @foreach($category as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Status:</label>
            <select class="filter-select" id="statusFilter" onchange="filterTable()">
                <option>All Status</option>
                <option>In Stock</option>
                <option>Low Stock</option>
                <option>Out of Stock</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Sort:</label>
            <select class="filter-select" id="sortFilter" onchange="filterTable()">
                <option>Name (A-Z)</option>
                <option>Name (Z-A)</option>
                <option>Price ↑</option>
                <option>Price ↓</option>
                <option>Stock ↑</option>
                <option>Stock ↓</option>
            </select>
        </div>
        <button class="reset-btn" title="Reset filters" onclick="resetFilters()">
            <i class="fa-solid fa-rotate-right"></i>
        </button>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Image</th>
                        <th>Medicine</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicenes as $medicine)
                    <tr>
                        <td>
                            <h4>#{{ $medicine->id }}</h4>
                        </td>
                        <td>
                            <div class="med-thumb">
                                <img src="{{ asset('uploads/medicine/' . $medicine->image) }}" alt="Medicine Image">
                            </div>
                        </td>
                        <td>
                            <h4>{{ $medicine->name }}</h4>
                        </td>
                        <td>{{ $medicine->category->name ?? 'N/A' }}</td>
                        <td>₹ {{ number_format($medicine->price, 2) }}</td>
                        <td>{{ $medicine->quantity }}</td>
                        <td>{{ $medicine->status }}</td>
                        <td>
                            <div class="actions-cell">
                                <button
                                    class="action-btn edit"
                                    onclick="openEditModal(
            '{{ $medicine->id }}',
            '{{ $medicine->name }}',
            '{{ $medicine->category_id }}',
            '{{ $medicine->price }}',
            '{{ $medicine->quantity }}',
            '{{ $medicine->status }}'
        )">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button
                                    class="action-btn del"
                                    title="Delete"
                                    onclick="openConfirm('{{ $medicine->id }}', '{{ $medicine->name }}')">

                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="table-footer">

            <div class="pagination">
                {{ $medicenes->links() }}
            </div>

        </div>
    </div>

</main>
</div>

<!-- ══════════════════════════════════════════════
     ADD MEDICINE MODAL — full screen
═══════════════════════════════════════════════ -->
<form action="{{ route('admin.medicine.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-backdrop" id="addMedicineModal">

        <header class="m-topbar">
            <div class="m-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search medicines, orders, or patient records…" />
            </div>
            <div class="m-topbar-right">
                <button class="icon-btn" title="Notifications">
                    <i class="fa-solid fa-bell"></i>
                    <span class="notif-dot"></span>
                </button>
                <div class="profile-pill">
                    <div class="avatar" style="background:linear-gradient(135deg,#c0392b,#e74c3c);">DS</div>
                    <div class="profile-info">
                        <strong>Dr. Sarah Smith</strong>
                        <small>Admin Profile</small>
                    </div>
                </div>
                <button class="icon-btn" onclick="closeModal()" title="Close (Esc)" type="button">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </header>

        <div class="m-body">

            <!-- Mirror sidebar -->


            <!-- Main form -->
            <div class="m-main">
                <div class="m-breadcrumb">
                    <a href="#" onclick="closeModal();return false;">Medicines</a>
                    <i class="fa-solid fa-chevron-right" style="font-size:.7rem;"></i>
                    <span id="modalBreadcrumb">Add New Medicine</span>
                </div>
                <div class="m-page-header">
                    <h1 id="modalTitle">Inventory Entry</h1>
                    <p id="modalSubtitle">Register a new pharmaceutical product into the central management system.</p>
                </div>

                <div class="m-content-grid">
                    <!-- Left column -->
                    <div class="m-left">

                        <div class="m-section">
                            <div class="m-section-title"><i class="fa-solid fa-circle-info"></i> Basic Information</div>
                            <div class="mf-row">
                                <div class="mf-group">
                                    <label>Medicine Name *</label>
                                    <input type="text" name="name" placeholder="e.g. Amoxicillin 500mg" />
                                </div>
                                <div class="mf-group">
                                    <label>Category *</label>
                                    <select id="f-cat" name="category_id">
                                        <option disabled selected>All Categories</option>
                                        @foreach($category as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mf-row">
                                <div class="mf-group">
                                    <label>Manufacturer</label>
                                    <input type="text" name="manufacturer" placeholder="e.g. Sun Pharma" />
                                </div>
                                <div class="mf-group">
                                    <label>Batch / Lot Number</label>
                                    <input type="text" name="batch_no" placeholder="e.g. BATCH-2024-001" />
                                </div>
                            </div>
                            <div class="mf-row single">
                                <div class="mf-group">
                                    <label>Description</label>
                                    <textarea id="f-desc" name="description" placeholder="Brief clinical description and indications…"></textarea>
                                </div>
                            </div>
                            <div class="mf-row single" style="margin-bottom:0;">
                                <div class="mf-group">
                                    <label>Usage Instructions</label>
                                    <input type="text" id="f-usage" name="usage_instructions" placeholder="e.g. Take twice daily after meals" />
                                </div>
                            </div>
                        </div>

                        <div class="m-section">
                            <div class="m-section-title"><i class="fa-solid fa-money-bill-wave"></i> Inventory &amp; Pricing</div>
                            <div class="mf-row three">
                                <div class="mf-group">
                                    <label>Base Price ($) *</label>
                                    <input type="number" id="f-price" name="price" placeholder="0.00" min="0" step="0.01" />
                                </div>
                                <div class="mf-group">
                                    <label>Discount (%)</label>
                                    <input type="number" id="f-discount" name="discount" placeholder="0" min="0" max="100" />
                                </div>
                                <div class="mf-group">
                                    <label>Stock Quantity *</label>
                                    <input type="number" id="f-stock" name="quantity" placeholder="0" min="0" />
                                </div>
                            </div>
                            <div class="mf-row three" style="margin-bottom:0;">
                                <div class="mf-group">
                                    <label>Reorder Level</label>
                                    <input type="number" id="f-reorder" name="reorder_level" placeholder="20" min="0" />
                                    <span class="field-hint">Alert below this qty</span>
                                </div>
                                <div class="mf-group">
                                    <label>Unit Type</label>
                                    <select id="f-unit" name="unit_type">
                                        <option>Tablets</option>
                                        <option>Capsules</option>
                                        <option>Syrup (ml)</option>
                                        <option>Injection (vial)</option>
                                        <option>Cream (g)</option>
                                        <option>Drops</option>
                                        <option>Patch</option>
                                    </select>
                                </div>
                                <div class="mf-group">
                                    <label>Pack Size</label>
                                    <input type="text" id="f-pack" name="pack_size" placeholder="e.g. 10 tabs/strip" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right column -->
                    <div class="m-right">

                        <div class="m-section">
                            <div class="m-section-title"><i class="fa-regular fa-image"></i> Product Media</div>
                            <div class="upload-zone" onclick="document.getElementById('imgInput').click()">
                                <input type="file" id="imgInput" name="image" accept="image/*" onchange="previewImage(event)" />
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <strong>Click to upload image</strong>
                                <span>PNG, JPG up to 10MB</span>
                            </div>
                            <div class="media-thumbs">
                                <div class="media-thumb" id="thumb1"></div>
                                <div class="media-thumb" id="thumb2"></div>
                                <div class="media-thumb-add" onclick="document.getElementById('imgInput').click()" title="Add more">+</div>
                            </div>
                        </div>

                        <div class="m-section">
                            <div class="m-section-title"><i class="fa-solid fa-shield-halved"></i> Compliance</div>
                            <div class="mf-group" style="margin-bottom:14px;">
                                <label>Manufacture Date</label>
                                <input type="date" id="f-mfgdate" name="manufacture_date" />
                            </div>
                            <div class="mf-group" style="margin-bottom:0;">
                                <label>Expiry Date *</label>
                                <input type="date" id="f-expiry" name="expiry_date" />
                            </div>
                            <div class="toggle-row">
                                <div class="toggle-info">
                                    <strong>Prescription Required</strong>
                                    <small>Must have doctor's validation</small>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="f-rx" name="prescription_required" checked />
                                    <span class="toggle-track"></span>
                                </label>
                            </div>
                            <div class="toggle-row" style="border-top:1px solid var(--border); padding-top:14px; margin-top:8px;">
                                <div class="toggle-info">
                                    <strong>Controlled Substance</strong>
                                    <small>Requires special handling</small>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="f-controlled" name="controlled_substance" />
                                    <span class="toggle-track"></span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <footer class="m-bottombar">
            <div class="m-autosave">
                <i class="fa-solid fa-circle-check"></i>
                <span>All changes auto-saved to secure cloud</span>
            </div>
            <div class="m-actions">
                <button class="btn-primary" type="submit">
                    <i class="fa-solid fa-paper-plane"></i> <span id="publishBtnText">Publish Medicine</span>
                </button>
            </div>
        </footer>

    </div>
</form>

<!-- ══════════════════════════════════════════════
     DRAFT PANEL
═══════════════════════════════════════════════ -->
<div class="draft-panel" id="draftPanel">
    <div class="draft-panel-header">
        <div>
            <h3><i class="fa-solid fa-file-pen" style="color:var(--primary);margin-right:6px;"></i>Saved Drafts</h3>
            <small id="draftSubtitle" style="font-size:.78rem;color:var(--muted);">0 drafts saved</small>
        </div>
        <button class="icon-btn" onclick="closeDraftPanel()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="draft-panel-body" id="draftPanelBody">
        <div class="draft-empty" id="draftEmpty">
            <i class="fa-solid fa-file-circle-question"></i>
            <span>No drafts yet.<br>Save a form to continue later.</span>
        </div>
    </div>
    <div class="draft-panel-footer">
        <button class="btn-primary" style="width:100%;" onclick="closeDraftPanel();openModal();">
            <i class="fa-solid fa-plus"></i> New Medicine Entry
        </button>
    </div>
</div>

<!-- ══════════════════════════════════════════════
     EDIT MODAL
═══════════════════════════════════════════════ -->
<div class="edit-modal" id="editModal">

    <form id="editForm" method="POST">
        @csrf
        @method('PUT')

        <div class="edit-box">

            <div class="edit-box-header">
                <h3>
                    <i class="fa-solid fa-pen"
                        style="color:var(--primary);margin-right:8px;"></i>
                    Edit Medicine
                </h3>

                <button type="button" class="icon-btn" onclick="closeEditModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="edit-box-body">

                <div class="mf-row">
                    <div class="mf-group">
                        <label>Medicine Name</label>
                        <input type="text" name="name" id="e-name" />
                    </div>

                    <div class="mf-group">
                        <label>Category</label>

                        <select name="category_id" id="e-cat">
                            @foreach($category as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mf-row three">

                    <div class="mf-group">
                        <label>Price ($)</label>
                        <input type="number" name="price" id="e-price" min="0" step="0.01" />
                    </div>

                    <div class="mf-group">
                        <label>Stock Qty</label>
                        <input type="number" name="quantity" id="e-stock" min="0" />
                    </div>

                    <div class="mf-group">
                        <label>Status</label>

                        <select name="status" id="e-status">
                            <option value="In Stock">In Stock</option>
                            <option value="Out of Stock">Out of Stock</option>
                        </select>
                    </div>

                </div>

            </div>

            <div class="edit-box-footer">
                <button type="button" class="btn-draft" onclick="closeEditModal()">
                    Cancel
                </button>

                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-check"></i>
                    Save Changes
                </button>
            </div>

        </div>

    </form>

</div>

<!-- ══════════════════════════════════════════════
     CONFIRM DELETE MODAL
═══════════════════════════════════════════════ -->
<div class="confirm-modal" id="confirmModal">

    <div class="confirm-box">

        <div class="confirm-box-header">

            <div class="confirm-icon-wrap">
                <i class="fa-solid fa-trash"></i>
            </div>

            <h3>Delete Medicine?</h3>

            <p id="confirmMsg">
                This action cannot be undone.
            </p>

        </div>

        <div class="confirm-box-footer">

            <button
                type="button"
                class="btn-draft"
                onclick="closeConfirm()">

                Cancel
            </button>

            <form id="deleteForm" method="POST">

                @csrf
                @method('DELETE')

                <button type="submit" class="btn-danger">
                    <i class="fa-solid fa-trash"></i>
                    Delete
                </button>

            </form>

        </div>

    </div>

</div>

<script src="{{ asset('js/main.js') }}"></script>
<script>
    function filterTable() {
        let input = document.getElementById("filterInput").value.toLowerCase();
        let catFilter = document.getElementById("catFilter").value;
        let statusFilter = document.getElementById("statusFilter").value;
        let sortFilter = document.getElementById("sortFilter").value;

        let table = document.querySelector("tbody");
        let rows = Array.from(table.querySelectorAll("tr"));

        rows.forEach(row => {
            let name = row.cells[2].innerText.toLowerCase();
            let category = row.cells[3].innerText.trim();
            let price = parseFloat(row.cells[4].innerText.replace("₹", "").replace(",", ""));
            let stock = parseInt(row.cells[5].innerText);
            let status = row.cells[6].innerText.trim();

            let matchName = name.includes(input);

            // CATEGORY MATCH
            let selectedCatText = "";
            let catSelect = document.getElementById("catFilter");

            if (catSelect.selectedIndex > 0) {
                selectedCatText = catSelect.options[catSelect.selectedIndex].text.trim();
            }

            let matchCategory =
                selectedCatText === "" ||
                category === selectedCatText;

            // STATUS MATCH
            let matchStatus =
                statusFilter === "All Status" ||
                status === statusFilter;

            if (matchName && matchCategory && matchStatus) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });

        // SORTING
        let visibleRows = rows.filter(row => row.style.display !== "none");

        visibleRows.sort((a, b) => {
            let nameA = a.cells[2].innerText.toLowerCase();
            let nameB = b.cells[2].innerText.toLowerCase();

            let priceA = parseFloat(a.cells[4].innerText.replace("₹", "").replace(",", ""));
            let priceB = parseFloat(b.cells[4].innerText.replace("₹", "").replace(",", ""));

            let stockA = parseInt(a.cells[5].innerText);
            let stockB = parseInt(b.cells[5].innerText);

            switch (sortFilter) {
                case "Name (A-Z)":
                    return nameA.localeCompare(nameB);

                case "Name (Z-A)":
                    return nameB.localeCompare(nameA);

                case "Price ↑":
                    return priceA - priceB;

                case "Price ↓":
                    return priceB - priceA;

                case "Stock ↑":
                    return stockA - stockB;

                case "Stock ↓":
                    return stockB - stockA;

                default:
                    return 0;
            }
        });

        visibleRows.forEach(row => table.appendChild(row));
    }

    function resetFilters() {
        document.getElementById("filterInput").value = "";
        document.getElementById("catFilter").selectedIndex = 0;
        document.getElementById("statusFilter").selectedIndex = 0;
        document.getElementById("sortFilter").selectedIndex = 0;

        filterTable();
    }
</script>

@endsection