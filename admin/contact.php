<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}

require_once '../includes/db.inc.php';
$stmt = $pdo->query("SELECT * FROM contacts ORDER BY id DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Messages</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="products-style.css">

  <style>
    .dashboard-title {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .card-msg {
      margin-bottom: 15px;
    }

    @media (min-width: 768px) {
      .card-msg {
        display: none;
      }
    }

    @media (max-width: 767.98px) {
      .table-responsive {
        display: none;
      }
    }
  </style>
</head>
<body>
<div class="dashboard">
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="returns.php"><i class="fas fa-undo-alt"></i> Returns</a>
    <a href="employees.php"><i class="fas fa-user-tie"></i> Employees</a>
     <a href="customers.php"><i class="fas fa-user-tie"></i> Customers</a>
    <a href="contact.php" class="active"><i class="fas fa-envelope"></i> Contact Messages</a>
    <a href="../frontend/index.php"><i class="fas fa-store"></i> Go to Shop</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="main ">
    <button id="menu-toggle" class="menu-toggle"><i class="fas fa-bars"></i></button>
    <div class="dashboard-title">Contact Messages</div>

    <!-- Desktop Table View -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="thead-light">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
        <?php if (count($messages) > 0): ?>
          <?php foreach ($messages as $msg): ?>
            <tr>
              <td><?= htmlspecialchars($msg['name']) ?></td>
              <td><?= htmlspecialchars($msg['email']) ?></td>
              <td><?= htmlspecialchars($msg['subject']) ?></td>
              <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
              <td><?= date("d M Y, H:i", strtotime($msg['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5">No messages found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Mobile Card View -->
    <div class="d-block d-md-none">
      <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $msg): ?>
          <div class="card card-msg shadow-sm">
            <div class="card-body">
              <h5 class="card-title mb-1"><?= htmlspecialchars($msg['name']) ?> <small class="text-muted">| <?= htmlspecialchars($msg['subject']) ?></small></h5>
              <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($msg['email']) ?></p>
              <p class="mb-2"><strong>Message:</strong> <?= nl2br(htmlspecialchars($msg['message'])) ?></p>
              <p class="text-muted mb-0"><small><?= date("d M Y, H:i", strtotime($msg['created_at'])) ?></small></p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No messages found.</p>
      <?php endif; ?>
    </div>

  </div>
</div>
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
</body>
</html>
