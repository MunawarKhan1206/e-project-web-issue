<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}

require_once '../includes/db.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = trim($_POST['description']);

    $imageName = '';

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../frontend/product/";
        $imageName = time() . '_' . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowedTypes)) {
            die("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            die("Sorry, there was an error uploading the image.");
        }
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image, description) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$name, $category, $price, $stock, $imageName, $description]);

    if ($result) {
        header("Location: products.php?success=1");
        exit;
    } else {
        die("Something went wrong while adding product.");
    }
} else {
    header("Location: products.php");
    exit;
}
