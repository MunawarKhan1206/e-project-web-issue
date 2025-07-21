<?php
session_start();
require_once '../includes/db.inc.php'; // adjust if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $recipient = $_POST['recipient'] ?? $name; // optional recipient name
        $payment_method = $_POST['payment'] ?? 'COD';
        $subtotal = $_SESSION['subtotal'] ?? 0;
        $discount = $_SESSION['discount'] ?? 0;
        $delivery = $_SESSION['delivery'] ?? 0;
        $grand_total = $_SESSION['grand_total'] ?? 0;

        try {
            // Insert into orders table
            $stmt = $pdo->prepare("INSERT INTO orders (name, email, phone, address, recipient, payment_method, subtotal, discount, delivery, grand_total, order_date) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $phone, $address, $recipient, $payment_method, $subtotal, $discount, $delivery, $grand_total]);
            $order_id = $pdo->lastInsertId();

            // Insert each item into order_items
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, total) 
                                       VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $order_id,
                    $product_id,
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item['price'] * $item['quantity']
                ]);
            }

            // Clear cart & totals
            unset($_SESSION['cart'], $_SESSION['subtotal'], $_SESSION['discount'], $_SESSION['delivery'], $_SESSION['grand_total']);

            // Redirect to thank you page
            header("Location: thankyou.php");
            exit();

        } catch (PDOException $e) {
            echo "Error placing order: " . $e->getMessage();
        }

    } else {
        echo "Cart is empty.";
    }
} else {
    echo "Invalid request.";
}
?>
