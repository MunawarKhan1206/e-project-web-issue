<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employee') {
  header("Location: ../form/index.php");
  exit;
}
require_once("../../includes/db.inc.php");


$stmt = $pdo->query("SELECT id, order_number, reason, return_date, status FROM returns ORDER BY return_date DESC");
$returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Return Requests - Employee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="customer.css">

  <style>
    /* Mobile cards for returns */
.card-return {
  background: #fffdf9; /* match beige/white table background */
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.05);
  padding: 20px;
  margin-bottom: 20px;
  font-size: 15px;
  color: #333;
}

.card-return .card-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
}

.card-return .label {
  font-weight: 600;
  color: #5c4434; /* dark beige text */
}

.card-return select.form-select {
  font-size: 14px;
  color: #5a3e1b;
  border-color: #c6a87d;
  border-radius: 8px;
  padding: 5px 10px;
}

.card-return select.form-select:focus {
  border-color: #c29958;
  box-shadow: 0 0 6px #c29958aa;
  outline: none;
}

  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="sidebar" id="sidebar">
        <h2>Employee Panel</h2>
        <a href="employee_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="stock.php"><i class="fas fa-boxes"></i> Stock</a>
        <a href="employee_return.php" class="active"><i class="fas fa-undo"></i> Returns</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
      <!-- Main Content -->
     <!-- Main Content -->
<main class="main">
 <button class="menu-toggle btn btn-sm btn-warning d-md-none" onclick="document.getElementById('sidebar').classList.toggle('active')">
        <i class="fas fa-bars"></i>
      </button>
  <h2 class="dashboard-title">Return Requests</h2>

  <!-- Desktop Table -->
  <div class="table-responsive d-none d-md-block">
    <table class="custom-table table table-striped">
      <thead class="custom-thead">
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
              <?php else: 
                $badgeClass = ($ret['status'] === 'Approved') ? 'success' : 'pending'; ?>
                <span class="badge <?= $badgeClass ?>"><?= $ret['status'] ?></span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Mobile Cards -->
  <div class="d-md-none">
    <?php foreach ($returns as $ret): ?>
      <div class="card-return">
        <div class="card-row"><span class="label">Order #:</span> <span><?= htmlspecialchars($ret['order_number']) ?></span></div>
        <div class="card-row"><span class="label">Reason:</span> <span><?= htmlspecialchars($ret['reason']) ?></span></div>
        <div class="card-row"><span class="label">Return Date:</span> <span><?= date("M d, Y", strtotime($ret['return_date'])) ?></span></div>
        <div class="card-row">
          <span class="label">Status:</span>
          <span>
            <?php if ($ret['status'] === 'Pending'): ?>
              <select class="form-select status-select" data-id="<?= $ret['id'] ?>" style="max-width: 180px;">
                <option selected disabled>Pending</option>
                <option value="Approved">Approve</option>
                <option value="Rejected">Reject</option>
              </select>
            <?php else: 
              $badgeClass = ($ret['status'] === 'Approved') ? 'success' : 'pending'; ?>
              <span class="badge <?= $badgeClass ?>"><?= $ret['status'] ?></span>
            <?php endif; ?>
          </span>
        </div>
      </div>
    <?php endforeach; ?>
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
    document.querySelectorAll(".status-select").forEach(select => {
      select.addEventListener("change", function() {
        const returnId = this.dataset.id;
        const status = this.value;

        fetch("update_return.php", {
            method: "POST",
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
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