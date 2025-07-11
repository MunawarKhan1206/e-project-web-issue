<?php
require_once '../db.inc.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_token = $data['id_token'];

$client = new \Google_Client(['client_id' => '547746583380-r0a21nabutv0jrurr3152ufnbqokvbo6.apps.googleusercontent.com']);
$payload = $client->verifyIdToken($id_token);

if ($payload) {
    $name = $payload['name'];
    $email = $payload['email'];

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, '', 'customer')");
        $stmt->execute([$name, $email]);
        $user_id = $pdo->lastInsertId();
        $pdo->prepare("INSERT INTO customers (user_id) VALUES (?)")->execute([$user_id]);
    }
    echo "Google Login successful!";
} else {
    echo "Invalid ID token.";
}
?>
