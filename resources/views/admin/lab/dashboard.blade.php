@extends('admin.layouts.layout')

@section('content')
<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1>Lab Management Dashboard</h1>
            <p>Monitor laboratory partners, diagnostics tests catalog, pricing, and orders.</p>
        </div>
        <div class="page-header-btns" style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('admin.lab.setup') }}" class="btn-primary" style="background:#4b5563;border-color:#4b5563;text-decoration:none;padding:9px 16px;border-radius:8px;color:#fff;display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;">
                <i class="fa-solid fa-hospital"></i> Manage Labs
            </a>
            <a href="{{ route('admin.lab.categories') }}" class="btn-primary" style="background:#6366f1;border-color:#6366f1;text-decoration:none;padding:9px 16px;border-radius:8px;color:#fff;display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;">
                <i class="fa-solid fa-layer-group"></i> Categories
            </a>
            <a href="{{ route('admin.lab.tests') }}" class="btn-primary" style="text-decoration:none;padding:9px 16px;border-radius:8px;color:#fff;display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;">
                <i class="fa-solid fa-plus"></i> Add / Manage Tests
            </a>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(210px, 1fr));gap:16px;margin-bottom:24px;">
        <div class="stat-card" style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <div class="stat-label" style="font-size:13px;color:#6b7280;font-weight:500;">Total Laboratories</div>
                <div style="width:36px;height:36px;background:#fce8f3;color:#d4155b;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;">
                    <i class="fa-solid fa-hospital"></i>
                </div>
            </div>
            <div class="stat-value" style="font-size:26px;font-weight:700;color:#111827;">{{ $totalLabs }}</div>
            <div class="stat-sub" style="font-size:12px;color:#059669;margin-top:6px;">
                <i class="fa-solid fa-circle-check"></i> {{ $activeLabs }} Active &bull; <span style="color:#6b7280;">{{ $inactiveLabs }} Inactive</span>
            </div>
        </div>

        <div class="stat-card" style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <div class="stat-label" style="font-size:13px;color:#6b7280;font-weight:500;">Total Lab Tests</div>
                <div style="width:36px;height:36px;background:#ecfdf5;color:#059669;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;">
                    <i class="fa-solid fa-vial-virus"></i>
                </div>
            </div>
            <div class="stat-value" style="font-size:26px;font-weight:700;color:#111827;">{{ $totalTests }}</div>
            <div class="stat-sub" style="font-size:12px;color:#059669;margin-top:6px;">
                <i class="fa-solid fa-circle-check"></i> {{ $activeTests }} Active &bull; <span style="color:#6b7280;">{{ $inactiveTests }} Inactive</span>
            </div>
        </div>

        <div class="stat-card" style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <div class="stat-label" style="font-size:13px;color:#6b7280;font-weight:500;">Test Categories</div>
                <div style="width:36px;height:36px;background:#e0e7ff;color:#4f46e5;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>
            <div class="stat-value" style="font-size:26px;font-weight:700;color:#111827;">{{ $totalCategories }}</div>
            <div class="stat-sub" style="font-size:12px;color:#6b7280;margin-top:6px;">
                Across pathology, imaging &amp; blood
            </div>
        </div>

        <div class="stat-card" style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <div class="stat-label" style="font-size:13px;color:#6b7280;font-weight:500;">Lab Orders</div>
                <div style="width:36px;height:36px;background:#fef3c7;color:#d97706;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>
            <div class="stat-value" style="font-size:26px;font-weight:700;color:#111827;">{{ $totalOrders }}</div>
            <div class="stat-sub" style="font-size:12px;color:#6b7280;margin-top:6px;">
                Ready for bookings module
            </div>
        </div>
    </div>

    <!-- MAIN TWO COLUMN GRID -->
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px;">
        
        <!-- RECENT TESTS TABLE -->
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div>
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;">Recently Added Lab Tests</h3>
                    <p style="margin:4px 0 0;font-size:13px;color:#6b7280;">Latest diagnostic offerings available on the platform</p>
                </div>
                <a href="{{ route('admin.lab.tests') }}" style="font-size:13px;color:#d4155b;text-decoration:none;font-weight:600;">
                    View All <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;color:#6b7280;">
                            <th style="padding:10px 8px;font-weight:600;">Test Name</th>
                            <th style="padding:10px 8px;font-weight:600;">Lab</th>
                            <th style="padding:10px 8px;font-weight:600;">Category</th>
                            <th style="padding:10px 8px;font-weight:600;">Price</th>
                            <th style="padding:10px 8px;font-weight:600;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTests as $test)
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:12px 8px;">
                                <div style="font-weight:600;color:#111827;">{{ $test->test_name }}</div>
                                <div style="font-size:11px;color:#9ca3af;">Code: {{ $test->test_code ?? 'N/A' }}</div>
                            </td>
                            <td style="padding:12px 8px;color:#374151;">
                                {{ $test->lab->name ?? 'N/A' }}
                            </td>
                            <td style="padding:12px 8px;">
                                <span style="background:#f3f4f6;padding:3px 8px;border-radius:4px;font-size:11px;color:#4b5563;">
                                    {{ $test->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td style="padding:12px 8px;">
                                <span style="font-weight:700;color:#111827;">₹{{ number_format($test->price, 2) }}</span>
                                @if($test->discount_price)
                                <div style="font-size:11px;color:#059669;">Deal: ₹{{ number_format($test->discount_price, 2) }}</div>
                                @endif
                            </td>
                            <td style="padding:12px 8px;">
                                @if($test->status === 'active')
                                    <span style="background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">Active</span>
                                @else
                                    <span style="background:#fee2e2;color:#b91c1c;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding:24px;text-align:center;color:#9ca3af;">
                                No lab tests added yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT SIDE: PARTNER LABS & CATEGORIES -->
        <div style="display:flex;flex-direction:column;gap:20px;">
            
            <!-- TOP LABS -->
            <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <h3 style="margin:0;font-size:15px;font-weight:700;color:#111827;">Laboratories</h3>
                    <a href="{{ route('admin.lab.setup') }}" style="font-size:12px;color:#d4155b;text-decoration:none;font-weight:600;">Setup <i class="fa-solid fa-chevron-right"></i></a>
                </div>
                <div>
                    @forelse($topLabs as $lab)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if($lab->logo)
                                <img src="{{ asset($lab->logo) }}" alt="{{ $lab->name }}" style="width:34px;height:34px;border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;">
                            @else
                                <div style="width:34px;height:34px;background:#fce8f3;color:#d4155b;border-radius:6px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                                    {{ strtoupper(substr($lab->name, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <div style="font-size:13px;font-weight:600;color:#111827;">{{ $lab->name }}</div>
                                <div style="font-size:11px;color:#6b7280;">{{ $lab->city ? $lab->city . ', ' . $lab->state : 'Location N/A' }}</div>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <span style="background:#eff6ff;color:#2563eb;padding:3px 8px;border-radius:12px;font-size:11px;font-weight:600;">
                                {{ $lab->tests_count }} tests
                            </span>
                        </div>
                    </div>
                    @empty
                    <div style="padding:16px 0;text-align:center;color:#9ca3af;font-size:13px;">No labs registered yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- CATEGORIES LIST -->
            <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <h3 style="margin:0;font-size:15px;font-weight:700;color:#111827;">Test Categories</h3>
                    <a href="{{ route('admin.lab.categories') }}" style="font-size:12px;color:#d4155b;text-decoration:none;font-weight:600;">All <i class="fa-solid fa-chevron-right"></i></a>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @forelse($categories as $cat)
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-flask" style="color:#6366f1;font-size:12px;"></i>
                        <span style="font-size:12px;font-weight:600;color:#1e293b;">{{ $cat->name }}</span>
                        <span style="background:#e2e8f0;color:#475569;font-size:11px;padding:1px 6px;border-radius:10px;">{{ $cat->tests_count }}</span>
                    </div>
                    @empty
                    <div style="color:#9ca3af;font-size:13px;">No categories configured.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</main>
@endsection
