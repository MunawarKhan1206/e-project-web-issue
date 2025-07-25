<?php
require_once("../includes/db.inc.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders2 SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo "success";
}
