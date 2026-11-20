@extends('admin.layouts.layout')

@section('content')
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1>Lab Test Categories</h1>
            <p>Group pathology and diagnostic tests by specialty, organ profile, or condition.</p>
        </div>
        <div class="page-header-btns">
            <button class="btn-primary" onclick="openAddCategoryModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:600;">
                <i class="fa-solid fa-plus"></i> Add New Category
            </button>
        </div>
    </div>

    <!-- STATS ROW -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;margin-bottom:20px;">
        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:12px;color:#6b7280;font-weight:500;">Total Categories</div>
                <div style="font-size:24px;font-weight:700;color:#111827;margin-top:2px;">{{ $stats['total'] }}</div>
            </div>
            <div style="width:40px;height:40px;background:#e0e7ff;color:#4f46e5;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>

        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:12px;color:#6b7280;font-weight:500;">Active Categories</div>
                <div style="font-size:24px;font-weight:700;color:#059669;margin-top:2px;">{{ $stats['active'] }}</div>
            </div>
            <div style="width:40px;height:40px;background:#dcfce7;color:#15803d;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:12px;color:#6b7280;font-weight:500;">Inactive</div>
                <div style="font-size:24px;font-weight:700;color:#6b7280;margin-top:2px;">{{ $stats['inactive'] }}</div>
            </div>
            <div style="width:40px;height:40px;background:#f3f4f6;color:#6b7280;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;">
                <i class="fa-solid fa-pause"></i>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div style="background:#fff;padding:14px 20px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('admin.lab.categories') }}" method="GET" style="display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;">
            <div style="display:flex;gap:12px;align-items:center;flex:1;">
                <div style="position:relative;min-width:240px;flex:1;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search category name..." style="width:100%;padding:9px 12px 9px 36px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                </div>

                <div>
                    <select name="status" onchange="this.form.submit()" style="padding:9px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" style="padding:9px 16px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.lab.categories') }}" style="padding:9px 14px;background:#f3f4f6;color:#4b5563;border-radius:8px;font-size:13px;text-decoration:none;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- CATEGORIES TABLE -->
    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                <thead>
                    <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;color:#4b5563;">
                        <th style="padding:12px 16px;font-weight:600;">Category</th>
                        <th style="padding:12px 16px;font-weight:600;">Description</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:center;">Total Tests</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:center;">Active Tests</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:center;">Status</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:14px 16px;vertical-align:middle;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                @if($category->icon)
                                    <img src="{{ asset($category->icon) }}" alt="{{ $category->name }}" style="width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;">
                                @else
                                    <div style="width:36px;height:36px;background:#e0e7ff;color:#4f46e5;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;">
                                        <i class="fa-solid fa-flask"></i>
                                    </div>
                                @endif
                                <div style="font-weight:700;color:#111827;font-size:14px;">
                                    {{ $category->name }}
                                </div>
                            </div>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;color:#6b7280;max-width:320px;">
                            {{ $category->description ?? 'No description added' }}
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                            <span style="background:#f3f4f6;padding:3px 8px;border-radius:12px;font-weight:600;color:#374151;">
                                {{ $category->tests_count }}
                            </span>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                            <span style="background:#dcfce7;color:#15803d;padding:3px 8px;border-radius:12px;font-weight:600;">
                                {{ $category->active_tests_count }}
                            </span>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                            <form action="{{ route('admin.lab.categories.toggleStatus', $category->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" title="Click to toggle status" style="border:none;background:none;cursor:pointer;padding:0;">
                                    @if($category->status === 'active')
                                        <span style="background:#dcfce7;color:#15803d;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                                            <i class="fa-solid fa-circle" style="font-size:7px;"></i> Active
                                        </span>
                                    @else
                                        <span style="background:#fee2e2;color:#b91c1c;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                                            <i class="fa-solid fa-circle" style="font-size:7px;"></i> Inactive
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;text-align:right;">
                            <div style="display:inline-flex;gap:6px;">
                                <a href="{{ route('admin.lab.tests', ['category_id' => $category->id]) }}" title="View tests" style="padding:6px 10px;background:#f3f4f6;color:#374151;border-radius:6px;font-size:12px;text-decoration:none;">
                                    <i class="fa-solid fa-list-check"></i>
                                </a>

                                <button type="button" onclick="openEditCategoryModal({{ json_encode($category) }})" title="Edit Category" style="padding:6px 10px;background:#e0e7ff;color:#4338ca;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <form action="{{ route('admin.lab.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete category {{ addslashes($category->name) }}? Linked tests will have category reset to null.');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Category" style="padding:6px 10px;background:#fee2e2;color:#b91c1c;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding:32px;text-align:center;color:#9ca3af;">
                            No lab test categories found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #e5e7eb;">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

    <!-- ADD CATEGORY MODAL -->
    <div id="addCategoryModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:500px;width:100%;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;">Add Test Category</h3>
                <button type="button" onclick="closeAddCategoryModal()" style="border:none;background:none;font-size:18px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form action="{{ route('admin.lab.categories.store') }}" method="POST" enctype="multipart/form-data" style="padding:24px;">
                @csrf
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Blood Tests, Cardiac Profile" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status</label>
                    <select name="status" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Category Icon (Optional)</label>
                    <input type="file" name="icon" accept="image/*" style="width:100%;font-size:13px;">
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Description</label>
                    <textarea name="description" rows="3" placeholder="Overview of tests in this clinical discipline..." style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;"></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;">
                    <button type="button" onclick="closeAddCategoryModal()" style="padding:9px 18px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:9px 20px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT CATEGORY MODAL -->
    <div id="editCategoryModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:500px;width:100%;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;">Edit Category</h3>
                <button type="button" onclick="closeEditCategoryModal()" style="border:none;background:none;font-size:18px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form id="editCategoryForm" method="POST" enctype="multipart/form-data" style="padding:24px;">
                @csrf
                @method('PUT')
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Category Name *</label>
                    <input type="text" id="edit_cat_name" name="name" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status</label>
                    <select id="edit_cat_status" name="status" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Update Icon (Leave blank to keep)</label>
                    <input type="file" name="icon" accept="image/*" style="width:100%;font-size:13px;">
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Description</label>
                    <textarea id="edit_cat_description" name="description" rows="3" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;"></textarea>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;">
                    <button type="button" onclick="closeEditCategoryModal()" style="padding:9px 18px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:9px 20px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Update Category</button>
                </div>
            </form>
        </div>
    </div>

</main>

<script>
    function openAddCategoryModal() {
        document.getElementById('addCategoryModal').style.display = 'flex';
    }
    function closeAddCategoryModal() {
        document.getElementById('addCategoryModal').style.display = 'none';
    }

    function openEditCategoryModal(cat) {
        document.getElementById('editCategoryForm').action = "{{ url('lab/categories') }}/" + cat.id;
        document.getElementById('edit_cat_name').value = cat.name || '';
        document.getElementById('edit_cat_status').value = cat.status || 'active';
        document.getElementById('edit_cat_description').value = cat.description || '';
        document.getElementById('editCategoryModal').style.display = 'flex';
    }
    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        if (e.target === document.getElementById('addCategoryModal')) closeAddCategoryModal();
        if (e.target === document.getElementById('editCategoryModal')) closeEditCategoryModal();
    });
</script>
@endsection
