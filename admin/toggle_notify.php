<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}
require_once '../includes/db.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $notify = $_POST['notify'];

    $stmt = $pdo->prepare("UPDATE products SET notify_users = ? WHERE id = ?");
    $stmt->execute([$notify, $product_id]);

    header("Location: products.php?updated");
    exit;
}
?>
