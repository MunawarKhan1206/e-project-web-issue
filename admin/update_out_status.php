<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit("Unauthorized");
}
require_once("../includes/db.inc.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE products SET is_temp_out_of_stock = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo "Updated";
}
?>
