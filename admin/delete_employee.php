<?php
require_once '../includes/db.inc.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
  $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'employee'");
  $stmt->execute([$_POST['id']]);
  header("Location: employees.php?deleted=1");
  exit;
}
?>
