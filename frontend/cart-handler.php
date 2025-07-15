<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    switch ($_GET['action']) {
        case 'add':
            // If already in cart, increase quantity
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity'] += 1;
            } else {
                // Normally you'd fetch details from DB. For now, only ID is stored.
                $_SESSION['cart'][$id] = ['quantity' => 1];
            }
            break;

        case 'remove':
            unset($_SESSION['cart'][$id]);
            break;

        case 'clear':
            $_SESSION['cart'] = [];
            break;
    }
}

// For AJAX response (e.g., cart count)
echo count($_SESSION['cart']);
