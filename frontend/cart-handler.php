<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    switch ($_GET['action']) {
        case 'add':
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$id] = ['quantity' => 1];
            }
            // Optional: redirect back to shop/cart
            header("Location: cart.php");
            exit;

        case 'remove':
            unset($_SESSION['cart'][$id]);
            header("Location: cart.php");
            exit;

        case 'clear':
            $_SESSION['cart'] = [];
            header("Location: cart.php");
            exit;
    }
}

// For AJAX use (e.g. cart icon update)
echo count($_SESSION['cart']);
