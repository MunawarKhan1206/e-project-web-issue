<?php
// admin/get_order_items.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../includes/db.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_number'])) {
    $orderNumber = $_POST['order_number'];

    $stmt = $pdo->prepare("SELECT product_name, quantity, price FROM orders2 WHERE order_number = ?");
    $stmt->execute([$orderNumber]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($items);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid request']);
exit;
