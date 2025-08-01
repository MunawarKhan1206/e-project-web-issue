<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employee') {
    header("Location: ../form/index.php");
    exit;
}
require_once("../../includes/db.inc.php");


$stmt = $pdo->query("SELECT id, name, price, stock, image, created_at FROM products ORDER BY name ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Stock Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="customer.css"> <!-- Shared dashboard styling -->
  <style>
    .order-card img {
      max-width: 70px;
      height: auto;
    }
    .stock-toggle {
      width: 50px;
      height: 24px;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="sidebar" id="sidebar">
      <h2>Employee Panel</h2>
      <a href="employee_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
      <a href="stock.php" class="active"><i class="fas fa-boxes"></i> Stock</a>
      <a href="employee_return.php"><i class="fas fa-undo"></i> Returns</a>
      <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
<div class="main col-md-9 ms-sm-auto col-lg-10 px-md-4">

    <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
      ☰
</button>

      <h2 class="my-4">Stock Overview</h2>

      <!-- Desktop View -->
      <div class="table-responsive d-none d-md-block">
        <table class="table custom-table table-striped align-middle">
          <thead class="custom-thead">
            <tr>
              <th>Image</th>
              <th>Product</th>
              <th>Price (Rs)</th>
              <th>Stock</th>
              <th>Status</th>
              <th>Added On</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td><img src="product/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="60"></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= number_format($product['price'], 2) ?></td>
                <td>
                  <span class="badge <?= $product['stock'] < 5 ? 'bg-danger' : 'bg-success' ?>">
                    <?= $product['stock'] ?> pcs
                  </span>
                </td>
                <td>
  <div class="form-check form-switch">
    <input class="form-check-input stock-toggle" type="checkbox"
      data-id="<?= $product['id'] ?>" <?= $product['stock'] > 0 ? 'checked' : '' ?>
      <?= $product['stock'] == 0 ? 'disabled' : '' ?>>
  </div>
</td>

                <td><?= date('M d, Y', strtotime($product['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      
      <!-- Mobile View -->
      <div class="d-md-none">
        <?php foreach ($products as $product): ?>
          <div class="order-card mb-3 p-3 border rounded bg-light">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <strong><?= htmlspecialchars($product['name']) ?></strong>
              <span class="badge <?= $product['stock'] < 5 ? 'bg-danger' : 'bg-success' ?>">
                <?= $product['stock'] ?> pcs
              </span>
            </div>
            <div class="mb-2 text-center">
              <img src="product/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded" style="max-height: 100px;">
            </div>
            <p class="mb-1"><strong>Price:</strong> Rs. <?= number_format($product['price'], 2) ?></p>
            <p class="mb-1"><strong>Added:</strong> <?= date('M d, Y', strtotime($product['created_at'])) ?></p>
            <div class="form-check form-switch mt-2">
  <input class="form-check-input stock-toggle" type="checkbox"
    data-id="<?= $product['id'] ?>" <?= $product['stock'] > 0 ? 'checked' : '' ?>
    <?= $product['stock'] == 0 ? 'disabled' : '' ?>>
  <label class="form-check-label">Stock <?= $product['stock'] > 0 ? 'Available' : 'Out' ?></label>
</div>

          </div>
          <?php endforeach; ?>
        </div>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
  <div id="toast" class="toast align-items-center text-white bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body">Stock status updated!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
  gsap.from(".sidebar", {x: -200, duration: 0.8, ease: "power2.out"});
  gsap.from(".main", {opacity: 0, y: 30, duration: 0.6, delay: 0.2});
  gsap.from("table tbody tr", {
    opacity: 0,
    y: 20,
    duration: 0.5,
    stagger: 0.1,
    delay: 0.4
  });
</script>
<script>
  document.querySelectorAll('.stock-toggle').forEach(toggle => {
    toggle.addEventListener('change', function () {
      const productId = this.dataset.id;
      const status = this.checked ? 1 : 0;

      fetch('update_stock.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${productId}&status=${status}`
      })
      .then(res => res.text())
      .then(() => {
        const toast = new bootstrap.Toast(document.getElementById('toast'));
        toast.show();
      });
    });
  });
</script>
  <script>
    document.addEventListener("click", function (e) {
      const sidebar = document.querySelector(".sidebar");
      const toggle = document.querySelector(".menu-toggle");
      if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove("active");
      }
    });
  </script>

</body>
</html>
