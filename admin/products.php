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
  </style>
</head>
<body>

<div class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar" id="adminSidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="products.php" class="active"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="returns.php"><i class="fas fa-undo"></i> Returns</a>
    <a href="employees.php"><i class="fas fa-user-tie"></i> Employees</a>
    <a href="../frontend/index.php"><i class="fas fa-store"></i> Shop</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main">
    <div class="dashboard-title d-flex justify-content-between align-items-center">
      <span>Manage Products</span>
      <button class="btn btn-add px-4 py-2" data-toggle="modal" data-target="#addProductModal">
        <i class="fas fa-plus"></i> Add Product
      </button>
    </div>

    <div class="section">
      <h3>Current Products</h3>
      <div class="bg-white p-4 shadow-sm rounded" style="overflow-x: auto;">
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
               <button class="btn btn-sm" style="background: #ffcf67; color: #333; border-radius: 6px;" data-toggle="modal"
        data-target="#editProductModal"
        data-id="<?= $p['id'] ?>"
        data-name="<?= htmlspecialchars($p['name']) ?>"
        data-category="<?= htmlspecialchars($p['category']) ?>"
        data-price="<?= $p['price'] ?>"
        data-stock="<?= $p['stock'] ?>"
        data-image="<?= $p['image'] ?>"
        data-description="<?= htmlspecialchars($p['description']) ?>">
  <i class="fas fa-pen"></i>
</button>

                  <button class="btn btn-sm" style="background: #ff6b6b; color: white; border-radius: 6px;"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="add_products.php" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">Add New Product</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body row">
        <div class="col-md-6">
          <label>Product Name</label>
          <input type="text" name="name" class="form-control" required>
          <label class="mt-3">Category</label>
          <input type="text" name="category" class="form-control" required>
          <label class="mt-3">Price</label>
          <input type="number" name="price" step="0.01" class="form-control" required>
          <label class="mt-3">Stock</label>
          <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label>Product Image</label>
          <input type="file" name="image" accept="image/*" class="form-control-file" required>
          <label class="mt-3">Description</label>
          <textarea name="description" rows="7" class="form-control"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn text-white" type="submit" name="add_product">Add Product</button>
      </div>
    </form>
  </div>
</div>
<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="edit_product.php" enctype="multipart/form-data">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Product</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body row">
        <input type="hidden" name="product_id" id="edit_id">
        <div class="col-md-6">
          <label>Product Name</label>
          <input type="text" name="name" id="edit_name" class="form-control" required>
          <label class="mt-3">Category</label>
          <input type="text" name="category" id="edit_category" class="form-control" required>
          <label class="mt-3">Price</label>
          <input type="number" name="price" id="edit_price" class="form-control" step="0.01" required>
          <label class="mt-3">Stock</label>
          <input type="number" name="stock" id="edit_stock" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label>Current Image</label><br>
          <img id="edit_image_preview" src="" alt="Preview" style="width: 80px; height: 80px; object-fit: cover;"><br>
          <label class="mt-2">Change Image</label>
          <input type="file" name="image" class="form-control-file">
          <label class="mt-3">Description</label>
          <textarea name="description" id="edit_description" rows="6" class="form-control"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit" name="update_product">Update Product</button>
      </div>
    </form>
  </div>
</div>


<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $('#toggleSidebar').click(function () {
    $('#adminSidebar').toggleClass('d-none');
  });
</script>
<?php if (isset($_GET['success'])): ?>
<script>
  setTimeout(() => {
    alert("✅ Product added successfully!");
  }, 300);
</script>
<?php endif; ?>
<script>
  $('#editProductModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#edit_id').val(button.data('id'));
    $('#edit_name').val(button.data('name'));
    $('#edit_category').val(button.data('category'));
    $('#edit_price').val(button.data('price'));
    $('#edit_stock').val(button.data('stock'));
    $('#edit_description').val(button.data('description'));
    $('#edit_image_preview').attr("src", "../frontend/product/" + button.data('image'));
  });
</script>

</body>
</html>
