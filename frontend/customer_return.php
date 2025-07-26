<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../form/index.php");
    exit;
}

require_once("../includes/db.inc.php");

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

$stmt = $pdo->prepare("SELECT order_number, grand_total, status, created_at FROM orders2 WHERE email = ? ORDER BY created_at DESC");
$stmt->execute([$user_email]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

function isReturnable($date) {
    $orderDate = new DateTime($date);
    $now = new DateTime();
    return $now->diff($orderDate)->days <= 7;
}

$returned = $pdo->prepare("SELECT order_number, status FROM returns WHERE order_number IN (" .
    implode(',', array_fill(0, count($orders), '?')) . ")");
$orderNums = array_column($orders, 'order_number');
$returnStatus = [];

if (count($orderNums)) {
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
      overflow-x: auto;
    }
    th, td {
      padding: 14px 20px;
      text-align: left;
    }
    th {
      background: #f2ebe0;
      color: #5c4434;
    }
    tr:nth-child(even) { background: #fdfdfd; }
    tr:hover { background: #f7f1e6; }
    .badge {
      padding: 5px 10px;
      border-radius: 8px;
      font-size: 12px;
      color: white;
    }
    .badge.Pending { background-color: #f0ad4e; }
    .badge.Approved { background-color: #5cb85c; }
    .badge.Rejected { background-color: #d9534f; }
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
    button:hover { background: #a87d3e; }
    @media (max-width: 768px) {
      body { padding: 20px; }
      table, th, td { font-size: 14px; }
    }
  </style>
</head>
<body>

<h2>Return Eligible & Submitted Orders</h2>

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
      <th>Return</th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($orders as $order):
      $orderNum = $order['order_number'];
      $created = $order['created_at'];
      $already = isset($returnStatus[$orderNum]);
      $returnable = strtolower($order['status']) === 'delivered' && isReturnable($created);
    ?>
    <tr>
      <td>#<?= htmlspecialchars($orderNum) ?></td>
      <td><?= date("M d, Y", strtotime($created)) ?></td>
      <td>Rs. <?= number_format($order['grand_total'], 2) ?></td>
      <td><?= ucfirst($order['status']) ?></td>
      <td>
        <?php if ($already): ?>
          <span class="badge <?= $returnStatus[$orderNum] ?>"> <?= $returnStatus[$orderNum] ?> </span>
        <?php elseif ($returnable): ?>
        <form method="POST">
          <input type="hidden" name="order_number" value="<?= htmlspecialchars($orderNum) ?>">
          <textarea name="reason" rows="2" placeholder="Reason for return..." required></textarea>
          <button type="submit">Request Return</button>
        </form>
        <?php else: ?>
          <span class="text-muted">Not Eligible</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</body>
</html>
