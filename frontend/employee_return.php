<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employee') {
    header("Location: ../form/index.php");
    exit;
}
require_once("../includes/db.inc.php");

$stmt = $pdo->query("SELECT id, order_number, reason, return_date, status FROM returns ORDER BY return_date DESC");
$returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Return Requests - Employee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .sidebar {
      min-height: 100vh;
    }
    .sidebar .nav-link.active {
      background-color: #c29958 !important;
      color: white !important;
    }
    .toast {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1055;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
      <div class="position-sticky pt-3">
        <h4 class="text-white px-3">Employee Panel</h4>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link text-white" href="employee_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="stock.php"><i class="fas fa-boxes me-2"></i>Stock</a></li>
          <li class="nav-item"><a class="nav-link text-white active" href="employee_return.php"><i class="fas fa-undo me-2"></i>Returns</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <button class="btn btn-outline-dark d-md-none mt-3 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
        <i class="fas fa-bars"></i>
      </button>
      <h2 class="my-4">Return Requests</h2>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>Order #</th>
              <th>Reason</th>
              <th>Return Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($returns as $ret): ?>
              <tr>
                <td><?= htmlspecialchars($ret['order_number']) ?></td>
                <td><?= htmlspecialchars($ret['reason']) ?></td>
                <td><?= date("M d, Y", strtotime($ret['return_date'])) ?></td>
                <td>
                  <?php if ($ret['status'] === 'Pending'): ?>
                    <select class="form-select status-select" data-id="<?= $ret['id'] ?>">
                      <option selected disabled>Pending</option>
                      <option value="Approved">Approve</option>
                      <option value="Rejected">Reject</option>
                    </select>
                  <?php else: ?>
                    <span class="badge bg-<?= $ret['status'] === 'Approved' ? 'success' : 'danger' ?>">
                      <?= $ret['status'] ?>
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<!-- Toast Alert -->
<div class="toast align-items-center text-bg-dark border-0" id="toast" role="alert">
  <div class="d-flex">
    <div class="toast-body" id="toast-msg">Status updated</div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll(".status-select").forEach(select => {
    select.addEventListener("change", function () {
      const returnId = this.dataset.id;
      const status = this.value;

      fetch("update_return.php", {
        method: "POST",
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${returnId}&status=${status}`
      })
      .then(res => res.text())
      .then(data => {
        if (data.trim() === "success") {
          showToast(`Return marked as ${status}`);
          setTimeout(() => location.reload(), 1500);
        } else {
          showToast("Failed to update status");
        }
      })
      .catch(() => showToast("Server error!"));
    });
  });

  function showToast(msg) {
    document.getElementById("toast-msg").textContent = msg;
    const toast = new bootstrap.Toast(document.getElementById('toast'));
    toast.show();
  }
</script>
</body>
</html>
