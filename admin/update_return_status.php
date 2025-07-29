<?php
require_once '../includes/db.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if (!in_array($action, ['approve', 'reject'])) {
        http_response_code(400);
        echo "Invalid action";
        exit;
    }

    $status = $action === 'approve' ? 'approved' : 'rejected';

    $stmt = $pdo->prepare("UPDATE returns SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo "success";
} else {
    http_response_code(400);
    echo "Invalid request";
}
