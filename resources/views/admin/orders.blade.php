@extends('admin.layouts.layout')

@section('content')


<!-- CONTENT -->

<div class="od-content">

  <!-- HEADER -->

  <div class="od-header">

    <div>
      <h1>Order Fulfillment</h1>
      <p>Manage prescription orders and pharmacy delivery cycles.</p>
    </div>


  </div>

  <div class="od-cards">

    <div class="od-card">
      <h4>Pending Orders</h4>
      <h2>{{ $ordertotal }}</h2>
    </div>

    <div class="od-card">
      <h4>Delivered Orders</h4>
      <h2>{{ $orderdelivered }}</h2>
    </div>

    <div class="od-card">
      <h4>Pending Payments</h4>
      <h2>{{ $paymentpending }}</h2>
    </div>

    <div class="od-card">
      <h4>Paid Payments</h4>
      <h2>{{ $paymentpaid }}</h2>
    </div>

  </div>

  <div class="od-table-box">

    <div class="od-table-top">

      <div class="od-tabs">
        <div class="od-tab od-active" data-status="All">All Orders</div>
        <div class="od-tab" data-status="Pending">Pending</div>
        <div class="od-tab" data-status="Shipped">Shipped</div>
        <div class="od-tab" data-status="Delivered">Delivered</div>
        <div class="od-tab" data-status="Cancelled">Cancelled</div>
      </div>

      <select class="od-filter" id="statusFilter">
        <option value="Any">Status : Any</option>
        <option value="Pending">Pending</option>
        <option value="Shipped">Shipped</option>
        <option value="Delivered">Delivered</option>
        <option value="Cancelled">Cancelled</option>
      </select>

    </div>

    <div class="od-table-wrap">

      <table class="od-table">

        <thead>
          <tr>
            <th>ID</th>
            <th>CUSTOMER</th>
            <th>PAYMENT</th>
            <th>STATUS</th>
            <th>Products</th>
            <th>AMOUNT</th>
            <th>DATE</th>
            <th>ACTION</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($orders as $order)

          <tr>

            <td><strong>#{{ $order->id }}</strong></td>

            <td>
              <a href="{{ route('order.details', $order->id) }}"
                style="text-decoration:none; color:inherit;">
                <div class="od-customer">

                  <div class="od-user">{{ substr($order->user->name ?? 'N/A', 0, 2) }}</div>

                  <div>
                    <strong>{{ $order->user->name ?? 'N/A' }}</strong>
                    <p>{{ $order->user->email ?? 'N/A' }}</p>
                  </div>

                </div>
              </a>
            </td>

            <!-- PAYMENT STATUS -->
            <td>
              <div class="editable-select-wrap">

                <i class="fa-solid fa-credit-card edit-icon"></i>

                <select class="od-status-select payment-status"
                  onchange="updatePaymentStatus(this.value, {{ $order->id }})">

                  <option value="Pending" @selected($order->payment_status=='Pending')>
                    Pending
                  </option>

                  <option value="Paid" @selected($order->payment_status=='Paid')>
                    Paid
                  </option>

                  <option value="Failed" @selected($order->payment_status=='Failed')>
                    Failed
                  </option>

                </select>

              </div>
            </td>


            <!-- ORDER STATUS -->
            <td>
              <div class="editable-select-wrap">

                <i class="fa-solid fa-truck edit-icon"></i>

                <select class="od-status-select order-status"
                  onchange="updateOrderStatus(this.value, {{ $order->id }})">

                  <option value="Pending" @selected($order->status=='Pending')>Pending</option>
                  <option value="Confirmed" @selected($order->status=='Confirmed')>Confirmed</option>
                  <option value="Processing" @selected($order->status=='Processing')>Processing</option>
                  <option value="Shipped" @selected($order->status=='Shipped')>Shipped</option>
                  <option value="Delivered" @selected($order->status=='Delivered')>Delivered</option>
                  <option value="Cancelled" @selected($order->status=='Cancelled')>Cancelled</option>

                </select>

                <i class="fa-solid fa-chevron-down edit-arrow"></i>

              </div>
            </td>
            <td>

              @foreach($order->items as $item)

              <div style="margin-bottom:5px">

                {{ $item->medicine_name }}

                ×

                {{ $item->quantity }}

              </div>

              @endforeach

            </td>

            <td>
              <strong>
                ₹ {{ number_format($order->total_amount,2) }}
              </strong>

              <br>

              <small>

                {{ $order->items->count() }}

                Item(s)

              </small>
            </td>

            <td>{{ $order->created_at->format('M j, Y') }}</td>
            <td>
              <a href="{{ route('orders.invoice',$order->id) }}"
                class="od-view-btn"
                target="_blank">
                Invoice
              </a>
            </td>

            <td>
              <button class="od-delete-btn" onclick="openDeleteModal(this)">

                Delete

              </button>
            </td>

          </tr>
          @endforeach

        </tbody>

      </table>

    </div>

    <!-- PAGINATION -->

    <div class="od-pagination">

      {{ $orders->links()  }}

    </div>


    <form id="deleteForm" method="POST">
      @csrf
      @method('DELETE')

      <div class="od-modal" id="odDeleteModal">

        <div class="od-modal-box">

          <h2>Delete Order</h2>

          <div class="od-delete-details">

            <p><strong>ID:</strong> <span id="deleteOrderId"></span></p>

            <p><strong>Customer:</strong> <span id="deleteCustomer"></span></p>

            <p><strong>Amount:</strong> <span id="deleteAmount"></span></p>

          </div>

          <p style="margin-bottom:15px; color:#666;">
            Are you sure you want to delete this order?
          </p>

          <div class="od-modal-actions">

            <button type="button" class="od-cancel-btn" onclick="closeDeleteModal()">
              Cancel
            </button>

            <button type="submit" class="od-confirm-delete">
              Yes Delete
            </button>

          </div>

        </div>

      </div>
    </form>

  </div>

  <script>
    const odToggle = document.getElementById('odToggle');
    const odSidebar = document.getElementById('odSidebar');
    const odMain = document.getElementById('odMain');

    odToggle.addEventListener('click', () => {

      // MOBILE
      if (window.innerWidth <= 768) {

        odSidebar.classList.toggle('od-show');

      }

      // DESKTOP
      else {

        odSidebar.classList.toggle('od-hide');
        odMain.classList.toggle('od-expand');

      }

    });


    // CLICK OUTSIDE CLOSE MOBILE SIDEBAR

    document.addEventListener('click', function(e) {

      if (
        window.innerWidth <= 768 &&
        !odSidebar.contains(e.target) &&
        !odToggle.contains(e.target)
      ) {
        odSidebar.classList.remove('od-show');
      }

    });
  </script>

  <script>
    function updateOrderStatus(status, orderId) {

      fetch('/update-order-status', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            id: orderId,
            status: status
          })
        })
        .then(response => response.json())
        .then(data => {
          location.reload();
        });

    }

    function updatePaymentStatus(payment_status, orderId) {

      fetch('/update-payment-status', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            id: orderId,
            payment_status: payment_status
          })
        })
        .then(response => response.json())
        .then(data => {
          location.reload();
        });

    }
  </script>

  <script>
    let selectedOrderId = null;

    function openDeleteModal(button) {

      const row = button.closest('tr');

      selectedOrderId = row.children[0].innerText.replace('#', '');

      const customer = row.querySelector('.od-customer strong').innerText;
      const amount = row.children[5].innerText;

      document.getElementById('deleteOrderId').innerText = selectedOrderId;
      document.getElementById('deleteCustomer').innerText = customer;
      document.getElementById('deleteAmount').innerText = amount;

      // IMPORTANT: set form action dynamically
      document.getElementById('deleteForm').action =
        "/orders/" + selectedOrderId;

      document.getElementById('odDeleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
      document.getElementById('odDeleteModal').style.display = 'none';
    }
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {

      const tabs = document.querySelectorAll(".od-tab");
      const filterSelect = document.getElementById("statusFilter");
      const rows = document.querySelectorAll(".od-table tbody tr");

      // TAB FILTER
      tabs.forEach(tab => {

        tab.addEventListener("click", function() {

          tabs.forEach(t => t.classList.remove("od-active"));
          this.classList.add("od-active");

          let status = this.dataset.status;

          filterRows(status);
        });

      });

      // SELECT FILTER
      filterSelect.addEventListener("change", function() {

        let value = this.value;

        if (value === "Any") {
          filterRows("All");
        } else {
          filterRows(value);
        }

      });

      // MAIN FILTER FUNCTION
      function filterRows(status) {

        rows.forEach(row => {

          let orderStatus = row.querySelectorAll(".od-status-select")[1]
            .value
            .trim();

          if (status === "All") {

            row.style.display = "";

          } else if (orderStatus === status) {

            row.style.display = "";

          } else {

            row.style.display = "none";

          }

        });

      }

    });
  </script>
  <script src="{{ asset('js/main.js') }}"></script>

  @endsection