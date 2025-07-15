<?php
session_start();
require_once '../includes/db.inc.php';
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if($_GET['action'] == 'add' && isset($_GET['id'])){
    $id = (int) $_GET['id'];
    if(!isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id] = 1; // Add item with quantity 1
    } else {
        $_SESSION['cart'][$id]++; // Increment quantity if item already exists
    }
    header("Location: cart.php"); // Redirect to cart page after adding item
    exit();
}
?>