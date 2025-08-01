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
  <!-- <link rel="stylesheet" href="admin.css"> -->
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


<div class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar">
            <h2 style="    display: block;
    font-size: 1.5em !important;
    /* margin-block-start: 0.83em; */
    /* margin-block-end: 0.83em; */
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    font-weight: bold;
    unicode-bidi: isolate;
    height: 28px !important;">Admin Panel</h2>
            <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="products.php" class="active"><i class="fas fa-box"></i> Products</a>
            <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="returns.php"><i class="fas fa-undo-alt"></i> Returns</a>
            <a href="employees.php"><i class="fas fa-user-tie"></i> Employees</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            <a href="notification.php" ><i class="fas fa-bell"></i> Notifications</a>
            <a href="../frontend/index.php"><i class="fas fa-store"></i> Go to Shop</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

  <!-- Main Content -->
  <div class="main">
   <button id="menu-toggle" class="menu-toggle"><i class="fas fa-bars"></i>
</button>


    <div class="dashboard-title d-flex justify-content-between align-items-center">
      <span>Manage Products</span>
      <button class="btn btn-add px-4 py-2" data-toggle="modal" data-target="#addProductModal">
        <i class="fas fa-plus"></i> Add Product
      </button>
    </div>

   <div class="section">
  <h3>Current Products</h3>

  <!-- Desktop Table -->
  <div class="table-responsive d-none d-md-block">
    <table class="table table-bordered table-hover">
      <thead style="background: #f2ebe0; color: #5c4434;">
        <tr>
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
            <td>
              <?php if (!empty($p['image']) && file_exists("../frontend/product/" . $p['image'])): ?>
                <img src="../frontend/product/<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" />
              <?php else: ?>
                <span class="text-muted">N/A</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td>Rs. <?= number_format($p['price'], 2) ?></td>
            <td><?= $p['stock'] ?></td>
            
            <td>
              <button class="btn btn-sm" style="background: #ffcf67; color: #333; border-radius: 6px;"
                data-toggle="modal"
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
              <button class="btn btn-sm" style="background: #ff6b6b; color: white; border-radius: 6px;"
                data-toggle="modal" data-target="#deleteProductModal"
                data-id="<?= $p['id'] ?>"
                data-name="<?= htmlspecialchars($p['name']) ?>">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Mobile Cards -->
  <div class="d-block d-md-none">
    <?php foreach ($products as $p): ?>
      <div class="product-card mb-4 p-3 bg-white rounded shadow-sm">
        <div class="d-flex align-items-center">
          <?php if (!empty($p['image']) && file_exists("../frontend/product/" . $p['image'])): ?>
            <img src="../frontend/product/<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"/>
          <?php else: ?>
            <div style="width: 80px; height: 80px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #aaa;">N/A</div>
          <?php endif; ?>
          <div class="ms-3 flex-grow-1">
            <h5 style="color: #3a2f27; font-weight: 600; margin-bottom: 6px;"><?= htmlspecialchars($p['name']) ?></h5>
            <p style="margin: 0; font-size: 0.9rem; color: #5c4434;"><?= htmlspecialchars($p['category']) ?></p>
            <p style="margin: 0; font-weight: 700; color: #c29958;">Rs. <?= number_format($p['price'], 2) ?></p>
            <p style="margin: 0; font-size: 0.9rem;">Stock: <strong><?= $p['stock'] ?></strong></p>
          </div>
        </div>
        <div class="mt-3 d-flex gap-2 justify-content-end">
          <button class="btn btn-sm" style="background: #ffcf67; color: #333; border-radius: 6px;"
            data-toggle="modal"
            data-target="#editProductModal"
            data-id="<?= $p['id'] ?>"
            data-name="<?= htmlspecialchars($p['name']) ?>"
            data-category="<?= htmlspecialchars($p['category']) ?>"
            data-price="<?= $p['price'] ?>"
            data-stock="<?= $p['stock'] ?>"
            data-image="<?= $p['image'] ?>"
            data-description="<?= htmlspecialchars($p['description']) ?>">
            <i class="fas fa-pen"></i> Edit
          </button>
          <button class="btn btn-sm" style="background: #ff6b6b; color: white; border-radius: 6px;"
            data-toggle="modal" data-target="#deleteProductModal"
            data-id="<?= $p['id'] ?>"
            data-name="<?= htmlspecialchars($p['name']) ?>">
            <i class="fas fa-trash"></i> Delete
          </button>
        </div>
      </div>
    <?php endforeach; ?>
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

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="delete_product.php">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Delete Product</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete <strong id="delete_product_name">this product</strong>?
        <input type="hidden" name="delete_id" id="delete_product_id">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" type="submit" name="delete_product">Yes, Delete</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
  // GSAP
  gsap.from(".sidebar", {x: -200, duration: 0.8, ease: "power2.out"});
  gsap.from(".main", {opacity: 0, y: 30, duration: 0.6, delay: 0.2});
  gsap.from(".stat-card", {y: 50, opacity: 0, stagger: 0.2, duration: 0.8, delay: 0.5, ease: "back.out(1.7)"});

  // Chart
  const ctx = document.getElementById('ordersChart').getContext('2d');
  const ordersChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [
        {
          label: 'Orders',
          data: <?= json_encode($monthly_orders) ?>,
          backgroundColor: '#3b82f6'
        },
        {
          label: 'Returns',
          data: <?= json_encode($monthly_returns) ?>,
          backgroundColor: '#f59e0b'
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
</script>

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
  $('#deleteProductModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var productId = button.data('id');
  var productName = button.data('name');
  $('#delete_product_id').val(productId);
  $('#delete_product_name').text(productName);
});
</script>
<!-- <script>
  const toggle = document.getElementById('menu-toggle');
  const sidebar = document.querySelector('.sidebar');

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });

  // Close sidebar if clicked outside on small screens
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
      const isClickInsideSidebar = sidebar.contains(e.target);
      const isClickOnToggle = toggle.contains(e.target);

      if (!isClickInsideSidebar && !isClickOnToggle) {
        sidebar.classList.remove('active');
      }
    }
  });
</script> -->
<script src="menu.js"></script>



</body>
</html>
