<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}

require_once '../includes/db.inc.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_product"])) {
    $id = $_POST["product_id"];
    $name = $_POST["name"];
    $category = $_POST["category"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = $_POST["description"];

    // Check if image is uploaded
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../frontend/product/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Optional: validate image type
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed)) {
            die("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Move new image
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Update with new image
            $sql = "UPDATE products SET name = ?, category = ?, price = ?, stock = ?, image = ?, description = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $category, $price, $stock, $image_name, $description, $id]);
        } else {
            die("Failed to upload new image.");
        }
    } else {
        // Update without changing image
        $sql = "UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $category, $price, $stock, $description, $id]);
    }

    // Redirect back with success
    header("Location: products.php?updated=1");
    exit;
} else {
    header("Location: products.php");
    exit;
}
?>
