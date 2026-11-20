@extends('admin.layouts.layout')

@section('content')
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1>Manage Lab Tests</h1>
            <p>Maintain catalog of pathology tests, laboratory associations, pricing, and sample instructions.</p>
        </div>
        <div class="page-header-btns">
            <button class="btn-primary" onclick="openAddTestModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:600;">
                <i class="fa-solid fa-plus"></i> Add New Lab Test
            </button>
        </div>
    </div>

    <!-- SEARCH & FILTER BAR -->
    <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('admin.lab.tests') }}" method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            
            <!-- Search -->
            <div style="position:relative;min-width:240px;flex:1;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search test name, test code..." style="width:100%;padding:9px 12px 9px 36px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
            </div>

            <!-- Lab Filter -->
            <div>
                <select name="lab_id" onchange="this.form.submit()" style="padding:9px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                    <option value="all">All Laboratories</option>
                    @foreach($labs as $l)
                    <option value="{{ $l->id }}" {{ request('lab_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category_id" onchange="this.form.submit()" style="padding:9px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                    <option value="all">All Categories</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" onchange="this.form.submit()" style="padding:9px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>

            <div>
                <button type="submit" style="padding:9px 16px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'lab_id', 'category_id', 'status']))
                <a href="{{ route('admin.lab.tests') }}" style="padding:9px 14px;background:#f3f4f6;color:#4b5563;border-radius:8px;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;margin-left:4px;">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TESTS TABLE -->
    <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                <thead>
                    <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;color:#4b5563;">
                        <th style="padding:12px 16px;font-weight:600;">Test Name &amp; Code</th>
                        <th style="padding:12px 16px;font-weight:600;">Laboratory</th>
                        <th style="padding:12px 16px;font-weight:600;">Category</th>
                        <th style="padding:12px 16px;font-weight:600;">Price (₹)</th>
                        <th style="padding:12px 16px;font-weight:600;">Sample &amp; Delivery</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:center;">Status</th>
                        <th style="padding:12px 16px;font-weight:600;text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests as $test)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:14px 16px;vertical-align:middle;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                @if($test->image)
                                    <img src="{{ asset($test->image) }}" alt="{{ $test->test_name }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid #e5e7eb;">
                                @else
                                    <div style="width:40px;height:40px;background:#eff6ff;color:#3b82f6;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;">
                                        <i class="fa-solid fa-vial"></i>
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight:700;color:#111827;font-size:14px;">{{ $test->test_name }}</div>
                                    <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                                        Code: <span style="font-family:monospace;font-weight:600;color:#374151;">{{ $test->test_code ?? 'N/A' }}</span>
                                    </div>
                                    @if($test->preparation_instructions)
                                    <div style="font-size:11px;color:#d97706;margin-top:3px;max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> {{ $test->preparation_instructions }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;">
                            <div style="font-weight:600;color:#111827;">{{ $test->lab->name ?? 'N/A' }}</div>
                            <div style="font-size:11px;color:#6b7280;">{{ $test->lab->city ?? '' }}</div>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;">
                            <span style="background:#f3f4f6;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;color:#4b5563;">
                                {{ $test->category->name ?? 'General' }}
                            </span>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;">
                            <div style="font-size:15px;font-weight:700;color:#111827;">
                                ₹{{ number_format($test->price, 2) }}
                            </div>
                            @if($test->discount_price)
                                <div style="font-size:12px;color:#059669;font-weight:600;">
                                    ₹{{ number_format($test->discount_price, 2) }} <span style="font-size:10px;background:#dcfce7;color:#15803d;padding:1px 5px;border-radius:4px;">Deal</span>
                                </div>
                            @endif
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;">
                            <div style="color:#111827;font-weight:500;">
                                <i class="fa-solid fa-droplet" style="color:#ef4444;margin-right:4px;font-size:11px;"></i> {{ $test->sample_type ?? 'N/A' }}
                            </div>
                            <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                                <i class="fa-regular fa-clock" style="margin-right:4px;"></i> {{ $test->report_delivery_time ?? '24 Hours' }}
                            </div>
                        </td>

                        <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                            <form action="{{ route('admin.lab.tests.toggleStatus', $test->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" title="Click to toggle status" style="border:none;background:none;cursor:pointer;padding:0;">
                                    @if($test->status === 'active')
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
                                <button type="button" onclick="openPricingModal({{ json_encode($test) }})" title="Quick Update Price" style="padding:6px 10px;background:#fef3c7;color:#b45309;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                                    <i class="fa-solid fa-indian-rupee-sign"></i>
                                </button>

                                <button type="button" onclick="openEditTestModal({{ json_encode($test) }})" title="Edit Test" style="padding:6px 10px;background:#e0e7ff;color:#4338ca;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <form action="{{ route('admin.lab.tests.destroy', $test->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($test->test_name) }}?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Test" style="padding:6px 10px;background:#fee2e2;color:#b91c1c;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:32px;text-align:center;color:#9ca3af;">
                            <i class="fa-solid fa-vial-virus" style="font-size:32px;margin-bottom:8px;display:block;color:#d1d5db;"></i>
                            No lab tests found. Click <strong>Add New Lab Test</strong> to register one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tests->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #e5e7eb;">
            {{ $tests->links() }}
        </div>
        @endif
    </div>

    <!-- ADD TEST MODAL -->
    <div id="addTestModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:700px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;">Add New Diagnostic Lab Test</h3>
                <button type="button" onclick="closeAddTestModal()" style="border:none;background:none;font-size:18px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form action="{{ route('admin.lab.tests.store') }}" method="POST" enctype="multipart/form-data" style="padding:24px;">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Name *</label>
                        <input type="text" name="test_name" required placeholder="e.g. Complete Blood Count (CBC)" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Code</label>
                        <input type="text" name="test_code" placeholder="e.g. CBC001 (auto if empty)" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Laboratory *</label>
                        <select name="lab_id" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="">Select Laboratory</option>
                            @foreach($labs as $l)
                            <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->city ?? 'Main' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Category *</label>
                        <select name="category_id" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="">Select Category</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Regular Price (₹) *</label>
                        <input type="number" step="0.01" name="price" required placeholder="350.00" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Discounted Price (₹)</label>
                        <input type="number" step="0.01" name="discount_price" placeholder="300.00 (optional)" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Sample Type</label>
                        <input type="text" name="sample_type" placeholder="e.g. Blood (EDTA), Urine" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Report Delivery Time</label>
                        <input type="text" name="report_delivery_time" placeholder="e.g. 24 Hours, Same Day" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Preparation Instructions</label>
                        <input type="text" name="preparation_instructions" placeholder="e.g. 10-12 hours overnight fasting is required." style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status</label>
                        <select name="status" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Image (Optional)</label>
                        <input type="file" name="image" accept="image/*" style="width:100%;font-size:13px;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Description</label>
                        <textarea name="description" rows="3" placeholder="Clinical significance, parameters measured..." style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;"></textarea>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;border-top:1px solid #e5e7eb;padding-top:16px;">
                    <button type="button" onclick="closeAddTestModal()" style="padding:9px 18px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:9px 20px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Save Lab Test</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT TEST MODAL -->
    <div id="editTestModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:700px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;">Edit Lab Test</h3>
                <button type="button" onclick="closeEditTestModal()" style="border:none;background:none;font-size:18px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form id="editTestForm" method="POST" enctype="multipart/form-data" style="padding:24px;">
                @csrf
                @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Name *</label>
                        <input type="text" id="edit_test_name" name="test_name" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Code</label>
                        <input type="text" id="edit_test_code" name="test_code" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Laboratory *</label>
                        <select id="edit_lab_id" name="lab_id" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            @foreach($labs as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Category *</label>
                        <select id="edit_category_id" name="category_id" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Regular Price (₹) *</label>
                        <input type="number" step="0.01" id="edit_price" name="price" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Discounted Price (₹)</label>
                        <input type="number" step="0.01" id="edit_discount_price" name="discount_price" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Sample Type</label>
                        <input type="text" id="edit_sample_type" name="sample_type" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Report Delivery Time</label>
                        <input type="text" id="edit_report_delivery_time" name="report_delivery_time" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Preparation Instructions</label>
                        <input type="text" id="edit_preparation_instructions" name="preparation_instructions" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status</label>
                        <select id="edit_status" name="status" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Update Image (Leave blank to keep)</label>
                        <input type="file" name="image" accept="image/*" style="width:100%;font-size:13px;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Description</label>
                        <textarea id="edit_description" name="description" rows="3" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;"></textarea>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;border-top:1px solid #e5e7eb;padding-top:16px;">
                    <button type="button" onclick="closeEditTestModal()" style="padding:9px 18px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:9px 20px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Update Lab Test</button>
                </div>
            </form>
        </div>
    </div>

    <!-- QUICK PRICING MODAL -->
    <div id="pricingModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:440px;width:100%;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:#111827;">Quick Update Pricing</h3>
                <button type="button" onclick="closePricingModal()" style="border:none;background:none;font-size:18px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form id="pricingForm" method="POST" style="padding:20px;">
                @csrf
                <div style="margin-bottom:14px;">
                    <div id="price_test_name" style="font-size:14px;font-weight:700;color:#111827;margin-bottom:4px;"></div>
                    <div id="price_lab_name" style="font-size:12px;color:#6b7280;"></div>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Regular Price (₹) *</label>
                    <input type="number" step="0.01" id="pricing_price" name="price" required style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-weight:600;outline:none;">
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Discounted Deal Price (₹)</label>
                    <input type="number" step="0.01" id="pricing_discount_price" name="discount_price" placeholder="Leave blank if no discount" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;">
                </div>

                <div style="display:flex;justify-content:flex-end;gap:10px;">
                    <button type="button" onclick="closePricingModal()" style="padding:8px 16px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:8px 18px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Save Pricing</button>
                </div>
            </form>
        </div>
    </div>

</main>

<script>
    function openAddTestModal() {
        document.getElementById('addTestModal').style.display = 'flex';
    }
    function closeAddTestModal() {
        document.getElementById('addTestModal').style.display = 'none';
    }

    function openEditTestModal(test) {
        document.getElementById('editTestForm').action = "{{ url('lab/tests') }}/" + test.id;
        document.getElementById('edit_test_name').value = test.test_name || '';
        document.getElementById('edit_test_code').value = test.test_code || '';
        document.getElementById('edit_lab_id').value = test.lab_id || '';
        document.getElementById('edit_category_id').value = test.category_id || '';
        document.getElementById('edit_price').value = test.price || '';
        document.getElementById('edit_discount_price').value = test.discount_price !== null ? test.discount_price : '';
        document.getElementById('edit_sample_type').value = test.sample_type || '';
        document.getElementById('edit_report_delivery_time').value = test.report_delivery_time || '';
        document.getElementById('edit_preparation_instructions').value = test.preparation_instructions || '';
        document.getElementById('edit_status').value = test.status || 'active';
        document.getElementById('edit_description').value = test.description || '';
        document.getElementById('editTestModal').style.display = 'flex';
    }
    function closeEditTestModal() {
        document.getElementById('editTestModal').style.display = 'none';
    }

    function openPricingModal(test) {
        document.getElementById('pricingForm').action = "{{ url('lab/tests') }}/" + test.id + "/pricing";
        document.getElementById('price_test_name').innerText = test.test_name;
        document.getElementById('price_lab_name').innerText = test.lab ? test.lab.name : '';
        document.getElementById('pricing_price').value = test.price || '';
        document.getElementById('pricing_discount_price').value = test.discount_price !== null ? test.discount_price : '';
        document.getElementById('pricingModal').style.display = 'flex';
    }
    function closePricingModal() {
        document.getElementById('pricingModal').style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        if (e.target === document.getElementById('addTestModal')) closeAddTestModal();
        if (e.target === document.getElementById('editTestModal')) closeEditTestModal();
        if (e.target === document.getElementById('pricingModal')) closePricingModal();
    });
</script>
@endsection
