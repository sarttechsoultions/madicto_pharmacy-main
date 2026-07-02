@extends('admin.layouts.layout')

@section('content')


<div class="notification-page">

    <!-- Header -->
    <div class="notification-header">
        <div>
            <h2 class="notification-title">
                <i class="fa fa-bell"></i>
                Notification Center
            </h2>
            <p class="notification-subtitle">
                Manage and send push notifications to all users.
            </p>
        </div>

        <button class="nf-btn nf-btn-danger" id="openNotificationModal">
            <i class="fa fa-paper-plane"></i>
            Send Notification
        </button>
    </div>

    <!-- Statistics -->
    <div class="nf-grid mt-4">

        <div class="nf-col-3 mb-4">
            <div class="notify-card total">
                <div class="notify-icon"><i class="fa fa-bell"></i></div>
                <div>
                    <h6>Total Notifications</h6>
                    <h2>{{ $notifications->count() }}</h2>
                </div>
            </div>
        </div>

        <div class="nf-col-3 mb-4">
            <div class="notify-card success">
                <div class="notify-icon"><i class="fa fa-check-circle"></i></div>
                <div>
                    <h6>Sent Today</h6>
                    <h2>{{ $todayCount ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="nf-col-3 mb-4">
            <div class="notify-card users">
                <div class="notify-icon"><i class="fa fa-users"></i></div>
                <div>
                    <h6>Total Users</h6>
                    <h2>{{ $totalUsers ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="nf-col-3 mb-4">
            <div class="notify-card failed">
                <div class="notify-icon"><i class="fa fa-times-circle"></i></div>
                <div>
                    <h6>Failed</h6>
                    <h2>{{ $failedCount ?? 0 }}</h2>
                </div>
            </div>
        </div>

    </div>

    <!-- Search + Filters -->
    <div class="notification-toolbar">
        <div class="nf-grid" style="align-items:center;">

            <div class="nf-col-6">
                <form onsubmit="return false;">
                    <div class="nf-search-wrap">
                        <span class="nf-search-icon"><i class="fa fa-search"></i></span>
                        <input type="text" id="notificationSearch" class="nf-input" placeholder="Search notification title...">
                    </div>
                </form>
            </div>

            <div class="nf-col-6 text-end">
                <select class="nf-select notification-filter" id="notificationFilter">
                    <option value="all">All Notifications</option>
                    <option value="today">Today</option>
                    <option value="this week">This Week</option>
                    <option value="sent">Success</option>
                    <option value="failed">Failed</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Quick Filters -->
    <div class="notification-tags mt-4">
        <button class="tag active">All</button>
        <button class="tag">Promotion</button>
        <button class="tag">Order</button>
        <button class="tag">Offers</button>
        <button class="tag">Reminder</button>
        <button class="tag">System</button>
    </div>

    <!-- Notification Table Card -->
    <div class="nf-card mt-4">
        <div class="nf-card-header">
            <h5>Notification History</h5>
            <span class="nf-badge bg-danger">{{ $notifications->count() }} Notifications</span>
        </div>

        <div class="nf-table-wrap">
            <table class="nf-table">
                <thead>
                    <tr>
                        <th width="70">#</th>
                        <th>Notification</th>
                        <th>Type</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="170" style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($notifications as $notification)
                    <tr class="notification-row" data-status="{{ strtolower($notification->status) }}">

                        <td>{{ $loop->iteration }}</td>

                        <td>
                            <div class="nf-row-flex">
                                <div class="notify-list-icon"><i class="fa fa-bell"></i></div>
                                <div class="ms-3">
                                    <h6>{{ $notification->title }}</h6>
                                    <small>{{ \Illuminate\Support\Str::limit($notification->message,70) }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            @switch($notification->type)
                            @case('Promotion')
                            <span class="nf-badge bg-success">Promotion</span>
                            @break
                            @case('Order')
                            <span class="nf-badge bg-primary">Order</span>
                            @break
                            @case('Reminder')
                            <span class="nf-badge bg-warning">Reminder</span>
                            @break
                            @default
                            <span class="nf-badge bg-secondary">System</span>
                            @endswitch
                        </td>

                        <td><span class="fw-bold">{{ $notification->total_users ?? $totalUsers }}</span></td>

                        <td>
                            @if($notification->status=="Sent")
                            <span class="nf-badge bg-success"><i class="fa fa-check-circle"></i> Sent</span>
                            @elseif($notification->status=="Failed")
                            <span class="nf-badge bg-danger"><i class="fa fa-times-circle"></i> Failed</span>
                            @else
                            <span class="nf-badge bg-warning">Pending</span>
                            @endif
                        </td>

                        <td>
                            {{ $notification->created_at->format('d M Y') }}
                            <br>
                            <small class="text-muted">{{ $notification->created_at->format('h:i A') }}</small>
                        </td>

                        <td>
                            <div class="nf-actions">
                                <button class="nf-icon-btn view" title="View"><i class="fa fa-eye"></i></button>
                                <button class="nf-icon-btn resend" title="Resend"><i class="fa fa-paper-plane"></i></button>
                                <button
                                    class="nf-icon-btn delete deleteNotification"
                                    data-url="{{ route('admin.notification.delete', $notification->id) }}"
                                    title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="nf-empty">
                                <h5>No Notifications Found</h5>
                                <p class="text-muted">Notifications you send will appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    <!-- Send Notification Modal -->
    <div class="custom-modal" id="sendNotificationModal">
        <div class="modal-box">
            <form action="{{ route('admin.notification.send') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h3 class="modal-title">Send Notification</h3>
                    <button type="button" id="closeNotificationModal" class="modal-close">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Notification Title</label>
                        <input class="nf-input" type="text" name="title" placeholder="Notification Title">
                    </div>

                    <div class="form-group">
                        <label>Message</label>
                        <textarea class="nf-textarea" name="message" rows="4" placeholder="Write Notification"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Type</label>
                            <select class="nf-select" name="type">
                                <option>General</option>
                                <option>Promotion</option>
                                <option>Order</option>
                                <option>Reminder</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" id="notificationImage" class="nf-input" name="image">
                        </div>
                    </div>

                    <div class="preview-card">
                        <div class="preview-icon"><i class="fa fa-bell"></i></div>
                        <div class="preview-content">
                            <h4 id="previewTitle">Notification Title</h4>
                            <p id="previewMessage">Write your notification...</p>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="nf-btn nf-btn-light" id="closeNotificationModal2">Cancel</button>
                    <button type="submit" class="nf-btn nf-btn-danger">Send Notification</button>
                </div>

            </form>
        </div>
    </div>

    <!-- View Notification Modal -->
    <div class="nf-modal" id="viewNotificationModal">
        <div class="nf-modal-box nf-modal-lg">

            <div class="nf-modal-header">
                <div>
                    <h4><i class="fa fa-bell" style="color:#2563eb;"></i> Notification Details</h4>
                    <small>View complete notification information</small>
                </div>
                <button class="nf-modal-close" data-close="viewNotificationModal">&times;</button>
            </div>

            <div class="nf-modal-body">

                <div class="nf-grid">
                    <div class="nf-col-12 mb-4">
                        <div class="detail-card">
                            <h5>{{ $notification->title ?? 'Notification Title' }}</h5>
                            <p class="text-muted">{{ $notification->message ?? 'Notification Message' }}</p>
                        </div>
                    </div>
                </div>

                <div class="nf-grid">
                    <div class="nf-col-3">
                        <div class="stats-box success">
                            <h3>{{ $notification->success_count ?? 0 }}</h3>
                            <small>Delivered</small>
                        </div>
                    </div>
                    <div class="nf-col-3">
                        <div class="stats-box danger">
                            <h3>{{ $notification->failed_count ?? 0 }}</h3>
                            <small>Failed</small>
                        </div>
                    </div>
                    <div class="nf-col-3">
                        <div class="stats-box primary">
                            <h3>{{ $notification->opened_count ?? 0 }}</h3>
                            <small>Opened</small>
                        </div>
                    </div>
                    <div class="nf-col-3">
                        <div class="stats-box warning">
                            <h3>{{ $notification->total_users ?? 0 }}</h3>
                            <small>Users</small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="nf-grid">
                    <div class="nf-col-6">
                        <label class="fw-bold">Notification Type</label>
                        <p>{{ $notification->type ?? 'General' }}</p>
                    </div>
                    <div class="nf-col-6">
                        <label class="fw-bold">Status</label>
                        <p><span class="nf-badge bg-success">{{ $notification->status ?? 'Sent' }}</span></p>
                    </div>
                    <div class="nf-col-6">
                        <label class="fw-bold">Sent To</label>
                        <p>{{ $notification->send_to ?? 'All Users' }}</p>
                    </div>
                    <div class="nf-col-6">
                        <label class="fw-bold">Created At</label>
                        <p>{{ isset($notification) ? $notification->created_at->format('d M Y h:i A') : now()->format('d M Y h:i A') }}</p>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Delivery Timeline</h5>
                <ul class="timeline">
                    <li><span class="timeline-dot bg-primary"></span> Notification Created</li>
                    <li><span class="timeline-dot bg-success"></span> Notification Sent</li>
                    <li><span class="timeline-dot bg-info"></span> Delivered to Devices</li>
                    <li><span class="timeline-dot bg-warning"></span> Opened by Users</li>
                </ul>

            </div>

            <div class="nf-modal-footer">
                <button class="nf-btn nf-btn-secondary" data-close="viewNotificationModal">Close</button>
                <button class="nf-btn nf-btn-success"><i class="fa fa-paper-plane"></i> Resend</button>
            </div>

        </div>
    </div>

    <!-- Delete Modal -->
    <div class="nf-modal" id="deleteNotificationModal">
        <div class="nf-modal-box nf-modal-sm">
            <form method="POST" id="deleteNotificationForm" action="">
                @csrf
                @method('DELETE')
                <div class="nf-delete-body">
                    <i class="fa fa-trash"></i>
                    <h3>Delete Notification?</h3>
                    <p class="text-muted">This action cannot be undone.</p>

                    <div class="nf-delete-actions">
                        <button class="nf-btn nf-btn-danger" type="submit">
                            Delete
                        </button>

                        <button
                            type="button"
                            class="nf-btn nf-btn-light"
                            data-close="deleteNotificationModal">
                            Cancel
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script>
    /*=====================================
        Notification Dashboard JS (No Bootstrap)
        ======================================*/

    const modal = document.getElementById("sendNotificationModal");
    const openBtn = document.getElementById("openNotificationModal");
    const closeBtn = document.getElementById("closeNotificationModal");
    const closeBtn2 = document.getElementById("closeNotificationModal2");

    function openSendModal() {
        modal.classList.add("show");
        document.body.style.overflow = "hidden";
    }

    function closeSendModal() {
        modal.classList.remove("show");
        document.body.style.overflow = "auto";
    }

    if (openBtn) openBtn.onclick = openSendModal;
    if (closeBtn) closeBtn.onclick = closeSendModal;
    if (closeBtn2) closeBtn2.onclick = closeSendModal;

    window.addEventListener("click", function(e) {
        if (e.target === modal) closeSendModal();
    });

    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") {
            closeSendModal();
            document.querySelectorAll(".nf-modal.show").forEach(function(m) {
                m.classList.remove("show");
            });
            document.body.style.overflow = "auto";
        }
    });

    /*=====================================
        Generic Modal Open/Close (View / Delete)
    ======================================*/

    function openNfModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add("show");
            document.body.style.overflow = "hidden";
        }
    }

    function closeNfModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove("show");
            document.body.style.overflow = "auto";
        }
    }

    document.querySelectorAll("[data-close]").forEach(function(btn) {
        btn.addEventListener("click", function() {
            closeNfModal(this.dataset.close);
        });
    });

    document.querySelectorAll(".nf-modal").forEach(function(m) {
        m.addEventListener("click", function(e) {
            if (e.target === m) {
                m.classList.remove("show");
                document.body.style.overflow = "auto";
            }
        });
    });

    // Hook up "View" buttons in the table to open the view modal
    document.querySelectorAll(".nf-icon-btn.view").forEach(function(btn) {
        btn.addEventListener("click", function() {
            openNfModal("viewNotificationModal");
        });
    });

    /*=====================================
        Live Preview
    ======================================*/

    const titleInput = document.querySelector("input[name='title']");
    const messageInput = document.querySelector("textarea[name='message']");

    const previewTitle = document.getElementById("previewTitle");
    const previewMessage = document.getElementById("previewMessage");

    if (titleInput) {
        titleInput.addEventListener("keyup", function() {
            previewTitle.innerHTML = this.value || "Notification Title";
        });
    }

    if (messageInput) {
        messageInput.addEventListener("keyup", function() {
            previewMessage.innerHTML = this.value || "Write your notification message...";
        });
    }

    /*=====================================
        Image Preview
    ======================================*/

    const imageInput = document.getElementById("notificationImage");
    const previewImage = document.getElementById("previewImage");

    if (imageInput) {
        imageInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file && previewImage) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    previewImage.style.display = "block";
                }
                reader.readAsDataURL(file);
            }
        }
    }

    /*=====================================
        Search Notification
    ======================================*/

    const search = document.getElementById("notificationSearch");

    if (search) {
        search.addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll(".notification-row");

            rows.forEach(function(row) {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    }

    /*=====================================
        Filter Status
    ======================================*/

    const filter = document.getElementById("notificationFilter");

    if (filter) {
        filter.addEventListener("change", function() {
            let value = this.value.toLowerCase();
            let rows = document.querySelectorAll(".notification-row");

            rows.forEach(function(row) {
                if (value === "all") {
                    row.style.display = "";
                    return;
                }
                let status = row.dataset.status.toLowerCase();
                row.style.display = status === value ? "" : "none";
            });
        });
    }

    /*=====================================
        Quick Filter Tags (active state)
    ======================================*/

    document.querySelectorAll(".tag").forEach(function(tag) {
        tag.addEventListener("click", function() {
            document.querySelectorAll(".tag").forEach(t => t.classList.remove("active"));
            this.classList.add("active");
        });
    });

    /*=====================================
        Delete Confirm
    ======================================*/

    document.querySelectorAll(".deleteNotification").forEach(function(btn) {

        btn.addEventListener("click", function() {

            let url = this.dataset.url;

            document.getElementById("deleteNotificationForm").action = url;

            openNfModal("deleteNotificationModal");

        });

    });

    /*=====================================
        Toast Message
    ======================================*/

    function showToast(message, color = "#2563eb") {
        const toast = document.createElement("div");
        toast.innerHTML = message;
        toast.style.position = "fixed";
        toast.style.top = "30px";
        toast.style.right = "30px";
        toast.style.background = color;
        toast.style.color = "#fff";
        toast.style.padding = "15px 25px";
        toast.style.borderRadius = "10px";
        toast.style.boxShadow = "0 10px 30px rgba(0,0,0,.25)";
        toast.style.zIndex = "999999";
        toast.style.opacity = "0";
        toast.style.transition = ".3s";

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = "1";
        }, 100);

        setTimeout(() => {
            toast.style.opacity = "0";
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    /*=====================================
        Auto Success / Error Message
    ======================================*/

    const success = document.getElementById("successMessage");
    if (success) showToast(success.innerHTML, "#16a34a");

    const error = document.getElementById("errorMessage");
    if (error) showToast(error.innerHTML, "#dc2626");
</script>

</div>

@endsection