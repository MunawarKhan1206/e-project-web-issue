<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}

require_once '../includes/db.inc.php';

if (isset($_POST['delete_product'])) {
    $id = $_POST['delete_id'];

    // Get the image filename
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    // Delete from DB
    $deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($deleteStmt->execute([$id])) {
        // Delete image file if it exists
        if (!empty($image) && file_exists("../frontend/product/" . $image)) {
            unlink("../frontend/product/" . $image);
        }
        header("Location: products.php?deleted=1");
        exit;
    } else {
        header("Location: products.php?error=1");
        exit;
    }
}
?>
