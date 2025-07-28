<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit;
}
require_once '../includes/db.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if (!in_array($action, ['approve', 'reject'])) {
        http_response_code(400);
        exit("Invalid action");
    }

    $status = $action === 'approve' ? 'approved' : 'rejected';

    $stmt = $pdo->prepare("UPDATE returns SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo "success";
}
?>
