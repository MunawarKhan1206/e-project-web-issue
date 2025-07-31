<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../form/index.php");
    exit;
}
require_once '../includes/db.inc.php';

$userId = $_SESSION['user_id'];
$msg = '';

// Fetch user info
$stmt = $pdo->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $hashed = $stmt->fetchColumn();

    if (!password_verify($current, $hashed)) {
        $msg = '<div class="alert alert-danger">Incorrect current password.</div>';
    } elseif (strlen($new) < 6) {
        $msg = '<div class="alert alert-warning">New password must be at least 6 characters.</div>';
    } elseif ($new !== $confirm) {
        $msg = '<div class="alert alert-warning">New passwords do not match.</div>';
    } else {
        $newHashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$newHashed, $userId]);
        $msg = '<div class="alert alert-success">Password updated successfully!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link rel="stylesheet" href="customer.css"> <!-- your custom CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Customer Panel</h2>
    <a href="customer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="myorders.php"><i class="fas fa-shopping-cart"></i> My Orders</a>
    <a href="customer_return.php"><i class="fas fa-undo-alt"></i> Returns</a>
    <a href="#" class="active"><i class="fas fa-user"></i> My Profile</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <main class="main">
    <button class="menu-toggle" id="toggleSidebar"><i class="fas fa-bars"></i></button>

    <h2 class="dashboard-title">My Profile</h2>

    <?= $msg ?>

    <div class="row">
      <!-- User Info -->
      <div class="col-md-6 mb-4">
        <div class="card p-4">
          <h5 class="mb-3">Account Details</h5>
          <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
          <p><strong>Joined:</strong> <?= date("F d, Y", strtotime($user['created_at'])) ?></p>
        </div>
      </div>

      <!-- Password Form -->
      <div class="col-md-6 mb-4">
        <div class="card p-4">
          <h5 class="mb-3">Change Password</h5>
          <form method="POST">
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="new_password">New Password</label>
              <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
            </div>
            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-outline-primary mt-3">Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- GSAP Animations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
  const toggle = document.getElementById('toggleSidebar');
  const sidebar = document.querySelector('.sidebar');
  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });

  gsap.from(".sidebar", {x: -200, duration: 0.8, ease: "power2.out"});
  gsap.from(".main", {opacity: 0, y: 30, duration: 0.6, delay: 0.2});
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
