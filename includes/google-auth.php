<?php
require_once '../includes/db.inc.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_token = $data['id_token'] ?? '';

if (!$id_token) {
    echo "No token received!";
    exit;
}

// Verify token via Google's tokeninfo endpoint
$verify_url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($id_token);
$response = file_get_contents($verify_url);
$payload = json_decode($response, true);

// Check if payload has required fields
if (isset($payload['email']) && $payload['aud'] === '547746583380-r0a21nabutv0jrurr3152ufnbqokvbo6.apps.googleusercontent.com') {
    $name = $payload['name'] ?? '';
    $email = $payload['email'];

    // Check if user already exists
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
echo "success"; // just a success message
exit;


    }
} else {
    echo "Invalid token or client ID mismatch!";
}
