<?php
require_once("../../includes/db.inc.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'] ?? null;
  $status = $_POST['status'] ?? null;

  if ($id && in_array($status, ['Approved', 'Rejected'])) {
    $stmt = $pdo->prepare("UPDATE returns SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    echo "success";
  } else {
    echo "invalid";
  }
} else {
  echo "invalid";
}
