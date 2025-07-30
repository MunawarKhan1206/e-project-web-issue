<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}
require_once '../includes/db.inc.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin - Products</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="products-style.css" />
  <style>
    .btn-add {
      border-radius: 8px;
      background: #c29958;
      color: white;
      font-weight: 600;
    }
    .btn-add:hover {
      background: #a7803f;
    }
    .modal-header {
      background: #3a2f27;
      color: white;
    }
    .modal-footer button {
      background: #c29958;
      border: none;
    }
    .modal-footer button:hover {
      background: #a87b35;
    }
    @media (max-width: 768px) {
      .sidebar {
        position: absolute;
        left: -240px;
        transition: all 0.3s ease;
        z-index: 1000;
      }
      .sidebar.show {
        left: 0;
      }
      .main {
        margin-left: 0;
        width: 100%;
        padding: 20px;
      }
    }
  </style>
</head>
<body>
<?php if (isset($_GET['updated'])): ?>
  <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert" style="z-index: 9999;">
    <strong>✅ Product updated successfully!</strong>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
  <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert" style="z-index: 9999;">
    <strong>🗑️ Product deleted successfully!</strong>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
<?php endif; ?>


<div class="dashboard d-flex">
  <!-- Sidebar -->
  <div class="sidebar bg-dark text-white p-4" id="adminSidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php" class="text-white d-block mb-2"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="products.php" class="active text-white d-block mb-2"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php" class="text-white d-block mb-2"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="returns.php" class="text-white d-block mb-2"><i class="fas fa-undo"></i> Returns</a>
    <a href="employees.php" class="text-white d-block mb-2"><i class="fas fa-user-tie"></i> Employees</a>
    <a href="../frontend/index.php" class="text-white d-block mb-2"><i class="fas fa-store"></i> Shop</a>
    <a href="../logout.php" class="text-white d-block mb-2"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main flex-grow-1 p-3">
    <button id="toggleSidebar" class="btn btn-sm btn-dark d-md-none mb-3">
      <i class="fas fa-bars"></i> Menu
    </button>

    <div class="dashboard-title d-flex justify-content-between align-items-center">
      <span>Manage Products</span>
      <button class="btn btn-add px-4 py-2" data-toggle="modal" data-target="#addProductModal">
        <i class="fas fa-plus"></i> Add Product
      </button>
    </div>

    <div class="section mt-4">
      <h3>Current Products</h3>
      <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-bordered table-hover" style="min-width: 900px;">
          <thead style="background: #f2ebe0; color: #5c4434;">
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price (Rs)</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
              <tr>
                <td><?= $p['id'] ?></td>
                <td>
                  <?php if (!empty($p['image']) && file_exists("../frontend/product/" . $p['image'])): ?>
                    <img src="../frontend/product/<?= $p['image'] ?>" style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover;" />
                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['category']) ?></td>
                <td>Rs. <?= number_format($p['price'], 2) ?></td>
                <td><?= $p['stock'] ?></td>
                <td>
                  <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editProductModal"
                    data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>"
                    data-category="<?= htmlspecialchars($p['category']) ?>" data-price="<?= $p['price'] ?>"
                    data-stock="<?= $p['stock'] ?>" data-image="<?= $p['image'] ?>"
                    data-description="<?= htmlspecialchars($p['description']) ?>">
                    <i class="fas fa-pen"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteProductModal"
                    data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Sidebar toggle script -->
<script>
  document.getElementById("toggleSidebar").addEventListener("click", function () {
    document.getElementById("adminSidebar").classList.toggle("show");
  });
</script>
</body>
</html>