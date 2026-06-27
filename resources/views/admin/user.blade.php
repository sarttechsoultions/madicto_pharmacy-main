@extends('admin.layouts.layout')

@section('content')

<div class="um-content">

    <div class="um-header">

        <div>
            <h1 class="um-title">Users Management</h1>
            <p class="um-subtitle">
                Manage and monitor patient accounts and their order activity.
            </p>
        </div>

        <a href="{{ route('users.export') }}" style="text-decoration: none;"
            class="um-btn um-btn-outline um-btn-users">
            ⬇ Export CSV
        </a>

    </div>

    <div class="stats-grid">
        <div class="stat-card" onclick="filterByStatus('All Status')">
            <div class="stat-top">
                <div class="stat-icon blue"><i class="fa-solid fa-layer-group"></i></div>
            </div>
            <div class="stat-label">Total Users</div>
            <div class="stat-value" id="statTotal">{{ $userall }}</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('Active')">
            <div class="stat-top">

                <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
            </div>
            <div class="stat-label">Active Users</div>
            <div class="stat-value" id="statLow">{{ $useractive }}</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('blocked')">
            <div class="stat-top">
                <div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
            </div>
            <div class="stat-label">Blocked Users</div>
            <div class="stat-value" id="statIn">{{ $userblocked }}</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('Admin')">
            <div class="stat-top">
                <div class="stat-icon purple"><i class="fa-solid fa-user-gear"></i></div>
            </div>
            <div class="stat-label">Admin Users</div>
            <div class="stat-value" id="statAdmin">{{ $admins }}</div>
        </div>
    </div>

    <!-- FILTER -->
    <form method="GET">

        <div class="um-filter">

            <div class="um-filter-input">
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Filter by name or email..."
                    onchange="this.form.submit()">
            </div>

            <div class="um-select-wrap">
                <select class="um-select"
                    name="status"
                    onchange="this.form.submit()">

                    <option value="all">All Statuses</option>

                    <option value="Active"
                        {{ request('status')=='Active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="blocked"
                        {{ request('status')=='blocked' ? 'selected' : '' }}>
                        Blocked
                    </option>

                </select>
            </div>

            <div class="um-select-wrap">
                <select class="um-select"
                    name="date"
                    onchange="this.form.submit()">

                    <option value="all">All</option>

                    <option value="30"
                        {{ request('date')=='30' ? 'selected' : '' }}>
                        Joined (Last 30 Days)
                    </option>

                    <option value="7"
                        {{ request('date')=='7' ? 'selected' : '' }}>
                        Last 7 Days
                    </option>

                    <option value="90"
                        {{ request('date')=='90' ? 'selected' : '' }}>
                        Last 3 Months
                    </option>

                    <option value="all"
                        {{ request('date')=='all' ? 'selected' : '' }}>
                        All Time
                    </option>

                </select>
            </div>

            <a href="{{ route('user.index') }}"
                class="um-clear">
                Clear All
            </a>

        </div>

    </form>

    <!-- TABLE -->

    <div class="um-card">

        <div class="um-table-wrap">
            <table class="um-table">

                <thead>
                    <tr>
                        <th>USER PROFILE</th>
                        <th>MOBILE NUMBER</th>
                        <th>TOTAL ORDERS</th>
                        <th>WALLET BALANCE</th>
                        <th>JOINED DATE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($user as $users)

                    <tr data-created="{{ $users->created_at }}">
                        <td>
                            <div class="um-user">
                                <a href="{{ route('user.show',$users->id) }}">

                                    <div class="um-user-img">

                                        {{ strtoupper(substr($users->name,0,2)) }}

                                    </div>

                                </a>
                                <div>
                                    <h4>

                                        <a href="{{ route('user.show',$users->id) }}"
                                            class="text-decoration-none text-dark">

                                            {{ $users->name }}

                                        </a>

                                    </h4>
                                    <p>{{ $users->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>{{ $users->number ?? 'N/A' }}</td>
                        <td>
                            <div class="um-order">{{ $users->orders->count() }} Orders</div>
                        </td>
                        <td>
                            <div class="um-wallet">₹ {{ number_format($users->wallet, 2) }}</div>
                        </td>
                        <td>{{ $users->created_at->format('M j, Y') }}</td>
                        <td>
                            <div class="um-actions-icons">

                                <!-- STATUS TOGGLE -->
                                <span class="toggle-status"
                                    data-id="{{ $users->id }}"
                                    data-status="{{ $users->status }}"
                                    onclick="toggleUserStatus(this)">

                                    @if($users->status == 'Active')
                                    🔓
                                    @elseif($users->status == 'blocked')
                                    🚫
                                    @endif

                                </span>

                                <!-- ROLE TOGGLE BUTTON -->
                                <!-- <button
                                    class="role-btn"
                                    onclick="toggleUserRole({{ $users->id }})">

                                    @if($users->role == 1)
                                    👤 Admin
                                    @else
                                    👥 User
                                    @endif

                                </button> -->

                            </div>
                        </td>
                    </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

        <div class="um-footer">
            <div>Showing {{ $user->firstItem() }} to {{ $user->lastItem() }} of {{ $user->total() }} users</div>

            <div class="um-pagination">
                {{ $user->links() }}

            </div>

        </div>

    </div>

    </main>

</div>

<script>
    const umToggle = document.getElementById('umToggle');
    const umSidebar = document.getElementById('umSidebar');
    const umMain = document.getElementById('umMain');

    umToggle.addEventListener('click', () => {

        if (window.innerWidth <= 768) {

            umSidebar.classList.toggle('um-show');

        } else {

            umSidebar.classList.toggle('um-hide');
            umMain.classList.toggle('um-expand');

        }

    });

    // OUTSIDE CLICK CLOSE MOBILE

    document.addEventListener('click', function(e) {

        if (
            window.innerWidth <= 768 &&
            !umSidebar.contains(e.target) &&
            !umToggle.contains(e.target)
        ) {

            umSidebar.classList.remove('um-show');

        }

    });
</script>

<script>
    function toggleUserStatus(el) {

        let userId = el.dataset.id;
        let currentStatus = el.dataset.status;

        let newStatus = (currentStatus === 'Active') ? 'blocked' : 'Active';

        fetch('/user/toggle-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: userId,
                    status: newStatus
                })
            })
            .then(res => res.json())
            .then(data => {

                if (data.success) {

                    // update dataset
                    el.dataset.status = newStatus;

                    // change icon
                    el.innerHTML = (newStatus === 'Active') ? '🔓' : '🚫';
                }

            });

    }
</script>

<script>
    function toggleUserRole(userId) {

        fetch("{{ route('admin.toggle.role') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    id: userId
                })
            })
            .then(res => res.json())
            .then(data => {

                alert(data.message);

                if (data.status) {
                    location.reload();
                }

            })
            .catch(error => {
                console.log(error);
            });

    }
</script>
<script src="{{ asset('js/main.js') }}"></script>

</body>

</html>

@endsection