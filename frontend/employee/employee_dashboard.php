<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employee') {
  header("Location: ../form/index.php");
  exit;
}
require_once("../../includes/db.inc.php");


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
  <title>Employee Dashboard</title>
  <link rel="stylesheet" href="customer.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1055;
    }
  </style>

  </style>
</head>

<body>
  <div class="dashboard">

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <h2>Employee Panel</h2>
      <a href="employee_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
      <a href="stock.php"><i class="fas fa-boxes"></i> Stock</a>
      <a href="employee_return.php"><i class="fas fa-undo"></i> Returns</a>
      <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main">
      <button class="menu-toggle btn btn-sm btn-warning d-md-none" onclick="document.getElementById('sidebar').classList.toggle('active')">
        <i class="fas fa-bars"></i>
      </button>

      <h2 class="dashboard-title">Welcome, <?= $_SESSION['user_name'] ?? 'Employee' ?> 👋</h2>

      <!-- Stats -->
      <div class="stats mb-4">
        <div class="stat-card">
          <div class="stat-title">Total Orders</div>
          <div class="stat-value"><?= $total_orders ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-title">Delivered</div>
          <div class="stat-value"><?= $delivered_orders ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-title">Processed</div>
          <div class="stat-value"><?= $processed_orders ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-title">Shipped</div>
          <div class="stat-value"><?= $shipped_orders ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-title">Pending</div>
          <div class="stat-value"><?= $pending_orders ?></div>
        </div>
      </div>
<?php
function getStatusColorClass($status) {
  switch (strtolower($status)) {
    case 'pending': return 'secondary';
    case 'processed': return 'info';
    case 'shipped': return 'primary';
    case 'delivered': return 'success';
    default: return 'dark';
  }
}
?>

      <!-- Orders Section -->
      <div class="section">
        <h3>All Orders</h3>

        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
          <table class="table custom-table table-striped align-middle">
            <thead class="custom-thead">
              <tr>
                <th>#Order</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Update</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td>#<?= $order['order_number'] ?></td>
                  <td><?= htmlspecialchars($order['customer_name']) ?></td>
                  <td>Rs. <?= number_format($order['grand_total'], 2) ?></td>
                  <td><span class="badge 
  <?= $order['status'] === 'Pending' ? 'bg-warning text-dark' : '' ?>
  <?= $order['status'] === 'Processed' ? 'bg-primary' : '' ?>
  <?= $order['status'] === 'Shipped' ? 'bg-info text-dark' : '' ?>
  <?= $order['status'] === 'Delivered' ? 'bg-success' : '' ?>">
  <?= ucfirst($order['status']) ?>
</span>
</td>
                  <td>
                    <select class="form-select form-select-sm status-select" data-id="<?= $order['id'] ?>">
                      <?php foreach (['Pending', 'Processed', 'Shipped', 'Delivered'] as $status): ?>
                        <option <?= $order['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td><?= date("M d, Y", strtotime($order['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
      <div class="d-md-none">
  <?php foreach ($orders as $order): ?>
    <div class="order-card mb-3 p-3 border rounded bg-light">
      <div class="d-flex justify-content-between mb-2">
        <strong>#<?= $order['order_number'] ?></strong>
        <!-- Colored status badge -->
        <span class="badge badge-<?= strtolower($order['status']) ?>">
          <?= ucfirst($order['status']) ?>
        </span>
      </div>

      <p class="mb-1"><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
      <p class="mb-1"><strong>Total:</strong> Rs. <?= number_format($order['grand_total'], 2) ?></p>
      <p class="mb-1"><strong>Date:</strong> <?= date("M d, Y", strtotime($order['created_at'])) ?></p>

      <!-- Status update dropdown -->
      <div class="mb-2">
        <strong>Update Status:</strong><br>
        <select class="form-control form-control-sm mt-1 status-dropdown" 
                data-order-id="<?= $order['order_number'] ?>">
          <?php foreach (['Pending', 'Processed', 'Shipped', 'Delivered'] as $status): ?>
            <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
              <?= $status ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  <?php endforeach; ?>
</div>



      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast-container">
    <div id="toast" class="toast align-items-center text-white bg-primary border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
  gsap.from(".sidebar", {x: -200, duration: 0.8, ease: "power2.out"});
  gsap.from(".main", {opacity: 0, y: 30, duration: 0.6, delay: 0.2});
  gsap.from("table tbody tr", {
    opacity: 0,
    y: 20,
    duration: 0.5,
    stagger: 0.1,
    delay: 0.4
  });
</script>
  <script>
    document.querySelectorAll('.status-select').forEach(select => {
      select.addEventListener('change', function() {
        const orderId = this.dataset.id;
        const newStatus = this.value;
        fetch('update_status_ajax.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `order_id=${orderId}&status=${newStatus}`
          })
          .then(res => res.text())
          .then(data => {
            const toastEl = document.getElementById('toast');
            const toastBody = toastEl.querySelector('.toast-body');
            toastBody.textContent = (data.trim() === 'success') ? `Status updated to ${newStatus}` : 'Failed to update';
            new bootstrap.Toast(toastEl).show();
          })
          .catch(() => {
            const toastEl = document.getElementById('toast');
            toastEl.querySelector('.toast-body').textContent = 'Server error!';
            new bootstrap.Toast(toastEl).show();
          });
      });
    });
    $('#Toast').toast({ delay: 3000 }).toast('show');

  </script>
  <script>
  document.addEventListener("click", function (e) {
    const sidebar = document.querySelector(".sidebar");
    const toggle = document.querySelector(".menu-toggle");
    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
      sidebar.classList.remove("active");
    }
  });
</script>
</body>

</html>