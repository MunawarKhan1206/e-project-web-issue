<?php
session_start();
require_once("../includes/db.inc.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" || 
    ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['payment']) && $_GET['payment'] === 'success')) {

    // Restore POST data for mock/redirected payments
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $_POST = $_SESSION['mock_post'] ?? [];
        if (empty($_POST)) {
            $_SESSION['toast'] = ['type' => 'danger', 'msg' => "Session expired. Please try again."];
            header("Location: checkout.php");
            exit;
        }
    }

    // Validate required fields
    $required_fields = ['first_name', 'last_name', 'address', 'email', 'phone', 'payment_method'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['toast'] = ['type' => 'danger', 'msg' => "Missing required field: $field"];
            header("Location: checkout.php");
            exit;
        }
    }

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $customer_name = "$first_name $last_name";
    $address = $_POST['address'];
$email = $_SESSION['user_email'] ?? '';
    $phone = $_POST['phone'];
    $payment_method = strtolower($_POST['payment_method']);

    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        $_SESSION['toast'] = ['type' => 'warning', 'msg' => "Your cart is empty."];
        header("Location: checkout.php");
        exit;
    }

    $subtotal = $_SESSION['totals']['subtotal'] ?? 0;
    $discount = $_SESSION['totals']['discount'] ?? 0;
    $delivery = $_SESSION['totals']['delivery'] ?? 0;
    $grand_total = $_SESSION['totals']['grand_total'] ?? 0;

    // Generate order number with appropriate prefix
    $prefix = ($payment_method === 'cod') ? 'VPP' : 'CC';
    $order_number = $prefix . rand(10000000, 99999999); // e.g., CC90837265

    try {
        $pdo->beginTransaction();

        // Insert into orders2
        $order_stmt = $pdo->prepare("
            INSERT INTO orders2 (order_number, customer_name, address, email, phone, payment_method, subtotal, discount, delivery, grand_total)
            VALUES (:order_number, :customer_name, :address, :email, :phone, :payment_method, :subtotal, :discount, :delivery, :grand_total)
        ");
        $order_stmt->execute([
            ':order_number' => $order_number,
            ':customer_name' => $customer_name,
            ':address' => $address,
            ':email' => $email,
            ':phone' => $phone,
            ':payment_method' => $payment_method,
            ':subtotal' => $subtotal,
            ':discount' => $discount,
            ':delivery' => $delivery,
            ':grand_total' => $grand_total
        ]);

        $order_id = $pdo->lastInsertId();

        // Insert order items and update stock
        $item_stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :quantity, :price)
        ");

        foreach ($cart as $product_id => $item) {
            $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            if (!$product) continue;

            $item_stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':quantity' => $item['quantity'],
                ':price' => $product['price']
            ]);

            $new_stock = max(0, $product['stock'] - $item['quantity']);
            $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?")->execute([$new_stock, $product_id]);
        }

        $pdo->commit();

        // ✅ Set order success session values
        $_SESSION['order_number'] = $order_number;
        $_SESSION['recipient_name'] = $customer_name;

        // ✅ Clear cart, totals, and mock_post (important!)
        unset($_SESSION['cart'], $_SESSION['totals'], $_SESSION['mock_post']);

        $_SESSION['toast'] = ['type' => 'success', 'msg' => 'Order placed successfully!'];
        header("Location: thankyou.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['toast'] = ['type' => 'danger', 'msg' => "Order failed: " . $e->getMessage()];
        header("Location: checkout.php");
        exit;
    }

} else {
    $_SESSION['toast'] = ['type' => 'danger', 'msg' => "Invalid request."];
    header("Location: checkout.php");
    exit;
}
