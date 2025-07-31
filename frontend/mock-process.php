<?php
session_start();
require_once '../includes/db.inc.php';

// Check essentials
if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: ../shop.php");
    exit;
}

$userId = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$recipient = $_SESSION['recipient_name'] ?? 'Unknown';

$orderNumber = 'ORD' . strtoupper(uniqid());
$paymentType = 'Card';
$status = 'Confirmed';
$placedOn = date('Y-m-d H:i:s');

// Generate tracking ID format: CC + mmddyy + Order ID later
$prefix = 'CC' . date('mdy'); // CC072925
$trackingId = ''; // Placeholder

try {
    $conn->beginTransaction();

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders2 (user_id, order_number, recipient_name, payment_type, order_status, placed_on, tracking_id)
                            VALUES (?, ?, ?, ?, ?, ?, '')"); // Insert temp tracking_id
    $stmt->execute([$userId, $orderNumber, $recipient, $paymentType, $status, $placedOn]);

    $orderId = $conn->lastInsertId(); // Get order ID
    $trackingId = $prefix . str_pad($orderId, 6, '0', STR_PAD_LEFT); // e.g., CC072925000123

    // Update tracking ID
    $stmt = $conn->prepare("UPDATE orders2 SET tracking_id = ? WHERE id = ?");
    $stmt->execute([$trackingId, $orderId]);

    // Insert order items
    foreach ($cart as $productId => $item) {
        $qty = $item['quantity'];
        $stmt = $conn->prepare("SELECT name, price, stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product && $product['stock'] >= $qty) {
            $subtotal = $product['price'] * $qty;
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, subtotal)
                                    VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $productId, $qty, $subtotal]);

            // Update stock
            $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$qty, $productId]);
        }
    }

    $conn->commit();

    // Save for thankyou.php
    $_SESSION['last_order_number'] = $orderNumber;
    $_SESSION['recipient_name_display'] = $recipient;
    $_SESSION['last_tracking_id'] = $trackingId;

    unset($_SESSION['cart']);

} catch (Exception $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Processing Payment</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    body {
      background: linear-gradient(135deg, #f6f8fc, #dce1e8);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .loader-card {
      background: white;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 400px;
      width: 90%;
      position: relative;
    }
    .loader-card img.bg-logo {
      position: absolute;
      top: 10px;
      left: 10px;
      opacity: 0.08;
      width: 100%;
      height: auto;
    }
    .loader {
      width: 80px;
      height: 80px;
      border: 6px solid #ccc;
      border-top-color: #007bff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 30px auto;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    .payment-icons i {
      font-size: 28px;
      margin: 0 8px;
      color: #495057;
    }
    .msg {
      font-size: 18px;
      color: #333;
      font-weight: 500;
    }
  </style>
</head>
<body>
  <div class="loader-card">
    <img src="../assets/logo.png" class="bg-logo" alt="">
    <h4>Processing Payment</h4>
    <div class="loader"></div>
    <p class="msg">Your payment is being securely verified...</p>
    <div class="payment-icons mt-3">
      <i class="fab fa-cc-visa"></i>
      <i class="fab fa-cc-mastercard"></i>
      <i class="fab fa-cc-amex"></i>
      <i class="fab fa-cc-discover"></i>
    </div>
  </div>

  <script>
    setTimeout(() => {
      window.location.href = "thankyou.php";
    }, 2500);
  </script>
</body>
</html>
