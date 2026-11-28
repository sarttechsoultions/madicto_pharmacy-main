@extends('admin.layouts.layout')

@section('content')
<style>
    body, input, select, textarea, button, table, th, td, h1, h2, h3, h4, h5, h6, .content, .page-header {
        font-family: 'Poppins', sans-serif !important;
    }
    .price-strike {
        text-decoration: line-through;
        color: #9ca3af;
        font-size: 12px;
        margin-left: 6px;
    }
    .badge-off {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
</style>

<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
        <div>
            <h1 style="font-weight:700;font-size:24px;color:#111827;margin:0 0 6px 0;display:flex;align-items:center;gap:10px;">
                <i class="fa-solid fa-vials" style="color:#d4155b;"></i> Manage Diagnostic Lab Tests
            </h1>
            <p style="color:#6b7280;font-size:14px;margin:0;">
                Maintain test catalog, clinical descriptions, regular selling price, cut price (MRP), and discount % OFF.
            </p>
        </div>
        <div class="page-header-btns">
            <button class="btn-primary" onclick="openAddTestModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:600;background:#d4155b;color:#fff;border:none;cursor:pointer;box-shadow:0 2px 4px rgba(212,21,91,0.2);">
                <i class="fa-solid fa-plus"></i> Add New Lab Test
            </button>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
    <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:12px 18px;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 18px;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px;">
        <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
    </div>
    @endif

    <!-- SEARCH & FILTER BAR -->
    <div style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <form action="{{ route('admin.lab.tests') }}" method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            
            <!-- Search -->
            <div style="position:relative;min-width:240px;flex:1;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by test name, code, description..." style="width:100%;padding:9px 12px 9px 36px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
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
                    <i class="fa-solid fa-filter"></i> Filter
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
                        <th style="padding:14px 16px;font-weight:600;">Test Details &amp; Description</th>
                        <th style="padding:14px 16px;font-weight:600;">Laboratory</th>
                        <th style="padding:14px 16px;font-weight:600;">Category</th>
                        <th style="padding:14px 16px;font-weight:600;">Pricing &amp; Discount</th>
                        <th style="padding:14px 16px;font-weight:600;">Sample &amp; Turnaround</th>
                        <th style="padding:14px 16px;font-weight:600;text-align:center;">Status</th>
                        <th style="padding:14px 16px;font-weight:600;text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests as $test)
                    <tr style="border-bottom:1px solid #f3f4f6;transition:background 0.15s ease;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='#fff'">
                        
                        <!-- Test Details & Description -->
                        <td style="padding:14px 16px;vertical-align:top;max-width:320px;">
                            <div style="display:flex;align-items:flex-start;gap:12px;">
                                @if($test->image)
                                    <img src="{{ asset($test->image) }}" alt="{{ $test->test_name }}" style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:1px solid #e5e7eb;flex-shrink:0;">
                                @else
                                    <div style="width:44px;height:44px;background:#fdf2f8;color:#d4155b;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;border:1px solid #fbcfe8;">
                                        <i class="fa-solid fa-vial"></i>
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight:700;color:#111827;font-size:14px;line-height:1.3;">{{ $test->test_name }}</div>
                                    <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                                        Code: <span style="font-family:monospace;font-weight:600;color:#374151;">{{ $test->test_code ?? 'N/A' }}</span>
                                    </div>
                                    @if($test->description)
                                    <div style="font-size:12px;color:#4b5563;margin-top:4px;line-height:1.4;" title="{{ $test->description }}">
                                        {{ Str::limit($test->description, 80) }}
                                    </div>
                                    @endif
                                    @if($test->preparation_instructions)
                                    <div style="font-size:11px;color:#b45309;margin-top:4px;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> {{ $test->preparation_instructions }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Laboratory -->
                        <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                            <div style="font-weight:600;color:#111827;">{{ $test->lab->name ?? 'N/A' }}</div>
                            <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                                <i class="fa-solid fa-location-dot" style="font-size:10px;"></i> {{ $test->lab->city ?? 'Center' }}
                            </div>
                        </td>

                        <!-- Category -->
                        <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                            <span style="background:#f3f4f6;padding:4px 9px;border-radius:6px;font-size:12px;font-weight:600;color:#374151;border:1px solid #e5e7eb;">
                                {{ $test->category->name ?? 'General' }}
                            </span>
                        </td>

                        <!-- Pricing & Discount (Price, Cut Price, OFF %) -->
                        <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                            <div style="display:flex;align-items:baseline;gap:4px;">
                                <span style="font-size:16px;font-weight:700;color:#0f172a;">
                                    ₹{{ number_format($test->price, 2) }}
                                </span>
                                @if($test->cut_price && $test->cut_price > $test->price)
                                <del class="price-strike">₹{{ number_format($test->cut_price, 2) }}</del>
                                @elseif($test->discount_price && $test->discount_price < $test->price)
                                <del class="price-strike">₹{{ number_format($test->price, 2) }}</del>
                                @endif
                            </div>

                            @if($test->calculated_off > 0)
                            <div style="margin-top:4px;">
                                <span class="badge-off">
                                    <i class="fa-solid fa-tag" style="font-size:9px;"></i> {{ $test->calculated_off }}% OFF
                                </span>
                            </div>
                            @elseif($test->cut_price && $test->cut_price > $test->price)
                            @php
                                $offPct = round((($test->cut_price - $test->price) / $test->cut_price) * 100);
                            @endphp
                            <div style="margin-top:4px;">
                                <span class="badge-off">
                                    <i class="fa-solid fa-tag" style="font-size:9px;"></i> {{ $offPct }}% OFF
                                </span>
                            </div>
                            @endif
                        </td>

                        <!-- Sample & Turnaround -->
                        <td style="padding:14px 16px;vertical-align:top;white-space:nowrap;">
                            <div style="color:#111827;font-weight:500;">
                                <i class="fa-solid fa-droplet" style="color:#ef4444;margin-right:4px;font-size:11px;"></i> {{ $test->sample_type ?? 'Blood' }}
                            </div>
                            <div style="font-size:11px;color:#6b7280;margin-top:3px;">
                                <i class="fa-regular fa-clock" style="margin-right:4px;"></i> {{ $test->report_delivery_time ?? '24 Hours' }}
                            </div>
                        </td>

                        <!-- Status Toggle -->
                        <td style="padding:14px 16px;vertical-align:top;text-align:center;">
                            <form action="{{ route('admin.lab.tests.toggleStatus', $test->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" title="Click to toggle status" style="border:none;background:none;cursor:pointer;padding:0;">
                                    @if($test->status === 'active')
                                        <span style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                            <i class="fa-solid fa-circle" style="font-size:7px;"></i> Active
                                        </span>
                                    @else
                                        <span style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                            <i class="fa-solid fa-circle" style="font-size:7px;"></i> Inactive
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>

                        <!-- Actions -->
                        <td style="padding:14px 16px;vertical-align:top;text-align:right;white-space:nowrap;">
                            <div style="display:inline-flex;gap:6px;">
                                <button type="button" onclick="openPricingModal({{ json_encode($test) }})" title="Quick Update Price & Discount" style="padding:6px 10px;background:#fef3c7;color:#b45309;border:1px solid #fde68a;border-radius:6px;font-size:12px;cursor:pointer;font-weight:600;">
                                    <i class="fa-solid fa-indian-rupee-sign"></i> Price
                                </button>

                                <button type="button" onclick="openEditTestModal({{ json_encode($test) }})" title="Edit Complete Test" style="padding:6px 10px;background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;cursor:pointer;font-weight:600;">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>

                                <form action="{{ route('admin.lab.tests.destroy', $test->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete test \'{{ addslashes($test->test_name) }}\'?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Test" style="padding:6px 10px;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;border-radius:6px;font-size:12px;cursor:pointer;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:40px;text-align:center;color:#9ca3af;">
                            <i class="fa-solid fa-vial-virus" style="font-size:40px;margin-bottom:12px;display:block;color:#cbd5e1;"></i>
                            <div style="font-size:15px;font-weight:600;color:#64748b;">No diagnostic lab tests found</div>
                            <div style="font-size:12px;margin-top:4px;">Click <strong>Add New Lab Test</strong> to register a new test with pricing and descriptions.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- UNIVERSAL NUMBERED PAGINATION -->
        @if($tests->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #e5e7eb;background:#fff;">
            {{ $tests->appends(request()->query())->links('pagination.admin') }}
        </div>
        @endif
    </div>

    <!-- ADD TEST MODAL -->
    <div id="addTestModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:740px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;background:#f8fafc;">
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-circle-plus" style="color:#d4155b;"></i> Add New Diagnostic Lab Test
                </h3>
                <button type="button" onclick="closeAddTestModal()" style="border:none;background:none;font-size:20px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form action="{{ route('admin.lab.tests.store') }}" method="POST" enctype="multipart/form-data" style="padding:24px;">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Name *</label>
                        <input type="text" name="test_name" required placeholder="e.g. Complete Blood Count (CBC) or Lipid Profile" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Code</label>
                        <input type="text" name="test_code" placeholder="e.g. CBC001 (auto if empty)" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Laboratory *</label>
                        <select name="lab_id" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="">Select Partner Laboratory</option>
                            @foreach($labs as $l)
                            <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->city ?? 'Center' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Category *</label>
                        <select name="category_id" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="">Select Category</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PRICING SECTION -->
                    <div style="grid-column:1 / -1;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px;">
                        <div style="font-weight:700;font-size:13px;color:#1e293b;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-indian-rupee-sign" style="color:#059669;"></i> Pricing &amp; Discount Configuration
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">
                                    Selling Price (₹) *
                                </label>
                                <input type="number" step="0.01" id="add_price" name="price" required placeholder="350.00" oninput="calcAddDiscount()" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;font-weight:700;color:#0f172a;outline:none;">
                                <span style="font-size:11px;color:#64748b;">What patient pays</span>
                            </div>

                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">
                                    Cut Price / MRP (₹)
                                </label>
                                <input type="number" step="0.01" id="add_cut_price" name="cut_price" placeholder="500.00" oninput="calcAddDiscount()" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;outline:none;">
                                <span style="font-size:11px;color:#64748b;">Original strike-through price</span>
                            </div>

                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">
                                    Discount / OFF (%)
                                </label>
                                <input type="number" id="add_discount_percentage" name="discount_percentage" placeholder="30%" oninput="calcAddCutPrice()" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;font-weight:600;color:#059669;outline:none;">
                                <span style="font-size:11px;color:#059669;" id="add_off_preview">Auto-calculated</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Sample Type</label>
                        <input type="text" name="sample_type" placeholder="e.g. Blood (EDTA), Serum, Urine" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Report Delivery Time</label>
                        <input type="text" name="report_delivery_time" placeholder="e.g. 24 Hours, Same Day Evening" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Preparation Instructions</label>
                        <input type="text" name="preparation_instructions" placeholder="e.g. 10-12 hours overnight fasting required. Water intake allowed." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Description &amp; Details *</label>
                        <textarea name="description" rows="3" required placeholder="Detailed clinical significance, disease screening parameters, organs monitored..." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;"></textarea>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Catalog Status</label>
                        <select name="status" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="active" selected>Active (Visible in Patient App)</option>
                            <option value="inactive">Inactive (Hidden)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Thumbnail Image</label>
                        <input type="file" name="image" accept="image/*" style="width:100%;font-size:13px;">
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;border-top:1px solid #e5e7eb;padding-top:16px;">
                    <button type="button" onclick="closeAddTestModal()" style="padding:10px 20px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:10px 24px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 4px rgba(212,21,91,0.2);">Save Lab Test</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT TEST MODAL -->
    <div id="editTestModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:740px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;background:#f8fafc;">
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-pen-to-square" style="color:#d4155b;"></i> Edit Diagnostic Lab Test
                </h3>
                <button type="button" onclick="closeEditTestModal()" style="border:none;background:none;font-size:20px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form id="editTestForm" method="POST" enctype="multipart/form-data" style="padding:24px;">
                @csrf
                @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Name *</label>
                        <input type="text" id="edit_test_name" name="test_name" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Code</label>
                        <input type="text" id="edit_test_code" name="test_code" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Laboratory *</label>
                        <select id="edit_lab_id" name="lab_id" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            @foreach($labs as $l)
                            <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->city ?? 'Center' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Category *</label>
                        <select id="edit_category_id" name="category_id" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PRICING SECTION -->
                    <div style="grid-column:1 / -1;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px;">
                        <div style="font-weight:700;font-size:13px;color:#1e293b;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-indian-rupee-sign" style="color:#059669;"></i> Pricing &amp; Discount Configuration
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">
                                    Selling Price (₹) *
                                </label>
                                <input type="number" step="0.01" id="edit_price" name="price" required oninput="calcEditDiscount()" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;font-weight:700;color:#0f172a;outline:none;">
                                <span style="font-size:11px;color:#64748b;">What patient pays</span>
                            </div>

                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">
                                    Cut Price / MRP (₹)
                                </label>
                                <input type="number" step="0.01" id="edit_cut_price" name="cut_price" oninput="calcEditDiscount()" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;outline:none;">
                                <span style="font-size:11px;color:#64748b;">Original strike-through price</span>
                            </div>

                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">
                                    Discount / OFF (%)
                                </label>
                                <input type="number" id="edit_discount_percentage" name="discount_percentage" oninput="calcEditCutPrice()" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;font-weight:600;color:#059669;outline:none;">
                                <span style="font-size:11px;color:#059669;" id="edit_off_preview">Auto-calculated</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Sample Type</label>
                        <input type="text" id="edit_sample_type" name="sample_type" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Report Delivery Time</label>
                        <input type="text" id="edit_report_delivery_time" name="report_delivery_time" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Preparation Instructions</label>
                        <input type="text" id="edit_preparation_instructions" name="preparation_instructions" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Test Description &amp; Details *</label>
                        <textarea id="edit_description" name="description" rows="3" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;"></textarea>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Status</label>
                        <select id="edit_status" name="status" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="active">Active (Visible)</option>
                            <option value="inactive">Inactive (Hidden)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Update Thumbnail Image (Leave blank to keep current)</label>
                        <input type="file" name="image" accept="image/*" style="width:100%;font-size:13px;">
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;border-top:1px solid #e5e7eb;padding-top:16px;">
                    <button type="button" onclick="closeEditTestModal()" style="padding:10px 20px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:10px 24px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 4px rgba(212,21,91,0.2);">Update Lab Test</button>
                </div>
            </form>
        </div>
    </div>

    <!-- QUICK PRICING MODAL -->
    <div id="pricingModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#fff;border-radius:12px;max-width:480px;width:100%;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="padding:16px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;background:#f8fafc;">
                <h3 style="margin:0;font-size:15px;font-weight:700;color:#111827;display:flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-indian-rupee-sign" style="color:#d4155b;"></i> Quick Update Pricing &amp; Discount
                </h3>
                <button type="button" onclick="closePricingModal()" style="border:none;background:none;font-size:18px;cursor:pointer;color:#9ca3af;">&times;</button>
            </div>
            
            <form id="pricingForm" method="POST" style="padding:20px;">
                @csrf
                <div style="margin-bottom:16px;background:#fdf2f8;padding:12px;border-radius:8px;border:1px solid #fbcfe8;">
                    <div id="price_test_name" style="font-size:14px;font-weight:700;color:#831843;"></div>
                    <div id="price_lab_name" style="font-size:12px;color:#9d174d;margin-top:2px;"></div>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">
                        Selling Price (₹) *
                    </label>
                    <input type="number" step="0.01" id="pricing_price" name="price" required oninput="calcQuickDiscount()" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;font-weight:700;color:#0f172a;outline:none;">
                    <span style="font-size:11px;color:#64748b;">The final price paid by patient</span>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">
                        Cut Price / Original MRP (₹)
                    </label>
                    <input type="number" step="0.01" id="pricing_cut_price" name="cut_price" oninput="calcQuickDiscount()" placeholder="e.g. 500.00" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;outline:none;">
                    <span style="font-size:11px;color:#64748b;">Crossed-out original price</span>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">
                        Discount / OFF (%)
                    </label>
                    <input type="number" id="pricing_discount_percentage" name="discount_percentage" oninput="calcQuickCutPrice()" placeholder="e.g. 25" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;font-weight:600;color:#059669;outline:none;">
                    <span style="font-size:11px;color:#059669;" id="quick_off_preview">Auto-calculated</span>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #e5e7eb;padding-top:16px;">
                    <button type="button" onclick="closePricingModal()" style="padding:9px 18px;background:#f3f4f6;color:#4b5563;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Cancel</button>
                    <button type="submit" style="padding:9px 20px;background:#d4155b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 4px rgba(212,21,91,0.2);">Save Pricing</button>
                </div>
            </form>
        </div>
    </div>

</main>

<script>
    // Live Calculation Helpers
    function calcAddDiscount() {
        const price = parseFloat(document.getElementById('add_price').value) || 0;
        const cut = parseFloat(document.getElementById('add_cut_price').value) || 0;
        const preview = document.getElementById('add_off_preview');
        const offInput = document.getElementById('add_discount_percentage');

        if (cut > price && price > 0) {
            const pct = Math.round(((cut - price) / cut) * 100);
            offInput.value = pct;
            preview.innerText = 'Save ₹' + (cut - price).toFixed(2) + ' (' + pct + '% OFF)';
        } else {
            preview.innerText = 'Auto-calculated';
        }
    }

    function calcAddCutPrice() {
        const price = parseFloat(document.getElementById('add_price').value) || 0;
        const pct = parseFloat(document.getElementById('add_discount_percentage').value) || 0;
        const cutInput = document.getElementById('add_cut_price');
        const preview = document.getElementById('add_off_preview');

        if (price > 0 && pct > 0 && pct < 100) {
            const cut = (price / (1 - (pct / 100))).toFixed(2);
            cutInput.value = cut;
            preview.innerText = 'Save ₹' + (cut - price).toFixed(2) + ' (' + pct + '% OFF)';
        }
    }

    function calcEditDiscount() {
        const price = parseFloat(document.getElementById('edit_price').value) || 0;
        const cut = parseFloat(document.getElementById('edit_cut_price').value) || 0;
        const preview = document.getElementById('edit_off_preview');
        const offInput = document.getElementById('edit_discount_percentage');

        if (cut > price && price > 0) {
            const pct = Math.round(((cut - price) / cut) * 100);
            offInput.value = pct;
            preview.innerText = 'Save ₹' + (cut - price).toFixed(2) + ' (' + pct + '% OFF)';
        } else {
            preview.innerText = 'Auto-calculated';
        }
    }

    function calcEditCutPrice() {
        const price = parseFloat(document.getElementById('edit_price').value) || 0;
        const pct = parseFloat(document.getElementById('edit_discount_percentage').value) || 0;
        const cutInput = document.getElementById('edit_cut_price');
        const preview = document.getElementById('edit_off_preview');

        if (price > 0 && pct > 0 && pct < 100) {
            const cut = (price / (1 - (pct / 100))).toFixed(2);
            cutInput.value = cut;
            preview.innerText = 'Save ₹' + (cut - price).toFixed(2) + ' (' + pct + '% OFF)';
        }
    }

    function calcQuickDiscount() {
        const price = parseFloat(document.getElementById('pricing_price').value) || 0;
        const cut = parseFloat(document.getElementById('pricing_cut_price').value) || 0;
        const preview = document.getElementById('quick_off_preview');
        const offInput = document.getElementById('pricing_discount_percentage');

        if (cut > price && price > 0) {
            const pct = Math.round(((cut - price) / cut) * 100);
            offInput.value = pct;
            preview.innerText = 'Save ₹' + (cut - price).toFixed(2) + ' (' + pct + '% OFF)';
        } else {
            preview.innerText = 'Auto-calculated';
        }
    }

    function calcQuickCutPrice() {
        const price = parseFloat(document.getElementById('pricing_price').value) || 0;
        const pct = parseFloat(document.getElementById('pricing_discount_percentage').value) || 0;
        const cutInput = document.getElementById('pricing_cut_price');
        const preview = document.getElementById('quick_off_preview');

        if (price > 0 && pct > 0 && pct < 100) {
            const cut = (price / (1 - (pct / 100))).toFixed(2);
            cutInput.value = cut;
            preview.innerText = 'Save ₹' + (cut - price).toFixed(2) + ' (' + pct + '% OFF)';
        }
    }

    // Modal Triggers
    function openAddTestModal() {
        document.getElementById('addTestModal').style.display = 'flex';
    }
    function closeAddTestModal() {
        document.getElementById('addTestModal').style.display = 'none';
    }

    function openEditTestModal(test) {
        document.getElementById('editTestForm').action = "/lab/tests/" + test.id;
        document.getElementById('edit_test_name').value = test.test_name || '';
        document.getElementById('edit_test_code').value = test.test_code || '';
        document.getElementById('edit_lab_id').value = test.lab_id || '';
        document.getElementById('edit_category_id').value = test.category_id || '';
        document.getElementById('edit_price').value = test.price || '';
        document.getElementById('edit_cut_price').value = test.cut_price || '';
        document.getElementById('edit_discount_percentage').value = test.discount_percentage || '';
        document.getElementById('edit_sample_type').value = test.sample_type || '';
        document.getElementById('edit_report_delivery_time').value = test.report_delivery_time || '';
        document.getElementById('edit_preparation_instructions').value = test.preparation_instructions || '';
        document.getElementById('edit_status').value = test.status || 'active';
        document.getElementById('edit_description').value = test.description || '';
        calcEditDiscount();
        document.getElementById('editTestModal').style.display = 'flex';
    }
    function closeEditTestModal() {
        document.getElementById('editTestModal').style.display = 'none';
    }

    function openPricingModal(test) {
        document.getElementById('pricingForm').action = "/lab/tests/" + test.id + "/price";
        document.getElementById('price_test_name').innerText = test.test_name;
        document.getElementById('price_lab_name').innerText = test.lab ? test.lab.name : '';
        document.getElementById('pricing_price').value = test.price || '';
        document.getElementById('pricing_cut_price').value = test.cut_price || '';
        document.getElementById('pricing_discount_percentage').value = test.discount_percentage || '';
        calcQuickDiscount();
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
