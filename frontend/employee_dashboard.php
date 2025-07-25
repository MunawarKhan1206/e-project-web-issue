<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employee') {
    echo "<script>
        alert('Access Denied. Employee login required.');
        window.location.href = '../form/index.php';
    </script>";
    exit;
}
require_once("../includes/db.inc.php");

// Fetch assigned orders (example logic, customize per your DB)
$stmt = $pdo->prepare("SELECT o.order_id, o.order_number, o.status, o.updated_at, u.email 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.assigned_to = ?");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Example counts
$total = count($orders);
$completed = count(array_filter($orders2, fn($o) => $o['status'] === 'Delivered'));
$pending = count(array_filter($orders2, fn($o) => $o['status'] !== 'Delivered'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .sidebar {
      height: 100vh;
      background: linear-gradient(135deg, #4e54c8, #8f94fb);
      padding-top: 30px;
      color: #fff;
    }
    .sidebar a {
      color: #fff;
      padding: 10px 20px;
      display: block;
      transition: background 0.3s ease;
    }
    .sidebar a:hover {
      background: rgba(255, 255, 255, 0.1);
      text-decoration: none;
    }
    .dashboard-content {
      padding: 30px;
    }
    .stat-card {
      background: #fff;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      transition: 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    .stat-title {
      font-size: 16px;
      font-weight: 500;
      color: #555;
    }
    .stat-value {
      font-size: 30px;
      font-weight: 700;
      color: #333;
    }
    .table th, .table td {
      vertical-align: middle;
    }
    .status-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 13px;
    }
    .Delivered {
      background: #28a745;
      color: white;
    }
    .Pending, .Processing {
      background: #ffc107;
      color: black;
    }
    .Cancelled {
      background: #dc3545;
      color: white;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar">
      <h4 class="text-center mb-4">🚚 Employee Panel</h4>
      <a href="employee_dashboard.php">📊 Dashboard</a>
      <a href="assigned_orders.php">📦 Assigned Orders</a>
      <a href="update_status.php">🛠 Update Status</a>
      <a href="../form/logout.php">🚪 Logout</a>
    </div>

    <!-- Main Dashboard Content -->
    <div class="col-md-9 dashboard-content">
      <h3 class="mb-4">Welcome, <?php echo $_SESSION['user_name']; ?> 👋</h3>

      <!-- Stats Row -->
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="stat-card text-center">
            <div class="stat-title">Assigned Orders</div>
            <div class="stat-value"><?php echo $total; ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card text-center">
            <div class="stat-title">Completed Orders</div>
            <div class="stat-value"><?php echo $completed; ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card text-center">
            <div class="stat-title">Pending Deliveries</div>
            <div class="stat-value"><?php echo $pending; ?></div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="card shadow-sm">
        <div class="card-header bg-white">
          <h5 class="mb-0">📋 Assigned Orders</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead class="thead-dark">
                <tr>
                  <th>#Order</th>
                  <th>Customer Email</th>
                  <th>Status</th>
                  <th>Last Updated</th>
                </tr>
              </thead>
              <tbody>
              <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                  <tr>
                    <td>#<?php echo $order['order_number']; ?></td>
                    <td><?php echo $order['email']; ?></td>
                    <td><span class="status-badge <?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                    <td><?php echo date("d M Y, h:i A", strtotime($order['updated_at'])); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">No assigned orders found.</td>
                </tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</body>
</html>
