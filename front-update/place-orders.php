<?php
session_start();
require_once '../includes/db.inc.php'; // adjust if needed

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: checkout.php?error=Cart is empty");
    exit;
}

// 1. Collect Form Data
$firstname   = $_POST['firstname'] ?? '';
$lastname    = $_POST['lastname'] ?? '';
$country     = $_POST['country'] ?? '';
$street      = $_POST['street_address'] ?? '';
$apartment   = $_POST['apartment'] ?? '';
$city        = $_POST['city'] ?? '';
$postcode    = $_POST['postcode'] ?? '';
$phone       = $_POST['phone'] ?? '';
$email       = $_POST['email'] ?? '';
$totalAmount = $_POST['total_amount'] ?? 0;

// (Optional) Save customer if needed — or use logged-in user ID later
$customer_id = null;

// 2. Insert into `orders`
try {
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, order_date, total_amount, status) VALUES (?, NOW(), ?, 'Pending')");
    $stmt->execute([$customer_id, $totalAmount]);
    $order_id = $conn->lastInsertId();
} catch (PDOException $e) {
    die("Error placing order: " . $e->getMessage());
}

// 3. Optional: Insert each item into order_items
foreach ($_SESSION['cart'] as $item) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
}


// 4. Clear Cart
unset($_SESSION['cart']);
// 5. Redirect
header("Location: thank-you.php?order=$order_id");
exit;
