<?php
require_once("../includes/db.inc.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null; // ✅ FIXED HERE
    $status = $_POST['status'] ?? null;

    if ($order_id && $status) {
        $stmt = $pdo->prepare("UPDATE orders2 SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        echo "success";
    } else {
        echo "Missing data.";
    }
} else {
    echo "Invalid request.";
}
