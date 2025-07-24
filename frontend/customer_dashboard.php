<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../form/index.php");
    exit;
}

require_once("../includes/db.inc.php");

// Get email from logged-in user
$user_email = $_SESSION['user_email'] ?? null;

if (!$user_email) {
    die("Email not found in session.");
}

// Fetch all orders from orders2 table using email
$stmt = $pdo->prepare("SELECT id, grand_total, status, created_at FROM orders2 WHERE email = ? ORDER BY created_at DESC");
$stmt->execute([$user_email]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$total_orders = count($orders);
$pending_orders = 0;
$total_spent = 0.00;

foreach ($orders as $order) {
    if (strtolower($order['status']) === 'pending') {
        $pending_orders++;
    }
    $total_spent += floatval($order['grand_total']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Dashboard</title>
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
    }

    .dashboard-title {
      font-size: 26px;
      font-weight: 700;
      margin-bottom: 30px;
      color: #3a2f27;
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

    .section h3 {
      font-size: 20px;
      color: #3a2f27;
      margin-bottom: 20px;
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

    .badge {
      padding: 5px 12px;
      border-radius: 12px;
      font-size: 12px;
    }
    .badge.success {
      background: #d1f1d1;
      color: #267c27;
    }
    .badge.pending {
      background: #f9e2ae;
      color: #9e6b00;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
      }
      .main {
        margin-left: 0;
        width: 100%;
        padding: 20px;
      }
    }
  </style>
</head>
<body>
<div class="dashboard">
  <div class="sidebar">
    <h2>Customer Panel</h2>
    <a href="customer_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="myorders.php"><i class="fas fa-shopping-cart"></i> My Orders</a>
    <a href="#"><i class="fas fa-user"></i> My Profile</a>
    <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="main">
    <div class="dashboard-title">Welcome Back 👋</div>

    <div class="stats">
  <div class="stat-card">
    <div class="stat-title">Total Orders</div>
    <div class="stat-value"><?= $total_orders ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-title">Total Spent</div>
    <div class="stat-value">Rs. <?= number_format($total_spent, 2) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-title">Pending Orders</div>
    <div class="stat-value"><?= $pending_orders ?></div>
  </div>
</div>


   <div class="section">
  <h3>Recent Orders</h3>
  <table>
    <thead>
      <tr>
        <th>#Order</th>
        <th>Date</th>
        <th>Total</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (array_slice($orders, 0, 5) as $order): ?>
        <tr>
          <td>#<?= htmlspecialchars($order['id']) ?></td>
          <td><?= date("M d, Y", strtotime($order['created_at'])) ?></td>
          <td>Rs. <?= number_format($order['grand_total'], 2) ?></td>
          <td>
            <span class="badge <?= strtolower($order['status']) === 'pending' ? 'pending' : 'success' ?>">
              <?= ucfirst($order['status']) ?>
            </span>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

  </div>
</div>

<!-- GSAP Animations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
  gsap.from(".sidebar", {x: -200, duration: 0.8, ease: "power2.out"});
  gsap.from(".main", {opacity: 0, y: 30, duration: 0.6, delay: 0.2});
  gsap.from(".stat-card", {
    y: 50, opacity: 0, stagger: 0.2, duration: 0.8, delay: 0.5, ease: "back.out(1.7)"
  });
</script>
</body>
</html>
