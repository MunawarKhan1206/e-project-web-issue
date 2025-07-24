<?php
session_start();
require_once("../includes/db.inc.php"); // This defines $pdo

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate required fields
    $required_fields = ['first_name', 'last_name', 'address', 'email', 'phone', 'payment_method'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo "Missing required field: " . $field;
            exit;
        }
    }

    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $customer_name = $first_name . ' ' . $last_name;

    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method'];

    // Fetch cart data from session
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        echo "Your cart is empty.";
        exit;
    }

    // Totals from session (already calculated in cart.php)
    $subtotal = $_SESSION['subtotal'] ?? 0;
    $discount = $_SESSION['discount'] ?? 0;
    $delivery = $_SESSION['delivery'] ?? 0;
    $grand_total = $_SESSION['grand_total'] ?? 0;

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Insert into orders
        $order_stmt = $pdo->prepare("
            INSERT INTO orders2 (customer_name, address, email, phone, payment_method, subtotal, discount, delivery, totals)
            VALUES (:customer_name, :address, :email, :phone, :payment_method, :subtotal, :discount, :delivery, :totals)
        ");
        $order_stmt->execute([
            ':customer_name' => $customer_name,
            ':address' => $address,
            ':email' => $email,
            ':phone' => $phone,
            ':payment_method' => $payment_method,
            ':subtotal' => $subtotal,
            ':discount' => $discount,
            ':delivery' => $delivery,
            ':totals' => $grand_total
        ]);

        $order_id = $pdo->lastInsertId();

        // Insert order items
        $item_stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
            VALUES (:order_id, :product_id, :product_name, :quantity, :price)
        ");

        foreach ($cart as $item) {
            $item_stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $item['id'],
                ':product_name' => $item['name'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
        }

        // Commit
        $pdo->commit();

        // Clear cart session
        unset($_SESSION['cart'], $_SESSION['subtotal'], $_SESSION['discount'], $_SESSION['delivery'], $_SESSION['grand_total']);

        echo "<script>
            alert('Order placed successfully!');
            window.location.href = 'thankyou.php';
        </script>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Order failed: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
