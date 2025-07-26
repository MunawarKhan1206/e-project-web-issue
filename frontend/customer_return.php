<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../form/index.php");
    exit;
}

require_once("../includes/db.inc.php");

$user_email = $_SESSION['user_email'] ?? null;
if (!$user_email) die("Email not found.");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_number = $_POST['order_number'] ?? '';
    $reason = trim($_POST['reason'] ?? '');

    // Check for duplicates
    $check = $pdo->prepare("SELECT COUNT(*) FROM returns WHERE order_number = ?");
    $check->execute([$order_number]);
    if ($check->fetchColumn() > 0) {
        $error = "Return already requested for this order.";
    } elseif (empty($reason)) {
        $error = "Please provide a reason for return.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO returns (order_number, reason, return_date, status) VALUES (?, ?, NOW(), 'Pending')");
        $stmt->execute([$order_number, $reason]);
        $success = "Return request submitted!";
    }
}

// Fetch recent orders eligible for return (within 7 days)
$stmt = $pdo->prepare("SELECT order_number, grand_total, status, created_at FROM orders2 WHERE email = ? AND status = 'Delivered' ORDER BY created_at DESC");

$stmt->execute([$user_email]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

function isReturnable($date) {
    $orderDate = new DateTime($date);
    $now = new DateTime();
    $interval = $now->diff($orderDate)->days;
    return $interval <= 7;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Return Request</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: #f7f6f3;
      font-family: 'Inter', sans-serif;
      padding: 40px;
    }
    h2 {
      color: #3a2f27;
      margin-bottom: 20px;
    }
    .message {
      padding: 12px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .success { background: #d1f1d1; color: #267c27; }
    .error { background: #fbdada; color: #a94442; }
    table {
      width: 100%;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
      margin-bottom: 40px;
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
    form {
      display: flex;
      flex-direction: column;
      gap: 12px;
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
  </style>
</head>
<body>

<h2>Return Eligible Orders</h2>

<?php if (!empty($success)): ?>
  <div class="message success"><?= $success ?></div>
<?php elseif (!empty($error)): ?>
  <div class="message error"><?= $error ?></div>
<?php endif; ?>

<table>
  <thead>
    <tr>
      <th>Order #</th>
      <th>Date</th>
      <th>Total</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
 <?php
// Inside foreach loop
foreach ($orders as $order):
  $isDelivered = strtolower($order['status']) === 'delivered';
  $isWithin7Days = isReturnable($order['created_at']);

  if ($isDelivered && $isWithin7Days):
?>
    <tr>
      <td>#<?= $order['order_number'] ?></td>
      <td><?= date("M d, Y", strtotime($order['created_at'])) ?></td>
      <td>Rs. <?= number_format($order['grand_total'], 2) ?></td>
      <td><?= ucfirst($order['status']) ?></td>
      <td>
        <form method="POST">
          <input type="hidden" name="order_number" value="<?= $order['order_number'] ?>">
          <textarea name="reason" rows="2" placeholder="Reason for return..." required></textarea>
          <button type="submit">Request Return</button>
        </form>
      </td>
    </tr>
<?php
  endif;
endforeach;
?>

  </tbody>
</table>

</body>
</html>
