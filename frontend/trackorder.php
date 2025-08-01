<?php
session_start();
require_once("../includes/db.inc.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<head>
    <title>Minishop - Free Bootstrap 4 Template by Colorlib</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">

    
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f1f5f9;
    }

    .track-container {
      max-width: 700px;
      margin: 60px auto;
      background: #fff;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    h3 {
      text-align: center;
      margin-bottom: 30px;
      color: #1f2937;
    }

    input[type="text"] {
      width: 100%;
      padding: 14px;
      margin-bottom: 20px;
      border: 1px solid #cbd5e1;
      border-radius: 10px;
      font-size: 15px;
    }

    button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, #f97316, #f59e0b);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      font-size: 15px;
      transition: 0.3s ease;
      cursor: pointer;
    }

    button:hover {
      background: linear-gradient(90deg, #ea580c, #d97706);
    }

    .order-info {
      background: #f9fafb;
      padding: 25px 20px;
      margin-top: 30px;
      border-radius: 14px;
      border: 1px solid #e5e7eb;
    }

    .order-info p {
      font-size: 15px;
      margin: 12px 0;
      color: #374151;
    }

    .order-info strong {
      color: #111827;
    }

    .badge {
      padding: 6px 16px;
      border-radius: 999px;
      font-size: 13px;
      color: white;
      display: inline-block;
      font-weight: 500;
    }

    .badge-warning { background-color: #f59e0b; }
    .badge-success { background-color: #10b981; }
    .badge-danger  { background-color: #ef4444; }
    .badge-primary { background-color: #3b82f6; }
    .badge-secondary { background-color: #6b7280; }

    .progress-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      margin: 40px 0 10px;
      padding: 0 10px;
    }

    .progress-line {
      position: absolute;
      top: 22px;
      left: 50px;
      right: 50px;
      height: 4px;
      background: linear-gradient(to right, #10b981 0%, #d1d5db 100%);
      z-index: 0;
      border-radius: 2px;
    }

    .progress-step {
      flex: 1;
      text-align: center;
      position: relative;
      z-index: 2;
    }

    .progress-step .icon {
      font-size: 20px;
      width: 40px;
      height: 40px;
      line-height: 40px;
      border-radius: 50%;
      margin: 0 auto 8px;
      background: #d1d5db;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.4s ease;
    }

    .progress-step.active .icon {
      background: #10b981;
    }

    .progress-step .label {
      font-size: 13px;
      color: #9ca3af;
    }

    .progress-step.active .label {
      color: #111827;
      font-weight: 600;
    }

    .btn-back {
      display: inline-block;
      margin-top: 25px;
      background: linear-gradient(135deg, #FDC830, #F37335);
      color: #fff;
      font-weight: 600;
      padding: 10px 25px;
      border-radius: 50px;
      text-decoration: none;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
      text-align: center;
    }

    .btn-back:hover {
      background: linear-gradient(135deg, #fbb034, #ffdd00);
      color: #000;
      transform: scale(1.05);
    }

    .center-btn {
      text-align: center;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <?php 
  include ("navbar.php")
  ?>
<div class="track-container">
  <h3>Track Your Order</h3>
  <form method="POST">
    <input type="text" name="order_number" placeholder="Enter your Order Number" required>
    <button type="submit" name="track">Check Status</button>
  </form>

  <?php
  if (isset($_POST['track'])) {
      $order_number = trim($_POST['order_number']);
      $stmt = $pdo->prepare("SELECT * FROM orders2 WHERE order_number = ?");
      $stmt->execute([$order_number]);
      $order = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($order) {
          $status_raw = strtolower($order['status'] ?? 'processed');
          $status = ucfirst($status_raw);
          $status_class = match($status_raw) {
              'processed' => 'badge-warning',
              'shipped'   => 'badge-primary',
              'delivered' => 'badge-success',
              default     => 'badge-secondary'
          };

          echo '<div class="order-info">';
          echo "<p><strong>Order Number:</strong> " . htmlspecialchars($order['order_number']) . "</p>";
          echo "<p><strong>Status:</strong> <span class='badge {$status_class}'>" . htmlspecialchars($status) . "</span></p>";
          echo "<p><strong>Order Date:</strong> " . date("F j, Y", strtotime($order['created_at'])) . "</p>";
          echo "<p><strong>Total:</strong> Rs. " . number_format($order['grand_total'], 0) . "</p>";
          echo "<p><strong>Payment:</strong> " . htmlspecialchars($order['payment_method']) . "</p>";

          $steps = [
              'processed' => ['label' => 'Processed', 'icon' => 'fa-solid fa-gear'],
              'shipped'   => ['label' => 'Shipped', 'icon' => 'fa-solid fa-truck'],
              'delivered' => ['label' => 'Delivered', 'icon' => 'fa-solid fa-box-open']
          ];
          $statusKeys = array_keys($steps);
          $currentIndex = array_search($status_raw, $statusKeys);

          echo '<div class="progress-container">';
          echo '<div class="progress-line"></div>';

          $i = 0;
          foreach ($steps as $key => $step) {
              $isActive = $i <= $currentIndex ? 'active' : '';
              echo "<div class='progress-step {$isActive}'>";
              echo "<div class='icon'><i class='{$step['icon']}'></i></div>";
              echo "<div class='label'>{$step['label']}</div>";
              echo "</div>";
              $i++;
          }
          echo '</div>'; // progress-container
          echo '</div>'; // order-info
      } else {
          echo '<div class="order-info"><p style="color:red;">⚠ No order found with that number.</p></div>';
      }
  }
  ?>

  <div class="center-btn">
    <a href="customer_dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  </div>
  <div class="center-btn">
    <a href="../frontend/index.php" class="btn-back"><i class="fas fa-home"></i> Return to Home</a>
  </div>
</div>

<script>
  gsap.from(".progress-step .icon", {
    scale: 0,
    opacity: 0,
    duration: 0.6,
    stagger: 0.2,
    ease: "back.out(1.7)"
  });
</script>
<?php
	include ("footer.php");
	?>
</body>
</html>
