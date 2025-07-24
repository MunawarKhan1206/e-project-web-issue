<?php
session_start();
require_once("../includes/db.inc.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Track Your Order</title>
  <link rel="stylesheet" href="../assets/css/dashboard-style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f1f5f9;
    }

    .track-container {
      max-width: 650px;
      margin: 60px auto;
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
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
      background: #ff6f00;
      color: #fff;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      font-size: 15px;
      transition: 0.3s ease;
      cursor: pointer;
    }

    button:hover {
      background: #e65c00;
    }

    .order-info {
      background: #f9fafb;
      padding: 25px 20px;
      margin-top: 30px;
      border-radius: 12px;
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
  align-items: flex-start;
  margin: 40px 0 10px;
  position: relative;
  padding: 0 10px;
}

.progress-step {
  flex: 1;
  text-align: center;
  position: relative;
  color: #9ca3af;
  font-size: 13px;
}

.progress-step .icon {
  font-size: 22px;
  width: 30px;
  height: 30px;
  line-height: 30px;
  background: #d1d5db;
  border-radius: 50%;
  margin: 0 auto 6px;
  position: relative;
  z-index: 2;
}

.progress-step.active .icon {
  background: #10b981;
  color: #fff;
}

.progress-step::after {
  content: '';
  position: absolute;
  top: 15px;
  left: 50%;
  width: 100%;
  height: 3px;
  background: #d1d5db;
  z-index: 1;
  transform: translateX(-50%);
}

.progress-step:last-child::after {
  display: none;
}

.progress-step.active::after {
  background: #10b981;
}

.progress-step.active {
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
    }
  </style>
</head>
<body>

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
          $status_raw = strtolower($order['status'] ?? 'pending');
          $status = ucfirst($status_raw);
          $status_class = match($status_raw) {
              'pending'   => 'badge-warning',
              'approved'  => 'badge-success',
              'delivered' => 'badge-primary',
              'cancelled' => 'badge-danger',
              default     => 'badge-secondary'
          };

          echo '<div class="order-info">';
          echo "<p><strong>Order Number:</strong> " . htmlspecialchars($order['order_number']) . "</p>";
          echo "<p><strong>Status:</strong> <span class='badge {$status_class}'>" . htmlspecialchars($status) . "</span></p>";
          echo "<p><strong>Order Date:</strong> " . date("F j, Y", strtotime($order['created_at'])) . "</p>";
          echo "<p><strong>Total:</strong> Rs. " . number_format($order['grand_total'], 0) . "</p>";
          echo "<p><strong>Payment:</strong> " . htmlspecialchars($order['payment_method']) . "</p>";

          // Steps for tracking
          $steps = ['pending' => '🕒', 'approved' => '✅', 'delivered' => '📦'];
          echo '<div class="progress-container">';
          foreach (array_keys($steps) as $step) {
              $active = array_search($status_raw, array_keys($steps)) >= array_search($step, array_keys($steps)) ? 'active' : '';
              echo "<div class='progress-step {$active}'>";
              echo "<div class='icon'>{$steps[$step]}</div>";
              echo ucfirst($step);
              echo "</div>";
          }
          echo '</div>';

          echo '</div>';
      } else {
          echo '<div class="order-info"><p style="color:red;">⚠ No order found with that number.</p></div>';
      }
  }
  ?>

  <div class="center-btn">
    <a href="customer_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
  </div>
</div>

</body>
</html>
