<?php
session_start();
require_once '../includes/db.inc.php';

// Check if cart and user are set
if (!isset($_SESSION['cart']) || empty($_SESSION['cart']) || !isset($_SESSION['user_id'])) {
    header("Location: ../shop.php");
    exit;
}

$userId = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

// Validate method
$method = $_GET['method'] ?? '';
if ($method !== 'card') {
    header("Location: ../checkout.php");
    exit;
}

// Store order
$recipient = $_SESSION['recipient_name'] ?? 'Unknown';
$orderNumber = 'ORD' . strtoupper(uniqid());
$trackingId = 'TRK' . rand(100000, 999999);
$paymentType = 'Card';
$status = 'Confirmed';
$placedOn = date('Y-m-d H:i:s');

try {
    $conn->beginTransaction();

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders2 (user_id, order_number, recipient_name, payment_type, order_status, placed_on, tracking_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $orderNumber, $recipient, $paymentType, $status, $placedOn, $trackingId]);

    $orderId = $conn->lastInsertId();

    // Insert order items
    foreach ($cart as $productId => $item) {
        $qty = $item['quantity'];

        // Get price from DB
        $stmt = $conn->prepare("SELECT name, price, stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product && $product['stock'] >= $qty) {
            $subtotal = $product['price'] * $qty;

            // Insert item
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, subtotal)
                                    VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $productId, $qty, $subtotal]);

            // Update stock
            $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$qty, $productId]);
        }
    }

    $conn->commit();

    // Save order summary in session
    $_SESSION['last_order_number'] = $orderNumber;
    $_SESSION['recipient_name_display'] = $recipient;

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect to thank you
    header("Location: thankyou.php");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
