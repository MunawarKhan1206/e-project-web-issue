<?php
require_once '../db.inc.php';
require_once 'google-client.php'; // this file should contain Google_Client class

$data = json_decode(file_get_contents("php://input"), true);
$id_token = $data['id_token'] ?? '';

$client = new Google_Client(['client_id' => '547746583380-r0a21nabutv0jrurr3152ufnbqokvbo6.apps.googleusercontent.com']);
$payload = $client->verifyIdToken($id_token);

if ($payload) {
    $name = $payload['name'] ?? '';
    $email = $payload['email'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Register new user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, '', 'customer')");
        $stmt->execute([$name, $email]);
        $user_id = $pdo->lastInsertId();
        $pdo->prepare("INSERT INTO customers (user_id) VALUES (?)")->execute([$user_id]);
        echo "Signed up with Google!";
    } else {
        // Login existing user
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        echo "Logged in with Google!";
    }
} else {
    echo "Invalid token!";
}
