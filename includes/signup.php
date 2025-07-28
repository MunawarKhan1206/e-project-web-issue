<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register_btn"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    try {
        require_once 'db.inc.php';

        // Insert into main users table
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);

        $user_id = $pdo->lastInsertId(); // Get the new user's ID

        // Insert into role-specific table
        if ($role === "admin") {
            $pdo->prepare("INSERT INTO admins (user_id) VALUES (?)")->execute([$user_id]);
        } elseif ($role === "employee") {
            $pdo->prepare("INSERT INTO employees (user_id) VALUES (?)")->execute([$user_id]);
        } elseif ($role === "customer") {
            $pdo->prepare("INSERT INTO customers (user_id) VALUES (?)")->execute([$user_id]);
        }

        echo "<script>
            localStorage.setItem('toastMessage', 'Registration successful!');
            window.location.href = '../form/index.php';
        </script>";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            // Duplicate entry (email already exists)
            echo "<script>
                localStorage.setItem('toastMessage', 'Email already exists!');
                window.location.href = '../form/index.php';
            </script>";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
