@extends('admin.layouts.layout')

@section('content')

<!-- CONTENT -->
<main class="content">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>Category Management</h1>
            <p>Organize your pharmacy inventory by therapeutic groups and drug types.</p>
        </div>
        <button class="btn-add" onclick="openCategoryModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style=" width: 20px; ">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>

            Add New Category
        </button>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Categories</div>
            <div class="stat-value pink">{{ $categoriestotal }}</div>
            <div class="stat-sub">All therapeutic groups</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active Medicines</div>
            <div class="stat-value">{{ $activemedicetotal }}</div>
            <div class="stat-sub">In stock across all groups</div>
        </div>
        <!-- <div class="stat-card">
            <div class="stat-label">Top Performing</div>
            <div class="stat-value" style="font-size:20px;padding-top:4px;">Antibiotics</div>
            <div class="stat-sub">Highest demand this week</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">System Health</div>
            <div class="stat-value green" style="font-size:20px;padding-top:4px;">Optimal</div>
            <div class="stat-sub">Database synced 2m ago</div>
        </div> -->
    </div>

    <!-- Category Cards -->
    <div class="categories-grid">

        @foreach ($categories as $category)
        <!-- {{ $category->name }} -->
        <div class="cat-card">
            <div class="cat-card-top">
                <div class="cat-icon">

                    @if($category->icon)
                    <img src="{{ asset( $category->icon) }}"
                        alt="{{ $category->name }}"
                        style="width:40px;height:40px;object-fit:cover;border-radius:8px;">
                    @else
                    <!-- fallback SVG icon -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                    </svg>
                    @endif

                </div>
                <div class="card-actions">
                    <button class="card-action-btn" onclick="openEditModal({{ $category }})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                    </button>
                    <button class="card-action-btn del" onclick="openDeleteModal({{ $category->id }})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                            <path d="M10 11v6" />
                            <path d="M14 11v6" />
                            <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="cat-name">{{ $category->name }}</div>
            <div class="cat-desc">{{ $category->description }}</div>
            <div class="cat-divider"></div>
            <div class="cat-footer">
                <span class="med-count">
                    {{ $category->medicines_count }}
                    {{ Str::plural('Medicine', $category->medicines_count) }}
                </span>
            </div>
        </div>
        @endforeach

    </div>

    <!-- Recent Activity -->
    <div class="activity-section">
        <div class="activity-header">
            <h2>Recent Activity</h2>
            <a href="#" class="audit-link">View Audit Log</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Category Name</th>
                    <th>Last Modified</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $data)

                <tr>
                    <td>#{{ $data->id }}</td>
                    <td class="td-name">{{ $data->name }}</td>
                    <td class="td-date">{{ $data->updated_at->format('M j, Y g:i A') }}</td>
                    <td><span class="status-dot {{ $data->status === 'Published' ? 'published' : 'draft' }}">{{ $data->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</main>
</div>
<!-- ================= MODAL ================= -->
<div id="categoryModal" class="modal-overlay">

    <div class="modal-box">

        <div class="modal-header">
            <h2>Add New Category</h2>
            <button onclick="closeCategoryModal()" class="close-btn">×</button>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required></textarea>
            </div>

            <!-- ✅ IMAGE UPLOAD -->
            <div class="form-group">
                <label>Category Icon</label>
                <input type="file" name="icon" accept="image/*" onchange="previewImage(event)">
            </div>

            <!-- ✅ IMAGE PREVIEW -->
            <div class="form-group">
                <img id="iconPreview" src="" style="display:none;width:80px;height:80px;border-radius:10px;object-fit:cover;border:1px solid #ddd;">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Published">Published</option>
                    <option value="Draft">Draft</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeCategoryModal()">Cancel</button>
                <button type="submit" class="btn-save">Save</button>
            </div>

        </form>

    </div>
</div>

<div id="editCategoryModal" class="modal-overlay">

    <div class="modal-box">

        <div class="modal-header">
            <h2>Edit Category</h2>
            <button onclick="closeEditModal()" class="close-btn">×</button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_id">

            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" id="edit_name" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="edit_description" required></textarea>
            </div>

            <div class="form-group">
                <label>Category Icon</label>
                <input type="file" name="icon" onchange="previewEditImage(event)">
            </div>

            <div class="form-group">
                <img id="edit_iconPreview"
                    style="width:80px;height:80px;object-fit:cover;border-radius:10px;">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_status">
                    <option value="Published">Published</option>
                    <option value="Draft">Draft</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-save">Update</button>
            </div>

        </form>

    </div>
</div>

<div id="deleteCategoryModal" class="modal-overlay">

    <div class="modal-box">

        <div class="modal-header">
            <h2>Delete Category</h2>
            <button onclick="closeDeleteModal()" class="close-btn">×</button>
        </div>

        <p>Are you sure you want to delete this category?</p>

        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn-save" style="background:red;">Delete</button>
            </div>

        </form>

    </div>
</div>

<!-- ================= SCRIPT ================= -->
<script src="{{ asset('js/main.js') }}"></script>
<script>
    function openCategoryModal() {
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }
</script>

<script>
    function previewImage(event) {
        let reader = new FileReader();

        reader.onload = function() {
            let output = document.getElementById('iconPreview');
            output.src = reader.result;
            output.style.display = 'block';
        };

        reader.readAsDataURL(event.target.files[0]);
    }
</script>

<script>
    function openCategoryModal() {
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }

    /* ================= EDIT ================= */
    function openEditModal(category) {

        document.getElementById('editCategoryModal').style.display = 'flex';

        document.getElementById('edit_name').value = category.name;
        document.getElementById('edit_description').value = category.description;
        document.getElementById('edit_status').value = category.status;

        document.getElementById('editForm').action = `/categories/${category.id}`;

        if (category.icon) {
            document.getElementById('edit_iconPreview').src = `${category.icon}`;
        }
    }

    function closeEditModal() {
        document.getElementById('editCategoryModal').style.display = 'none';
    }

    /* ================= DELETE ================= */
    function openDeleteModal(id) {
        document.getElementById('deleteCategoryModal').style.display = 'flex';
        document.getElementById('deleteForm').action = `/categories/${id}`;
    }

    function closeDeleteModal() {
        document.getElementById('deleteCategoryModal').style.display = 'none';
    }

    /* ================= IMAGE PREVIEW ================= */
    function previewImage(event) {
        let reader = new FileReader();
        reader.onload = function() {
            let output = document.getElementById('iconPreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function previewEditImage(event) {
        let reader = new FileReader();
        reader.onload = function() {
            let output = document.getElementById('edit_iconPreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>

</html>

@endsection