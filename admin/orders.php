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
</head>
<body>
<div class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar" id="adminSidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="returns.php"><i class="fas fa-undo"></i> Returns</a>
    <a href="employees.php"><i class="fas fa-user-tie"></i> Employees</a>
    <a href="../frontend/index.php"><i class="fas fa-store"></i> Shop</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
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
    <div class="bg-white p-3 shadow-sm rounded" style="overflow-x: auto;">
      <div class="table-responsive">

        <table class="table table-bordered table-hover" >
          <thead class="thead-light">
            <tr>
              <!-- <th>Order ID</th> -->
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
                  <span class="badge badge-<?= $o['status'] === 'pending' ? 'warning' : ($o['status'] === 'delivered' ? 'success' : 'info') ?>">
                    <?= ucfirst($o['status']) ?>
                  </span>
                </td>
                <td>Rs. <?= number_format($o['grand_total'], 2) ?></td>
                <td><?= date("d M Y", strtotime($o['created_at'])) ?></td>
                <td>
                  <select class="form-control status-select" data-id="<?= $o['id'] ?>">
                    <option value="Pending" <?= $o['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Processed" <?= $o['status'] === 'Processed' ? 'selected' : '' ?>>Processed</option>
                    <option value="Shipped" <?= $o['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="Delivered" <?= $o['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                  </select>
                  <button class="btn btn-sm btn-primary mt-2 update-status-btn" data-id="<?= $o['id'] ?>">Update</button>
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

<!-- Scripts -->
</body>
</html>
