<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

require_once '../includes/db.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($orderId && $status) {
        $stmt = $pdo->prepare("UPDATE orders2 SET status = ? WHERE id = ?");
        $updated = $stmt->execute([$status, $orderId]);

        if ($updated) {
            echo "success";
        } else {
            http_response_code(500);
            echo "Error updating order";
        }
    } else {
        http_response_code(400);
        echo "Invalid data";
    }
}
?>
