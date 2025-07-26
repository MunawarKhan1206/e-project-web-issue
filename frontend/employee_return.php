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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Return Requests - Employee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
  <div class="row">
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

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <button class="btn btn-outline-dark d-md-none mt-3 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
      </button>
      <h2 class="my-4">Return Requests</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>Order #</th>
              <th>Reason</th>
              <th>Date</th>
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
                  <span class="badge bg-<?php
                    if ($ret['status'] === 'Approved') echo 'success';
                    elseif ($ret['status'] === 'Rejected') echo 'danger';
                    else echo 'warning';
                  ?>">
                    <?= ucfirst($ret['status']) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
