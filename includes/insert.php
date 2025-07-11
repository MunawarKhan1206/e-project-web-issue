<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    try{
        require_once '../include/db.inc.php';
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
        echo "User registered successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}