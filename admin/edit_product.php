<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}

require_once '../includes/db.inc.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_product"])) {
    $id = $_POST["product_id"];
    $name = $_POST["name"] ?? '';
    $category = $_POST["category"] ?? '';
    $price = $_POST["price"] ?? 0;
    $stock = $_POST["stock"] ?? 0;
    $description = $_POST["description"] ?? '';
    $onlyStock = isset($_POST["only_stock"]);

    // ✅ Only update stock
    if ($onlyStock) {
        $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->execute([$stock, $id]);
        header("Location: products.php?updated=1");
        exit;
    }

    // ✅ Full update (possibly with image)
    $image_name = $_FILES["image"]["name"] ?? '';
    $target_dir = "../frontend/product/";
    $target_file = $target_dir . basename($image_name);

    if (!empty($image_name)) {
        // Get old image from DB
        $oldStmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $oldStmt->execute([$id]);
        $oldImage = $oldStmt->fetchColumn();

        // Upload new image
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image
            if (!empty($oldImage) && file_exists($target_dir . $oldImage)) {
                unlink($target_dir . $oldImage);
            }

            // Update all fields including image
            $sql = "UPDATE products SET name = ?, category = ?, price = ?, stock = ?, image = ?, description = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $category, $price, $stock, $image_name, $description, $id]);
        }
    } else {
        // Update all fields except image
        $sql = "UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $category, $price, $stock, $description, $id]);
    }

    header("Location: products.php?updated=1");
    exit;
} else {
    header("Location: products.php");
    exit;
}
?>
