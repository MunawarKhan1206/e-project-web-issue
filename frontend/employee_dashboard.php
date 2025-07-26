<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employee') {
    header("Location: ../form/index.php");
    exit;
}
require_once("../includes/db.inc.php");

$stmt = $pdo->query("SELECT id, order_number, customer_name, grand_total, status, created_at FROM orders2 ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_orders = count($orders);
$delivered_orders = count(array_filter($orders, fn($o) => strtolower($o['status']) === 'delivered'));
$processed_orders = count(array_filter($orders, fn($o) => strtolower($o['status']) === 'processed'));
$shipped_orders = count(array_filter($orders, fn($o) => strtolower($o['status']) === 'shipped'));
$pending_orders = count(array_filter($orders, fn($o) => strtolower($o['status']) === 'pending'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .toast-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1055;
    }
    .sidebar-collapse {
      display: none;
    }
    @media (max-width: 768px) {
      .sidebar-collapse {
        display: block;
        margin-bottom: 1rem;
      }
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
      <div class="position-sticky pt-3">
        <h4 class="text-white px-3">Employee Panel</h4>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link text-white active" href="employee_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white active" href="stock.php"><i class="fas fa-boxes me-2"></i>Stock</a></li>
             <li class="nav-item"><a class="nav-link text-white active" href="employee_return.php"><i class="fas fa-undo me-2"></i>Returns</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
      </div>
    </nav>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <button class="btn btn-outline-dark d-md-none sidebar-collapse" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
      </button>

      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2>Welcome, <?= $_SESSION['user_name'] ?? 'Employee' ?> 👋</h2>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6"><div class="card text-bg-light"><div class="card-body"><h6>Total Orders</h6><h3><?= $total_orders ?></h3></div></div></div>
        <div class="col-md-3 col-sm-6"><div class="card text-bg-success"><div class="card-body"><h6>Delivered</h6><h3><?= $delivered_orders ?></h3></div></div></div>
        <div class="col-md-3 col-sm-6"><div class="card text-bg-warning"><div class="card-body"><h6>Processed</h6><h3><?= $processed_orders ?></h3></div></div></div>
        <div class="col-md-3 col-sm-6"><div class="card text-bg-info"><div class="card-body"><h6>Shipped</h6><h3><?= $shipped_orders ?></h3></div></div></div>
        <div class="col-md-3 col-sm-6"><div class="card text-bg-secondary"><div class="card-body"><h6>Pending</h6><h3><?= $pending_orders ?></h3></div></div></div>
      </div>

      <h4 class="mt-4">All Orders</h4>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-dark">
            <tr><th>#Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Update</th><th>Date</th></tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td>#<?= $order['order_number'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td>Rs. <?= number_format($order['grand_total'], 2) ?></td>
                <td><span class="badge bg-<?= strtolower($order['status']) === 'delivered' ? 'success' : 'warning' ?>"><?= ucfirst($order['status']) ?></span></td>
                <td>
                  <select class="form-select status-select" data-id="<?= $order['id'] ?>">
                    <option <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option <?= $order['status'] == 'Processed' ? 'selected' : '' ?>>Processed</option>
                    <option <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                  </select>
                </td>
                <td><?= date("M d, Y", strtotime($order['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<div class="toast-container">
  <div id="toast" class="toast align-items-center text-white bg-primary border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.status-select').forEach(select => {
  select.addEventListener('change', function () {
    const orderId = this.dataset.id;
    const newStatus = this.value;
    fetch('update_status_ajax.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `order_id=${orderId}&status=${newStatus}`
    }).then(res => res.text()).then(data => {
      const toastEl = document.getElementById('toast');
      const toastBody = toastEl.querySelector('.toast-body');
      toastBody.textContent = (data.trim() === 'success') ? `Status updated to ${newStatus}` : 'Failed to update';
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
    }).catch(() => {
      const toastEl = document.getElementById('toast');
      toastEl.querySelector('.toast-body').textContent = 'Server error!';
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
    });
  });
});
</script>
</body>
</html>
