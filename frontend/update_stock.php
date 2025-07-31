<?php
require_once("../includes/db.inc.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $status = (int)$_POST['status'];

    // If status = 0 → update to stock = 0, else restore to default (say, 10)
    $stockValue = $status === 1 ? 10 : 0;

    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $stmt->execute([$stockValue, $id]);
}
