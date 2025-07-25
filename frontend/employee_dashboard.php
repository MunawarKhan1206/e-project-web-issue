<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employee') {
    header("Location: ../form/index.php");
    exit;
}
require_once("../includes/db.inc.php");

// Fetch all orders (you can filter if needed)
$stmt = $pdo->query("SELECT id, order_number, customer_name, grand_total, status, created_at FROM orders2 ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$total_orders = count($orders);
$delivered_orders = count(array_filter($orders, fn($o) => strtolower($o['status']) === 'delivered'));
$pending_orders = $total_orders - $delivered_orders;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0; padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }
    body {
      background: #f7f6f3;
      color: #333;
    }
    .dashboard {
      display: flex;
    }
    .sidebar {
      width: 240px;
      background: #312b26;
      min-height: 100vh;
      padding: 40px 20px;
      position: fixed;
      top: 0; left: 0;
      transition: transform 0.3s ease;
      z-index: 999;
    }
    .sidebar.hidden {
      transform: translateX(-100%);
    }
    .sidebar h2 {
      color: #fff;
      text-align: center;
      margin-bottom: 40px;
      font-size: 22px;
    }
    .sidebar a {
      display: block;
      padding: 12px 20px;
      margin: 10px 0;
      color: #eee;
      border-radius: 10px;
      text-decoration: none;
      transition: 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #c29958;
      color: #fff;
    }

    .main {
      margin-left: 240px;
      width: calc(100% - 240px);
      padding: 40px 30px;
      transition: margin-left 0.3s ease;
    }

    .dashboard-title {
      font-size: 26px;
      font-weight: 700;
      margin-bottom: 30px;
      color: #3a2f27;
    }

    .toggle-btn {
      display: none;
      font-size: 22px;
      margin-bottom: 20px;
      cursor: pointer;
    }

    .stats {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .stat-card {
      background: #fff;
      border-left: 6px solid #c29958;
      flex: 1;
      min-width: 200px;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
      transition: transform 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-title {
      font-size: 14px;
      color: #777;
    }

    .stat-value {
      font-size: 28px;
      font-weight: 600;
      color: #c29958;
    }

    .section {
      margin-top: 50px;
    }

    table {
      width: 100%;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 14px 20px;
      text-align: left;
    }

    th {
      background: #f2ebe0;
      color: #5c4434;
    }

    tr:nth-child(even) {
      background: #fdfdfd;
    }

    tr:hover {
      background: #f7f1e6;
    }

    select {
      padding: 6px 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: absolute;
        height: 100%;
        z-index: 99;
      }
      .main {
        margin-left: 0;
        width: 100%;
        padding: 20px;
      }
      .toggle-btn {
        display: inline-block;
      }
    }
  </style>
</head>
<body>
<div class="dashboard">
  <div class="sidebar" id="sidebar">
    <h2>Employee Panel</h2>
    <a href="employee_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="main">
    <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>

    <div class="dashboard-title">Welcome, <?= $_SESSION['user_name'] ?? 'Employee' ?> 👋</div>

    <div class="stats">
      <div class="stat-card">
        <div class="stat-title">Total Orders</div>
        <div class="stat-value"><?= $total_orders ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-title">Delivered</div>
        <div class="stat-value"><?= $delivered_orders ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-title">Pending</div>
        <div class="stat-value"><?= $pending_orders ?></div>
      </div>
    </div>

    <div class="section">
      <h3>All Orders</h3>
      <table>
        <thead>
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
            <td>
              <span class="badge <?= strtolower($order['status']) === 'delivered' ? 'success' : 'pending' ?>">
                <?= ucfirst($order['status']) ?>
              </span>
            </td>
            <td>
              <select class="status-select" data-id="<?= $order['id'] ?>">
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
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
  gsap.from(".sidebar", {x: -200, duration: 0.8});
  gsap.from(".main", {opacity: 0, y: 30, duration: 0.6});
  gsap.from(".stat-card", {
    y: 50, opacity: 0, stagger: 0.2, duration: 0.8, ease: "back.out(1.7)"
  });

  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('hidden');
  }

  // AJAX for status update
  document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function () {
      const orderId = this.dataset.id;
      const newStatus = this.value;

      fetch('update_status_ajax.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `order_id=${orderId}&status=${newStatus}`
      }).then(res => res.text()).then(data => {
        alert('Status updated to ' + newStatus);
      });
    });
  });
</script>
</body>
</html>
