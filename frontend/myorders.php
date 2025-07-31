<?php
session_start();
require_once '../includes/db.inc.php';

if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../form/index.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$stmt = $pdo->prepare("SELECT * FROM orders2 WHERE email = ? ORDER BY created_at DESC");
$stmt->execute([$user_email]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


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

    .page-title {
      font-size: 26px;
      font-weight: 700;
      margin-bottom: 30px;
      color: #3a2f27;
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
    .badge.pending {
      background: #f9e2ae;
      color: #9e6b00;
    }
    .badge.confirmed {
      background: #d1ecf1;
      color: #0c5460;
    }
    .badge.shipped {
      background: #d1e7dd;
      color: #155724;
    }
    .badge.delivered {
      background: #d4edda;
      color: #1a662c;
    }
    .badge.cancelled {
      background: #f8d7da;
      color: #842029;
    }

    .btn-view {
      padding: 6px 12px;
      font-size: 13px;
      border: 1px solid #c29958;
      background: transparent;
      color: #c29958;
      border-radius: 6px;
      text-decoration: none;
      transition: 0.3s;
    }

    .btn-view:hover {
      background: #c29958;
      color: #fff;
    }

    .empty-msg {
      background: #fff3cd;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      color: #856404;
    }

.menu-toggle {
  display: none;
  font-size: 24px;
  background: #c29958;
  color: #fff;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
  margin-bottom: 20px; /* adds spacing below toggle */
}


.table-container {
  overflow-x: auto;
  background: white;
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}
.table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
.order-cards {
  display: flex;
  flex-direction: column;
  gap: 15px;
  padding: 10px;
}
.order-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 15px;
  font-size: 14px;
  border-left: 4px solid #eee;
}
.order-card div {
  margin-bottom: 6px;
}
.order-card .badge {
  padding: 4px 8px;
  border-radius: 5px;
  font-size: 12px;
}
@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: -100%;
    width: 240px;
    height: 100%;
    background: #312b26;
    transition: left 0.3s ease;
    z-index: 1000;
  }

  .sidebar.active {
    left: 0;
  }

  .main {
    margin-left: 0;
    width: 100%;
    padding: 20px;
  }

  .menu-toggle {
    display: block;
  }

  table {
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;
  }
}

  </style>
</head>
<!-- <body> -->
<body>
  <div class="dashboard">


    <!-- Sidebar -->
    <div class="sidebar">
      <h2>Customer Panel</h2>
      <a href="customer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
      <a href="myorders.php" class="active"><i class="fas fa-shopping-cart"></i> My Orders</a>
      <a href="#"><i class="fas fa-user"></i> My Profile</a>
      <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main">
          <!-- Menu Toggle Button -->
    <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
  ☰
</button>
      <div class="page-title"><i class="fas fa-box-open"></i> My Orders</div>

      <?php if ($orders): ?>
     <!-- Desktop & Tablet View (Table) -->
<!-- Table view for desktop -->
<div class="table-responsive d-none d-md-block">
  <table class="table table-bordered table-striped text-center">
    <thead class="thead-dark">
      <tr>
        <th>#</th>
        <th>Order No.</th>
        <th>Date</th>
        <th>Total</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $index => $order): ?>
        <?php
          $statusClass = match (strtolower($order['status'])) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-info',
            'shipped' => 'badge-primary',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary',
          };
        ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td>#<?= htmlspecialchars($order['order_number']) ?></td>
          <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
          <td>Rs. <?= number_format($order['grand_total'], 0) ?></td>
          <td><span class="badge <?= $statusClass ?>"><?= ucfirst($order['status']) ?></span></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Card view for mobile -->
<div class="d-md-none">
  <?php foreach ($orders as $index => $order): ?>
    <?php
      $statusClass = match (strtolower($order['status'])) {
        'pending' => 'badge-warning',
        'confirmed' => 'badge-info',
        'shipped' => 'badge-primary',
        'delivered' => 'badge-success',
        'cancelled' => 'badge-danger',
        default => 'badge-secondary',
      };
    ?>
    <div class="card mb-3 shadow-sm">
      <div class="card-body">
        <h5 class="card-title">#<?= htmlspecialchars($order['order_number']) ?></h5>
        <p class="card-text mb-1"><strong>Date:</strong> <?= date('M d, Y', strtotime($order['created_at'])) ?></p>
        <p class="card-text mb-1"><strong>Total:</strong> Rs. <?= number_format($order['grand_total'], 0) ?></p>
        <p class="card-text"><strong>Status:</strong> <span class="badge <?= $statusClass ?>"><?= ucfirst($order['status']) ?></span></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>



      <?php else: ?>
        <div class="empty-msg">
          <i class="fas fa-info-circle"></i> You haven’t placed any orders yet.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Responsive Sidebar Script -->
  <script>
    document.addEventListener("click", function (e) {
      const sidebar = document.querySelector(".sidebar");
      const toggle = document.querySelector(".menu-toggle");
      if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove("active");
      }
    });
  </script>



<!-- GSAP Animations -->
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
</body>
</html>
