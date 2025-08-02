<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../form/index.php");
  exit;
}
require_once '../includes/db.inc.php';

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT * FROM returns";
$params = [];

if (!empty($statusFilter)) {
  $sql .= " WHERE status = ?";
  $params[] = $statusFilter;
}

$sql .= " ORDER BY return_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin - Returns</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="products-style.css" />
</head>

<body>
  <div class="dashboard">
    <div class="sidebar">
            <h2 style="    display: block;
    font-size: 1.5em !important;
    /* margin-block-start: 0.83em; */
    /* margin-block-end: 0.83em; */
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    font-weight: bold;
    unicode-bidi: isolate;
    height: 28px !important;">Admin Panel</h2>
            <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="products.php"><i class="fas fa-box"></i> Products</a>
            <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="returns.php" class="active"><i class="fas fa-undo-alt"></i> Returns</a>
            <a href="employees.php"><i class="fas fa-user-tie"></i> Employees</a>
            <a href="customers.php"><i class="fas fa-user-tie"></i> Customers</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            <a href="notification.php"><i class="fas fa-bell"></i> Notifications</a>
            <a href="../frontend/index.php"><i class="fas fa-store"></i> Go to Shop</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

    <div class="main">
      <button id="menu-toggle" class="menu-toggle"><i class="fas fa-bars"></i></button>
      <div class="dashboard-title d-flex justify-content-between align-items-center">
        <span>Return Requests</span>
      </div>

      <form method="GET" class="row mb-4">
        <div class="col-md-4">
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option <?= $statusFilter === 'pending' ? 'selected' : '' ?>>pending</option>
            <option <?= $statusFilter === 'approved' ? 'selected' : '' ?>>approved</option>
            <option <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>rejected</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-dark w-100"><i class="fas fa-filter"></i> Filter</button>
        </div>
      </form>

      <div class="bg-white p-4 shadow-sm rounded" style="overflow-x:auto;">
        <div class="table-responsive d-none d-md-block">
  <table class="table table-bordered table-hover">
    <thead  style="background: #f2ebe0; color: #5c4434;">
      <tr>
        <th>#</th>
        <th>Order #</th>
        <th>Return Reason</th>
        <th>Return Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($returns) > 0): ?>
        <?php foreach ($returns as $r): ?>
          <tr id="return-row-<?= $r['id'] ?>">
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['order_number']) ?></td>
            <td>
              <button class="btn btn-sm btn-info view-details-btn"
                data-id="<?= $r['id'] ?>"
                data-order="<?= $r['order_number'] ?>"
                data-reason="<?= htmlspecialchars($r['reason']) ?>"
                data-status="<?= $r['status'] ?>"
                data-date="<?= $r['return_date'] ?>"
                data-customer="<?= $orderRow['customer_name'] ?>"
                data-email="<?= $orderRow['email'] ?>"
                data-phone="<?= $orderRow['phone'] ?>"
                data-address="<?= $orderRow['address'] ?>"
                data-payment="<?= $orderRow['payment_method'] ?>"
                data-subtotal="<?= $orderRow['subtotal'] ?>"
                data-delivery="<?= $orderRow['delivery'] ?>"
                data-grandtotal="<?= $orderRow['grand_total'] ?>"
                data-toggle="modal"
                data-target="#viewDetailsModal">
                View Details
              </button>
            </td>
            <td><?= date("d M Y", strtotime($r['return_date'])) ?></td>
            <td class="return-status">
              <span class="badge badge-<?= $r['status'] === 'approved' ? 'success' : ($r['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                <?= ucfirst($r['status']) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted">No return requests found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Card Layout for Mobile -->
<div class="d-md-none">
  <?php if (count($returns) > 0): ?>
    <?php foreach ($returns as $r): ?>
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Order #<?= htmlspecialchars($r['order_number']) ?></h5>
          <p><strong>ID:</strong> <?= $r['id'] ?></p>
          <p><strong>Return Date:</strong> <?= date("d M Y", strtotime($r['return_date'])) ?></p>
          <p><strong>Status:</strong>
            <span class="badge badge-<?= $r['status'] === 'approved' ? 'success' : ($r['status'] === 'rejected' ? 'danger' : 'warning') ?>">
              <?= ucfirst($r['status']) ?>
            </span>
          </p>
          <button class="btn btn-sm btn-info view-details-btn"
            data-id="<?= $r['id'] ?>"
            data-order="<?= $r['order_number'] ?>"
            data-reason="<?= htmlspecialchars($r['reason']) ?>"
            data-status="<?= $r['status'] ?>"
            data-date="<?= $r['return_date'] ?>"
            data-customer="<?= $orderRow['customer_name'] ?>"
            data-email="<?= $orderRow['email'] ?>"
            data-phone="<?= $orderRow['phone'] ?>"
            data-address="<?= $orderRow['address'] ?>"
            data-payment="<?= $orderRow['payment_method'] ?>"
            data-subtotal="<?= $orderRow['subtotal'] ?>"
            data-delivery="<?= $orderRow['delivery'] ?>"
            data-grandtotal="<?= $orderRow['grand_total'] ?>"
            data-toggle="modal"
            data-target="#viewDetailsModal">
            View Details
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-muted text-center">No return requests found.</p>
  <?php endif; ?>
</div>

      </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title">Return & Order Details</h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <p><strong>Order #:</strong> <span id="detailOrder"></span></p>
            <p><strong>Status:</strong> <span id="detailStatus" class="badge badge-secondary"></span></p>
            <p><strong>Return Date:</strong> <span id="detailDate"></span></p>
            <p><strong>Days Left:</strong> <span id="detailDaysLeft" class="text-danger"></span></p>
            <hr>
            <p><strong>Reason:</strong></p>
            <p id="detailReason" style="white-space: pre-wrap;"></p>
            <hr>
            <p><strong>Order Items:</strong></p>
            <table class="table table-sm table-bordered">
              <thead>
                <!-- <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
              </tr> -->
              </thead>
              <tbody id="detailItemsBody"></tbody>
            </table>
            <div class="d-flex justify-content-end mt-3" id="statusBtns">
              <button class="btn btn-success btn-sm mr-2" id="modalApproveBtn">Approve</button>
              <button class="btn btn-danger btn-sm" id="modalRejectBtn">Reject</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      $(document).ready(function() {
        $('.view-details-btn').click(function() {
          const btn = $(this);
          const order = btn.data('order');
          const reason = btn.data('reason');
          const status = btn.data('status');
          const date = btn.data('date');
          const id = btn.data('id');

          const customer = btn.data('customer');
          const email = btn.data('email');
          const phone = btn.data('phone');
          const address = btn.data('address');
          const payment = btn.data('payment');
          const subtotal = btn.data('subtotal');
          const delivery = btn.data('delivery');
          const grandtotal = btn.data('grandtotal');

          // Populate header data
          $('#detailOrder').text(order);
          $('#detailReason').text(reason);
          $('#detailDate').text(new Date(date).toDateString());

          $('#detailStatus')
            .removeClass()
            .addClass('badge badge-' + (status === 'approved' ? 'success' : status === 'rejected' ? 'danger' : 'warning'))
            .text(status.charAt(0).toUpperCase() + status.slice(1));

          // Days left
          const daysPassed = Math.floor((new Date() - new Date(date)) / (1000 * 60 * 60 * 24));
          const daysLeft = Math.max(0, 7 - daysPassed);
          $('#detailDaysLeft').text(daysLeft > 0 ? `${daysLeft} day(s)` : 'Expired');

          // Action buttons
          $('#modalApproveBtn').data('id', id);
          $('#modalRejectBtn').data('id', id);
          $('#statusBtns').toggle(status === 'pending');

          // Order Summary (Single row data)
          const rows = `
        <tr><td><strong>Customer</strong></td><td>${customer}</td></tr>
        <tr><td><strong>Email</strong></td><td>${email}</td></tr>
        <tr><td><strong>Phone</strong></td><td>${phone}</td></tr>
        <tr><td><strong>Address</strong></td><td>${address}</td></tr>
        <tr><td><strong>Payment</strong></td><td>${payment}</td></tr>
        <tr><td><strong>Subtotal</strong></td><td>Rs. ${subtotal}</td></tr>
        <tr><td><strong>Delivery</strong></td><td>Rs. ${delivery}</td></tr>
        <tr><td><strong><b>Grand Total</b></strong></td><td><strong>Rs. ${grandtotal}</strong></td></tr>
      `;
          $('#detailItemsBody').html(rows);

          $('#viewDetailsModal').modal('show');
        });

        $('#modalApproveBtn, #modalRejectBtn').click(function() {
          const id = $(this).data('id');
          const action = this.id.includes('Approve') ? 'approve' : 'reject';

          $.post('update_return_status.php', {
            id: id,
            action: action
          }, function() {
            const row = $('#return-row-' + id);
            row.find('.return-status').html(`<span class="badge badge-${action === 'approve' ? 'success' : 'danger'}">${action}d</span>`);
            row.find('.return-action-cell').html('<i class="text-muted">—</i>');
            $('#viewDetailsModal').modal('hide');
          });
        });
      });
    </script>
    <script src="menu.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
  // GSAP Animations
  gsap.from(".sidebar", { x: -200, duration: 0.8, ease: "power2.out" });
  gsap.from(".main", { opacity: 0, y: 30, duration: 0.6, delay: 0.2 });
  gsap.from(".btn-add, .dashboard-title, .section", {
    y: 50,
    opacity: 0,
    stagger: 0.2,
    duration: 0.8,
    delay: 0.5,
    ease: "back.out(1.7)",
  });
</script>
   <script>
        const toggle = document.getElementById('menu-toggle');
        const sidebar = document.querySelector('.sidebar');

        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar if clicked outside on small screens
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
                const isClickInsideSidebar = sidebar.contains(e.target);
                const isClickOnToggle = toggle.contains(e.target);

                if (!isClickInsideSidebar && !isClickOnToggle) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
</body>

</html>