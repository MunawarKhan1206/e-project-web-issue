<?php
session_start();
require_once 'db.inc.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_token = $data['id_token'] ?? '';

if (!$id_token) {
    echo json_encode(['success' => false, 'message' => 'Missing token']);
    exit;
}

// Decode token manually
$payload = explode('.', $id_token);
$userData = json_decode(base64_decode(strtr($payload[1], '-_', '+/')), true);

if (!isset($userData['email'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
    exit;
}

$email = $userData['email'];
$name = $userData['name'] ?? 'Google User';
$role = 'customer'; // default role if new user

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    // Register new user
    $stmt = $pdo->prepare("INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, '')");
    $stmt->execute([$name, $email, $role]);
    $user_id = $pdo->lastInsertId();

    // Insert into customers table
    $stmt = $pdo->prepare("INSERT INTO customers (user_id) VALUES (?)");
    $stmt->execute([$user_id]);

    // Fetch newly inserted user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

// Set session
$_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_email"] = $user["email"];
$_SESSION["user_role"] = $user["role"];

// Redirect based on role
$redirect = ($user["role"] === 'admin') ? "../admin/dashboard.php" : "../frontend/customer_dashboard.php";

echo json_encode([
    'success' => true,
    'redirect' => $redirect
]);
exit;
