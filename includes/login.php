<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login_btn"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        require_once 'db.inc.php';

        // Check user from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            // Set session variables
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];

            echo "<script>
                alert('Login successful!');
                window.location.href = '../frontend/index.php'; // redirect to home
            </script>";
        } else {
            echo "<script>
                alert('Invalid email or password.');
                window.location.href = '../form/index.php';
            </script>";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
