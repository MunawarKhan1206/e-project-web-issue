<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}

require_once '../includes/db.inc.php';

// Filters
$status = $_GET['status'] ?? '';
$payment = $_GET['payment'] ?? '';
$email = $_GET['email'] ?? '';
$date = $_GET['date'] ?? '';

// Base query
$sql = "SELECT * FROM orders2 WHERE 1=1";
$params = [];

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}
if (!empty($payment)) {
    $sql .= " AND payment_type = ?";
    $params[] = $payment;
}
if (!empty($email)) {
    $sql .= " AND email LIKE ?";
    $params[] = "%$email%";
}
if (!empty($date)) {
    $sql .= " AND DATE(created_at) = ?";
    $params[] = $date;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - All Orders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="products-style.css" rel="stylesheet">
  <style>
    /* Hide card list on desktop */
.orders-card-list {
  display: none;
}
.badge.fs-6 {
  font-size: 0.95rem !important;
}

/* Show card list and hide table on mobile */
@media (max-width: 768px) {
  .table-responsive table {
    display: none !important;
  }
  .orders-card-list {
    display: block;
  }
  .order-card {
    font-size: 0.9rem;
    border-radius: 10px;
    /* Optional: box-shadow or border for cards */
    box-shadow: 0 5px 10px rgba(0,0,0,0.05);
  }
  .order-card > div {
    margin-bottom: 8px;
  }
  .order-card select,
  .order-card button {
    width: 100%;
  }
}

  </style>
</head>
<body>
<div class="dashboard">
  <!-- Sidebar -->
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
            <a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="returns.php"><i class="fas fa-undo-alt"></i> Returns</a>
            <a href="employees.php"><i class="fas fa-user-tie"></i> Employees</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            <a href="notification.php" ><i class="fas fa-bell"></i> Notifications</a>
            <a href="../frontend/index.php"><i class="fas fa-store"></i> Go to Shop</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

  <!-- Main -->
  <div class="main">
    <button id="menu-toggle" class="menu-toggle"><i class="fas fa-bars"></i></button>
    <div class="dashboard-title d-flex justify-content-between align-items-center">
      <span>All Orders</span>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-4 row">
      <div class="col-md-3 mb-2">
        <input type="text" name="email" class="form-control" placeholder="Search by email" value="<?= htmlspecialchars($email) ?>">
      </div>
      <div class="col-md-2 mb-2">
        <select name="status" class="form-control">
          <option value="">All Status</option>
          <option <?= $status === 'pending' ? 'selected' : '' ?>>pending</option>
          <option <?= $status === 'processed' ? 'selected' : '' ?>>processed</option>
          <option <?= $status === 'shipped' ? 'selected' : '' ?>>shipped</option>
          <option <?= $status === 'delivered' ? 'selected' : '' ?>>delivered</option>
        </select>
      </div>
      <div class="col-md-2 mb-2">
        <select name="payment" class="form-control">
          <option value="">All Payments</option>
          <option <?= $payment === 'credit' ? 'selected' : '' ?>>credit</option>
          <option <?= $payment === 'cheque' ? 'selected' : '' ?>>cheque</option>
          <option <?= $payment === 'vpp' ? 'selected' : '' ?>>vpp</option>
        </select>
      </div>
      <div class="col-md-2 mb-2">
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>">
      </div>
      <div class="col-md-3 mb-2">
        <button class="btn btn-dark w-100"><i class="fas fa-filter"></i> Filter</button>
      </div>
    </form>

    <!-- Orders Table -->
   <!-- Orders Table for desktop -->
<!-- Orders Table -->
<div class="bg-white p-2 shadow-sm rounded" style="overflow-x: auto;">
  <div class="table-responsive">
   <table class="table table-bordered table-hover">
  <thead style="background: #f2ebe0; color: #5c4434;">
    <tr>
      <th>Email</th>
      <th>Payment</th>
      <th>Status</th>
      <th>Total (Rs)</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($orders) > 0): ?>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td><?= htmlspecialchars($o['email']) ?></td>
          <td><?= ucfirst($o['payment_method']) ?></td>
          <td>
            <span class="badge px-3 py-2 fs-6
              <?= $o['status'] === 'pending' ? 'bg-warning text-dark' : '' ?>
              <?= $o['status'] === 'processed' ? 'bg-primary' : '' ?>
              <?= $o['status'] === 'shipped' ? 'bg-info text-dark' : '' ?>
              <?= $o['status'] === 'delivered' ? 'bg-success' : '' ?>">
              <?= ucfirst($o['status']) ?>
            </span>
          </td>
          <td>Rs. <?= number_format($o['grand_total'], 2) ?></td>
          <td><?= date("d M Y", strtotime($o['created_at'])) ?></td>
          <td>
            <div class="d-flex flex-column gap-1">
              <select class="form-select form-select-sm status-select" data-id="<?= $o['id'] ?>">
                <option value="pending" <?= $o['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="processed" <?= $o['status'] === 'processed' ? 'selected' : '' ?>>Processed</option>
                <option value="shipped" <?= $o['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="delivered" <?= $o['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
              </select>
              <button class="btn btn-sm btn-primary update-status-btn" data-id="<?= $o['id'] ?>">Update</button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="6" class="text-center text-muted">No orders found</td></tr>
    <?php endif; ?>
  </tbody>
</table>

  </div>
</div>

<!-- Card view for mobile, same data -->
<div class="orders-card-list">
  <?php if (count($orders) > 0): ?>
    <?php foreach ($orders as $o): ?>
      <div class="order-card p-3 mb-3 bg-white shadow-sm rounded">
        <div><strong>Email:</strong> <?= htmlspecialchars($o['email']) ?></div>
        <div><strong>Payment:</strong> <?= ucfirst($o['payment_method']) ?></div>
        <div>
          <strong>Status:</strong>
          <span class="badge badge-<?= $o['status'] === 'pending' ? 'warning' : ($o['status'] === 'delivered' ? 'success' : 'info') ?>">
            <?= ucfirst($o['status']) ?>
          </span>
        </div>
        <div><strong>Total:</strong> Rs. <?= number_format($o['grand_total'], 2) ?></div>
        <div><strong>Date:</strong> <?= date("d M Y", strtotime($o['created_at'])) ?></div>
        <div class="mt-2">
          <select class="form-control status-select" data-id="<?= $o['id'] ?>">
            <option value="Pending" <?= $o['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Processed" <?= $o['status'] === 'Processed' ? 'selected' : '' ?>>Processed</option>
            <option value="Shipped" <?= $o['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="Delivered" <?= $o['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
          </select>
          <button class="btn btn-sm btn-primary mt-2 update-status-btn w-100" data-id="<?= $o['id'] ?>">Update</button>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-center text-muted">No orders found</p>
  <?php endif; ?>
</div>

</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
  $('.update-status-btn').click(function() {
    const button = $(this);
    const orderId = button.data('id');
    const status = button.siblings('.status-select').val();

    $.ajax({
      url: 'update_order_status.php',
      method: 'POST',
      data: {
        order_id: orderId,
        status: status
      },
      success: function(response) {
        if (response.trim() === 'success') {
          button.text('Updated ✅').removeClass('btn-primary').addClass('btn-success');
          setTimeout(() => {
            button.text('Update').removeClass('btn-success').addClass('btn-primary');
          }, 2000);
        }
      },
      error: function(xhr) {
        alert("❌ Failed to update: " + xhr.responseText);
      }
    });
  });
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
<!-- <script>
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
</script> -->
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
<!-- Scripts -->
</body>
</html>
