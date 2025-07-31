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
  <link rel="stylesheet" href="customer.css">
   <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="dashboard">
  <div class="sidebar">
    <h2>Customer Panel</h2>
    <a href="customer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="myorders.php"><i class="fas fa-shopping-cart"></i> My Orders</a>
    <a href="customer_return.php"><i class="fas fa-undo-alt"></i> Returns</a>
    <a href="#"><i class="fas fa-user"></i> My Profile</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>


  
  <div class="main">
    <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
  ☰
</button>
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
  <!-- Desktop Table (visible on md and up) -->
<div class="table-responsive d-none d-md-block">
  <table class="table table-bordered text-center custom-table">
   <thead class="custom-thead">
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

<!-- Mobile Cards (visible below md) -->
<div class="d-block d-md-none">
  <?php foreach (array_slice($orders, 0, 5) as $order): ?>
    <div class="card shadow-sm mb-3">
      <div class="card-body p-3">
        <h6 class="card-title mb-2">Order #<?= htmlspecialchars($order['id']) ?></h6>
        <p class="mb-1"><strong>Date:</strong> <?= date("M d, Y", strtotime($order['created_at'])) ?></p>
        <p class="mb-1"><strong>Total:</strong> Rs. <?= number_format($order['grand_total'], 2) ?></p>
        <span class="badge <?= strtolower($order['status']) === 'pending' ? 'pending' : 'success' ?>">
          <?= ucfirst($order['status']) ?>
        </span>
      </div>
    </div>
  <?php endforeach; ?>
</div>

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
<script>
  // Optional: close sidebar when clicking outside on small screens
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
