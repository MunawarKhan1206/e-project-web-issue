<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login_btn"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];
    try {
        require_once 'db.inc.php';
        // Find user in users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            // Optional: Fetch extra role-specific info
            /*
            if ($role === "employee") {
                $stmt = $pdo->prepare("SELECT department FROM employees WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $_SESSION['department'] = $stmt->fetchColumn();
            }
            */
            echo "<script>alert('Login successful!');
            window.location.href = '../dashboard.php';</script>";
            // header("Location: dashboard.php"); exit;
        } else {
            echo "<script>alert('Invalid email or password.');
            window.location.href = '../form/index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');
        window.location.href = '../form/index.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request method.');
    window.location.href = '../form/index.php';</script>";
}
?>
