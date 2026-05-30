@extends('admin.layouts.layout')

@section('content')

<div class="st-content">

    <h1 class="st-title">Account Settings</h1>

    <p class="st-sub">
        Manage your clinical workstation preferences, security, and team integrations.
    </p>

    <div class="st-grid">

        <!-- LEFT -->
        <div>

            <!-- PROFILE -->
            <div class="st-card st-profile-card">

                <!-- EDIT BUTTON (FIXED) -->
                <div class="st-edit" onclick="openProfileModal()">Edit</div>

                <img src="{{ asset('uploads/profile/' . (auth()->user()->profile_img ?? 'default.jpg')) }}" class="st-user-img">

                <div class="st-camera">📷</div>

                <h3>{{ auth()->user()->name ?? 'User' }}</h3>

                <p>
                    {{ auth()->user()->email ?? 'Email not provided' }}
                </p>

                <div class="st-line"></div>

                <div class="st-info">
                    <span>Last login</span>
                    <strong>2 hours ago</strong>
                </div>

                <div class="st-info">
                    <span>Clinic ID</span>
                    <strong>MF-88291-TX</strong>
                </div>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="st-right-grid">

            <!-- ROLE -->
            <div class="st-card">

                <div class="st-role-header">

                    <div>
                        <h3 style="font-size:16px;">Role Management</h3>
                        <p style="font-size:12px;color:#777;margin-top:4px;">
                            Define and assign access levels across the clinic.
                        </p>
                    </div>

                    <!-- CREATE ROLE FIXED -->
                    <button onclick="openRoleModal()">＋ Create Role</button>

                </div>

                <div style="overflow:auto;">

                    <table class="st-role-table">

                        <thead>
                            <tr>
                                <th>USER NAME</th>
                                <th>EMAIL</th>
                                <th>ROLE</th>
                                <th>STATUS</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($roles as $role)

                            <tr>

                                <td>
                                    <strong>{{ $role->name ?? 'User' }}</strong><br>
                                    <span style="font-size:12px;color:#777;">
                                        Custom Role
                                    </span>
                                </td>

                                <td>{{ $role->email ?? 'Email not provided' }}</td>

                                <td>
                                    <span class="st-pill">ADMIN</span>
                                </td>

                                <td>
                                    <span class="st-pill st-active-pill">{{ $role->status ?? 'Status not provided' }}</span>
                                </td>


                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- ================= MODALS ================= -->

<!-- PROFILE MODAL -->
<div id="profileModal"
    style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:#0008;z-index:999;">

    <div style="background:#fff;width:400px;margin:80px auto;padding:20px;border-radius:10px;">

        <h3>Update Profile</h3>

        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf

            <input type="file" name="profile_img" class="form-control"><br>

            <input type="text" name="name" value="{{ auth()->user()->name ?? 'User' }}"><br>

            <input type="email" name="email" value="{{ auth()->user()->email ?? 'Email not provided' }}"><br>

            <input type="text" name="number" value="{{ auth()->user()->number ?? 'Number not provided' }}"><br>

            <button type="submit">Update</button>

        </form>

    </div>

</div>

<!-- ROLE MODAL -->
<div id="roleModal"
    style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:#0008;z-index:999;">

    <div style="background:#fff;width:400px;margin:80px auto;padding:20px;border-radius:10px;">

        <h3>Create Role</h3>

        <form method="POST" action="{{ route('admin.role.store') }}">
            @csrf

            <input type="text" name="name"
                placeholder="User Name"
                class="form-control"><br>

            <input type="email" name="email"
                placeholder="Email"
                class="form-control"><br>

            <input type="password" name="password"
                placeholder="Password"
                class="form-control"><br>

            <input type="hidden" name="role" value="1">

            <button type="submit" class="btn btn-success">Save</button>

            <button type="button" onclick="closeRoleModal()" class="btn btn-danger">Close</button>

        </form>

    </div>

</div>
<script src="{{ asset('js/main.js') }}"></script>
<!-- ================= SCRIPT ================= -->

<script>
    function openProfileModal() {
        document.getElementById('profileModal').style.display = 'block';
    }

    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    function openRoleModal() {
        document.getElementById('roleModal').style.display = 'block';
    }

    function closeRoleModal() {
        document.getElementById('roleModal').style.display = 'none';
    }
</script>

@endsection