@extends('admin.layouts.layout')

@section('content')
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1>Laboratory Setup &amp; Partners</h1>
            <p>Manage diagnostic laboratories, contact information, addresses, and partnerships.</p>
        </div>
        <div class="page-header-btns">
            <button class="btn-primary" onclick="openAddLabModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:600;">
                <i class="fa-solid fa-plus"></i> Add Laboratory
            </button>
        </div>
    </div>

    <!-- SEARCH & FILTERS -->
    <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('admin.lab.setup') }}" method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;flex:1;">
                <div style="position:relative;min-width:240px;flex:1;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search lab name, city, contact..." style="width:100%;padding:9px 12px 9px 36px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                </div>

                <div>
                    <select name="status" onchange="this.form.submit()" style="padding:9px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" style="padding:9px 16px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.lab.setup') }}" style="padding:9px 14px;background:#f3f4f6;color:#4b5563;border-radius:8px;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- LABS TABLE -->
    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                <thead>
                    <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;color:#4b5563;">
                        <th style="padding:12px 16px;font-weight:600;">Laboratory</th>
                        <th style="padding:12px 16px;font-weight:600;">Contact &amp; Email</th>
                        <th style="padding:12px 16px;font-weight:600;">Location</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:center;">Tests</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:center;">Status</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($labs as $lab)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:14px 16px;vertical-align:middle;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                @if($lab->logo)
                                    <img src="{{ asset($lab->logo) }}" alt="{{ $lab->name }}" style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:1px solid #e5e7eb;">
                                @else
                                    <div style="width:44px;height:44px;background:#fce8f3;color:#d4155b;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;">
                                        {{ strtoupper(substr($lab->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight:700;color:#111827;font-size:14px;">{{ $lab->name }}</div>
                                    @if($lab->description)
                                    <div style="font-size:12px;color:#6b7280;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $lab->description }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;">
                            <div style="color:#111827;font-weight:500;">
                                <i class="fa-solid fa-phone" style="color:#9ca3af;margin-right:4px;font-size:11px;"></i> {{ $lab->contact_number ?? 'Not provided' }}
                            </div>
                            @if($lab->email)
                            <div style="font-size:12px;color:#6b7280;margin-top:2px;">
                                <i class="fa-solid fa-envelope" style="color:#9ca3af;margin-right:4px;font-size:11px;"></i> {{ $lab->email }}
                            </div>
                            @endif
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;">
                            <div style="font-weight:600;color:#111827;">{{ $lab->city ? $lab->city . ', ' . $lab->state : 'N/A' }}</div>
                            <div style="font-size:12px;color:#6b7280;max-width:240px;line-height:1.3;margin-top:2px;">
                                {{ $lab->address ?? '' }} {{ $lab->pincode ? '(' . $lab->pincode . ')' : '' }}
                            </div>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                            <span style="background:#eff6ff;color:#2563eb;padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600;">
                                {{ $lab->tests_count }} Tests
                            </span>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                            <form action="{{ route('admin.lab.toggleStatus', $lab->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" title="Click to toggle status" style="border:none;background:none;cursor:pointer;padding:0;">
                                    @if($lab->status === 'active')
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
                                <a href="{{ route('admin.lab.tests', ['lab_id' => $lab->id]) }}" title="View tests" style="padding:6px 10px;background:#f3f4f6;color:#374151;border-radius:6px;font-size:12px;text-decoration:none;">
                                    <i class="fa-solid fa-vial"></i>
                                </a>

                                <button type="button" onclick="openEditLabModal({{ json_encode($lab) }})" title="Edit Lab" style="padding:6px 10px;background:#e0e7ff;color:#4338ca;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <form action="{{ route('admin.lab.destroy', $lab->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($lab->name) }}? All linked tests will also be deleted!');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Lab" style="padding:6px 10px;background:#fee2e2;color:#b91c1c;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding:32px;text-align:center;color:#9ca3af;">
                            <i class="fa-solid fa-hospital" style="font-size:32px;margin-bottom:8px;display:block;color:#d1d5db;"></i>
                            No laboratories found. Click <strong>Add Laboratory</strong> to create your first lab partner.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($labs->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #e5e7eb;">
            {{ $labs->links() }}
        </div>
        @endif
    </div>

    <!-- ADD LAB MODAL -->
    <div id="addLabModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:650px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;">Add New Laboratory Partner</h3>
                <button type="button" onclick="closeAddLabModal()" style="border:none;background:none;font-size:18px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form action="{{ route('admin.lab.store') }}" method="POST" enctype="multipart/form-data" style="padding:24px;">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Laboratory Name *</label>
                        <input type="text" name="name" required placeholder="e.g. City Diagnostic Laboratory" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Contact Number *</label>
                        <input type="text" name="contact_number" required placeholder="+91 98765 43210" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Email Address</label>
                        <input type="email" name="email" placeholder="contact@citylab.com" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Complete Address</label>
                        <input type="text" name="address" placeholder="Building, Street, Landmark" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">City</label>
                        <input type="text" name="city" placeholder="Mumbai" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">State</label>
                        <input type="text" name="state" placeholder="Maharashtra" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Pincode</label>
                        <input type="text" name="pincode" placeholder="400001" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status</label>
                        <select name="status" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Lab Logo</label>
                        <input type="file" name="logo" accept="image/*" style="width:100%;font-size:13px;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Description / Accreditation</label>
                        <textarea name="description" rows="3" placeholder="NABL accredited, 24x7 diagnostics..." style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;"></textarea>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;border-top:1px solid #e5e7eb;padding-top:16px;">
                    <button type="button" onclick="closeAddLabModal()" style="padding:9px 18px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:9px 20px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Save Laboratory</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT LAB MODAL -->
    <div id="editLabModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:650px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;">Edit Laboratory Details</h3>
                <button type="button" onclick="closeEditLabModal()" style="border:none;background:none;font-size:18px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form id="editLabForm" method="POST" enctype="multipart/form-data" style="padding:24px;">
                @csrf
                @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Laboratory Name *</label>
                        <input type="text" id="edit_name" name="name" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Contact Number *</label>
                        <input type="text" id="edit_contact_number" name="contact_number" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Email Address</label>
                        <input type="email" id="edit_email" name="email" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Complete Address</label>
                        <input type="text" id="edit_address" name="address" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">City</label>
                        <input type="text" id="edit_city" name="city" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">State</label>
                        <input type="text" id="edit_state" name="state" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Pincode</label>
                        <input type="text" id="edit_pincode" name="pincode" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status</label>
                        <select id="edit_status" name="status" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Update Logo (Leave blank to keep existing)</label>
                        <input type="file" name="logo" accept="image/*" style="width:100%;font-size:13px;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Description / Accreditation</label>
                        <textarea id="edit_description" name="description" rows="3" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;"></textarea>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;border-top:1px solid #e5e7eb;padding-top:16px;">
                    <button type="button" onclick="closeEditLabModal()" style="padding:9px 18px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:9px 20px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Update Laboratory</button>
                </div>
            </form>
        </div>
    </div>

</main>

<script>
    function openAddLabModal() {
        document.getElementById('addLabModal').style.display = 'flex';
    }
    function closeAddLabModal() {
        document.getElementById('addLabModal').style.display = 'none';
    }

    function openEditLabModal(lab) {
        document.getElementById('editLabForm').action = "{{ url('lab/setup') }}/" + lab.id;
        document.getElementById('edit_name').value = lab.name || '';
        document.getElementById('edit_contact_number').value = lab.contact_number || '';
        document.getElementById('edit_email').value = lab.email || '';
        document.getElementById('edit_address').value = lab.address || '';
        document.getElementById('edit_city').value = lab.city || '';
        document.getElementById('edit_state').value = lab.state || '';
        document.getElementById('edit_pincode').value = lab.pincode || '';
        document.getElementById('edit_status').value = lab.status || 'active';
        document.getElementById('edit_description').value = lab.description || '';
        document.getElementById('editLabModal').style.display = 'flex';
    }
    function closeEditLabModal() {
        document.getElementById('editLabModal').style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        const addModal = document.getElementById('addLabModal');
        const editModal = document.getElementById('editLabModal');
        if (e.target === addModal) closeAddLabModal();
        if (e.target === editModal) closeEditLabModal();
    });
</script>
@endsection
