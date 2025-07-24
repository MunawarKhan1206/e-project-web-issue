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

    // Totals from session
    $subtotal = $_SESSION['totals']['subtotal'] ?? 0;
    $discount = $_SESSION['totals']['discount'] ?? 0;
    $delivery = $_SESSION['totals']['delivery'] ?? 0;
    $grand_total = $_SESSION['totals']['grand_total'] ?? 0;

    // Generate unique 16-digit order number
    $prefix = strtoupper($payment_method) === 'VPP' ? 'VPP' : 'PP';
    $order_number = $prefix . date('Ymd') . rand(100000, 999999);

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Insert into orders
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

        // Insert order items
        $item_stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :quantity, :price)
        ");

        foreach ($cart as $product_id => $item) {
            // Fetch product details from DB
            $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            if (!$product) continue; // Skip if product no longer exists

            $quantity = $item['quantity'];
            $price = $product['price'];
            $stock = $product['stock'];

            // Insert into order_items
            $item_stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':quantity' => $quantity,
                ':price' => $price
            ]);

            // Update product stock
            $new_stock = max(0, $stock - $quantity);
            $update_stock_stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
            $update_stock_stmt->execute([$new_stock, $product_id]);
        }

        // Commit
        $pdo->commit();

        // Store order info in session
        $_SESSION['order_number'] = $order_number;
        $_SESSION['recipient_name'] = $customer_name;

        // Clear cart and totals
        unset($_SESSION['cart'], $_SESSION['totals']);

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
