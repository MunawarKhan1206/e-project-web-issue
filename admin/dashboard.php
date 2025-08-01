<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}

require_once '../includes/db.inc.php';

// Fetch total counts
// $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders2")->fetchColumn();
$total_returns = $pdo->query("SELECT COUNT(*) FROM returns")->fetchColumn();
$total_employees = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'employee'")->fetchColumn();
$total_messages = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();

// Fetch monthly order & return stats
$monthly_orders = [];
$monthly_returns = [];
$monthly_sales = [];
$dailyLabels = [];
$dailyTotals = [];

for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('d M', strtotime($date)); // e.g., "01 Aug"
    $dailyLabels[] = $label;

    $stmt = $pdo->prepare("
        SELECT SUM(oi.quantity * oi.price) AS total
        FROM order_items oi
        JOIN orders2 o ON o.id = oi.order_id
        WHERE DATE(o.created_at) = ?
    ");
    $stmt->execute([$date]);
    $dailyTotals[] = (float) $stmt->fetchColumn() ?? 0;
}

for ($i = 1; $i <= 12; $i++) {
    $stmt3 = $pdo->prepare("
        SELECT SUM(oi.quantity * oi.price) AS monthly_total
        FROM order_items oi
        JOIN orders2 o ON o.id = oi.order_id
        WHERE MONTH(o.created_at) = ?
    ");
    $stmt3->execute([$i]);
    $monthly_sales[] = (float)$stmt3->fetchColumn() ?? 0;
}


for ($i = 1; $i <= 12; $i++) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders2 WHERE MONTH(created_at) = ?");
    $stmt->execute([$i]);
    $monthly_orders[] = $stmt->fetchColumn();
$stmt2 = $pdo->prepare("SELECT COUNT(*) FROM returns WHERE MONTH(return_date) = ?");


    $stmt2->execute([$i]);
    $monthly_returns[] = $stmt2->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body>
<div class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="returns.php"><i class="fas fa-undo-alt"></i> Returns</a>
    <a href="employees.php"><i class="fas fa-user-tie"></i> Employees</a>
    <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
    <a href="notification.php"><i class="fas fa-bell"></i> Notifications</a>
    <a href="../frontend/index.php"><i class="fas fa-store"></i> Go to Shop</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main">
    <button id="menu-toggle" class="menu-toggle"><i class="fas fa-bars"></i></button>

<div class="dashboard-title">Welcome Admin 👋</div>
  <div class="stats">
  <div class="stat-card">
    <div class="stat-title">Total Orders</div>
    <div class="stat-value"><?= $total_orders ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-title">Returns</div>
    <div class="stat-value"><?= $total_returns ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-title">Employees</div>
    <div class="stat-value"><?= $total_employees ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-title">Messages</div>
    <div class="stat-value"><?= $total_messages ?></div>
  </div>
</div>


    <div class="section">
<h3 style="margin-top: 40px;">Monthly Sales (PKR)</h3>
<div class="chart-container">
  <canvas id="salesChart"></canvas>
</div>
      <h3>Monthly Stats</h3>
<div class="chart-container">
  <canvas id="ordersChart"></canvas>
</div>
<div class="card mt-4">
  <div class="card-body">
    <h5 class="card-title">Daily Sales (Last 30 Days)</h5>
    <canvas id="dailySalesChart" height="120"></canvas>
  </div>
</div>


    </div>
  </div>
</div>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
  // GSAP
  gsap.from(".sidebar", {x: -200, duration: 0.8, ease: "power2.out"});
  gsap.from(".main", {opacity: 0, y: 30, duration: 0.6, delay: 0.2});
  gsap.from(".stat-card", {y: 50, opacity: 0, stagger: 0.2, duration: 0.8, delay: 0.5, ease: "back.out(1.7)"});

  // Chart
  const ctx = document.getElementById('ordersChart').getContext('2d');
  const ordersChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [
        {
          label: 'Orders',
          data: <?= json_encode($monthly_orders) ?>,
          backgroundColor: '#3b82f6'
        },
        {
          label: 'Returns',
          data: <?= json_encode($monthly_returns) ?>,
          backgroundColor: '#f59e0b'
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
</script>
<script>
  const salesCtx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Total Sales (PKR)',
        data: <?= json_encode($monthly_sales) ?>,
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointBackgroundColor: '#10b981'
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: value => '₨' + value.toLocaleString()
          }
        }
      }
    }
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
<script>
const dailyLabels = <?= json_encode($dailyLabels) ?>;
const dailyTotals = <?= json_encode($dailyTotals) ?>;
const ctx2 = document.getElementById('dailySalesChart').getContext('2d');
new Chart(ctx2, {
  type: 'bar',
  data: {
    labels: dailyLabels,
    datasets: [{
      label: 'Sales (Rs)',
      data: dailyTotals,
      backgroundColor: 'rgba(91, 44, 111, 0.7)',
      borderColor: '#5c4434',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      x: {
        ticks: {
          autoSkip: true,
          maxTicksLimit: 10
        }
      },
      y: {
        beginAtZero: true,
        ticks: {
          callback: value => `Rs ${value}`
        }
      }
    }
  }
});

</script>


</body>
</html>
