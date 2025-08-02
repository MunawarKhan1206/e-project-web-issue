<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
  header("Location: ../form/index.php");
  exit;
}
require_once("../../includes/db.inc.php");


$user_email = $_SESSION['user_email'] ?? null;
if (!$user_email) die("Email not found.");

// Handle Return Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $order_number = $_POST['order_number'] ?? '';
  $reason = trim($_POST['reason'] ?? '');

  $check = $pdo->prepare("SELECT COUNT(*) FROM returns WHERE order_number = ?");
  $check->execute([$order_number]);
  if ($check->fetchColumn() > 0) {
    $error = "Return already requested for this order.";
  } elseif (empty($reason)) {
    $error = "Please provide a reason.";
  } else {
    $stmt = $pdo->prepare("INSERT INTO returns (order_number, reason, return_date, status) VALUES (?, ?, NOW(), 'Pending')");
    $stmt->execute([$order_number, $reason]);
    $success = "Return request submitted!";
  }
}

// Fetch orders
$stmt = $pdo->prepare("SELECT order_number, grand_total, status, created_at FROM orders2 WHERE email = ? ORDER BY created_at DESC");
$stmt->execute([$user_email]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

function isReturnable($date)
{
  $orderDate = new DateTime($date);
  $now = new DateTime();
  return $now->diff($orderDate)->days <= 7;
}

// Fetch return statuses
$returnStatus = [];
$orderNums = array_column($orders, 'order_number');

if (count($orderNums)) {
  $placeholders = implode(',', array_fill(0, count($orderNums), '?'));
  $returned = $pdo->prepare("SELECT order_number, status FROM returns WHERE order_number IN ($placeholders)");
  $returned->execute($orderNums);
  foreach ($returned->fetchAll(PDO::FETCH_ASSOC) as $rtn) {
    $returnStatus[$rtn['order_number']] = $rtn['status'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Return Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
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
      top: 0;
      left: 0;
      z-index: 1000;
      transition: left 0.3s ease;
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

    .sidebar a:hover,
    .sidebar a.active {
      background: #c29958;
      color: #fff
    }

    .main {
      margin-left: 240px;
      width: calc(100% - 240px);
      padding: 40px 30px;
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
      margin-bottom: 20px;
      /* adds spacing below toggle */
    }

    h2 {
      margin-bottom: 20px;
      color: #3a2f27;
    }

    .message {
      padding: 12px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .success {
      background: #d1f1d1;
      color: #267c27;
    }

    .error {
      background: #fbdada;
      color: #a94442;
    }

    table {
      width: 100%;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      overflow-x: auto;
    }

    th,
    td {
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
      padding: 5px 10px;
      border-radius: 8px;
      font-size: 12px;
      /* color: white; */
    }

    .badge.Pending {
      background-color: #f0ad4e;
    }

    .badge.Approved {
      background-color: #5cb85c;
    }

    .badge.Rejected {
      background-color: #d9534f;
    }

    .text-muted {
      color: #999;
      font-size: 14px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 10px;
    }

    textarea {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      resize: vertical;
    }

    button {
      background: #c29958;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }

    button:hover {
      background: #a87d3e;
    }

    @media (max-width: 768px) {
      .sidebar {
        left: -100%;
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

      table,
      th,
      td {
        font-size: 14px;
      }
    }

    /* Beige Dashboard Table Styling */
    .custom-table {
      background-color: #fffdf9;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .custom-thead th {
      background-color: #f9f4ef;
      /* soft beige */
      color: #333;
      font-weight: 600;
      border-bottom: 2px solid #e4dccc;
    }

    .custom-table td {
      background-color: #fffdf9;
      color: #444;
      vertical-align: middle;
      border-color: #e8ded0;
    }

    .custom-table.table-striped tbody tr:nth-of-type(odd) {
      background-color: #fefaf3;
    }

    .custom-table .btn-outline-primary {
      border-color: #c6a87d;
      color: #5a3e1b;
    }

    .custom-table .btn-outline-primary:hover {
      background-color: #c6a87d;
      color: white;
    }
  </style>
</head>

<body>
  <div class="dashboard">
    <div class="sidebar">
      <h2>Customer Panel</h2>
      <a href="customer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
      <a href="myorders.php"><i class="fas fa-shopping-cart"></i> My Orders</a>
      <a href="customer_return.php"><i class="fas fa-undo-alt"></i> Returns</a>
      <a href="customer-profile.php"><i class="fas fa-user"></i> My Profile</a>
      <a href="frontend/index.php"><i class="fas fa-store"></i> Go to Shop</a>
      <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
      <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>
      <h2>Return Eligible & Submitted Orders</h2>

      <?php if (!empty($success)): ?>
        <div class="message success"><?= $success ?></div>
      <?php elseif (!empty($error)): ?>
        <div class="message error"><?= $error ?></div>
      <?php endif; ?>

      <!-- DESKTOP VIEW: table -->
      <div class="table-responsive d-none d-md-block">
        <table class="table table-bordered text-center custom-table">
          <thead class="custom-thead">
            <tr>
              <th>Order #</th>
              <th>Date</th>
              <th>Total</th>
              <th>Status</th>
              <th>Return</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order):
              $orderNum = $order['order_number'];
              $created = $order['created_at'];
              $already = isset($returnStatus[$orderNum]);
              $returnable = strtolower($order['status']) === 'delivered' && isReturnable($created);

              // Determine order status badge class
              $status = strtolower($order['status']);
              $statusClass = match ($status) {
                'pending'   => 'badge badge-warning',
                'processed' => 'badge badge-primary',
                'shipped'   => 'badge badge-info',
                'delivered' => 'badge badge-success',
                'cancelled' => 'badge badge-danger',
                default     => 'badge badge-secondary',
              };

              // Determine return status badge class if exists
              $returnClass = '';
              if ($already) {
                $r = strtolower($returnStatus[$orderNum]);
                $returnClass = match ($r) {
                  'pending'  => 'badge badge-warning',
                  'approved' => 'badge badge-success',
                  'rejected' => 'badge badge-danger',
                  default    => 'badge badge-secondary',
                };
              }
            ?>
              <tr>
                <td>#<?= htmlspecialchars($orderNum) ?></td>
                <td><?= date("M d, Y", strtotime($created)) ?></td>
                <td>Rs. <?= number_format($order['grand_total'], 2) ?></td>
                <td><span class="<?= $statusClass ?>"><?= ucfirst($status) ?></span></td>
                <td>
                  <?php if ($already): ?>
                    <span class="<?= $returnClass ?>"><?= ucfirst($returnStatus[$orderNum]) ?></span>
                  <?php elseif ($returnable): ?>
                    <form method="POST" class="d-flex flex-column align-items-center">
                      <input type="hidden" name="order_number" value="<?= htmlspecialchars($orderNum) ?>">
                      <textarea name="reason" rows="2" class="form-control mb-1" placeholder="Reason..." required></textarea>
                      <button type="submit" class="btn btn-sm btn-outline-primary text-dark">Request</button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted">Not Eligible</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>

        </table>
      </div>

      <!-- MOBILE VIEW: cards -->
      <div class="d-md-none">
        <?php foreach ($orders as $order):
          $orderNum = $order['order_number'];
          $created = $order['created_at'];
          $already = isset($returnStatus[$orderNum]);
          $returnable = strtolower($order['status']) === 'delivered' && isReturnable($created);
        ?>
          <div class="card mb-3 border shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Order #<?= htmlspecialchars($orderNum) ?></h5>
              <p><strong>Date:</strong> <?= date("M d, Y", strtotime($created)) ?></p>
              <p><strong>Total:</strong> Rs. <?= number_format($order['grand_total'], 2) ?></p>
              <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
              <p><strong>Return:</strong><br>
                <?php if ($already): ?>
                  <span><?= $returnStatus[$orderNum] ?></span>
                <?php elseif ($returnable): ?>
              <form method="POST">
                <input type="hidden" name="order_number" value="<?= htmlspecialchars($orderNum) ?>">
                <textarea name="reason" rows="2" class="form-control mb-2" placeholder="Reason..." required></textarea>
                <button type="submit" class="btn btn-sm btn-outline-primary text-dark w-100">Request</button>
              </form>
            <?php else: ?>
              <span class="text-muted">Not Eligible</span>
            <?php endif; ?>
            </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>

  </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script>
    gsap.from(".sidebar", {
      x: -200,
      duration: 0.8,
      ease: "power2.out"
    });
    gsap.from(".main", {
      opacity: 0,
      y: 30,
      duration: 0.6,
      delay: 0.2
    });
    gsap.from(".stat-card", {
      y: 50,
      opacity: 0,
      stagger: 0.2,
      duration: 0.8,
      delay: 0.5,
      ease: "back.out(1.7)"
    });
  </script>
  <script>
    document.addEventListener("click", function(e) {
      const sidebar = document.querySelector(".sidebar");
      const toggle = document.querySelector(".menu-toggle");
      if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove("active");
      }
    });
  </script>
</body>


</html>