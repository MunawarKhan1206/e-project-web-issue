<?php
require_once '../includes/db.inc.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = 'employee';

  // Prevent duplicate emails
  $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $check->execute([$email]);
  if ($check->rowCount() > 0) {
    header("Location: employees.php?error=exists");
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->execute([$name, $email, $password, $role]);
  header("Location: employees.php?added=1");
  exit;
}
?>
