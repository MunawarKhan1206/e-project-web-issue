<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}
require_once '../includes/db.inc.php';

// Filter by status (optional)
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT * FROM returns";
$params = [];

if (!empty($statusFilter)) {
    $sql .= " WHERE status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY return_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Returns</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="products-style.css" />
</head>
<body>
<div class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar" id="adminSidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="returns.php" class="active"><i class="fas fa-undo"></i> Returns</a>
    <a href="employees.php"><i class="fas fa-user-tie"></i> Employees</a>
    <a href="../frontend/index.php"><i class="fas fa-store"></i> Shop</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="dashboard-title d-flex justify-content-between align-items-center">
      <span>Return Requests</span>
    </div>

    <!-- Filter -->
    <form method="GET" class="row mb-4">
      <div class="col-md-4">
        <select name="status" class="form-control">
          <option value="">All Status</option>
          <option <?= $statusFilter === 'pending' ? 'selected' : '' ?>>pending</option>
          <option <?= $statusFilter === 'approved' ? 'selected' : '' ?>>approved</option>
          <option <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>rejected</option>
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-dark w-100"><i class="fas fa-filter"></i> Filter</button>
      </div>
    </form>

    <div class="bg-white p-4 shadow-sm rounded" style="overflow-x:auto;">
      <table class="table table-bordered table-hover" style="min-width: 900px;">
        <thead class="thead-light">
          <tr>
            <th>#</th>
            <th>Order #</th>
            <th>Reason</th>
            <th>Return Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($returns) > 0): ?>
            <?php foreach ($returns as $r): ?>
              <tr id="return-row-<?= $r['id'] ?>">
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['order_number']) ?></td>
               <td>
  <button class="btn btn-sm btn-info view-reason-btn" 
          data-reason="<?= htmlspecialchars($r['reason']) ?>" 
          data-toggle="modal" 
          data-target="#viewReasonModal">
    View Reason
  </button>
</td>

                <td><?= date("d M Y", strtotime($r['return_date'])) ?></td>
                <td class="return-status">
                  <span class="badge badge-<?= 
                    $r['status'] === 'approved' ? 'success' : 
                    ($r['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                    <?= ucfirst($r['status']) ?>
                  </span>
                </td>
                <td class="return-action-cell">
                  <?php if ($r['status'] === 'pending'): ?>
                    <div class="d-flex">
                      <button class="btn btn-sm btn-success mr-1 return-action" data-id="<?= $r['id'] ?>" data-action="approve"><i class="fas fa-check"></i></button>
                      <button class="btn btn-sm btn-danger return-action" data-id="<?= $r['id'] ?>" data-action="reject"><i class="fas fa-times"></i></button>
                    </div>
                  <?php else: ?>
                    <i class="text-muted">—</i>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center text-muted">No return requests found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- View Reason Modal -->
<div class="modal fade" id="viewReasonModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Return Reason</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p id="modalReasonText" class="mb-0" style="white-space: pre-wrap;"></p>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function () {
    $('.return-action').click(function () {
      const button = $(this);
      const id = button.data('id');
      const action = button.data('action');
      const row = $('#return-row-' + id);

      $.post('update_return_status.php', { id: id, action: action }, function () {
        row.find('.return-status').html(`<span class="badge badge-${action === 'approve' ? 'success' : 'danger'}">${action}d</span>`);
        row.find('.return-action-cell').html('<i class="text-muted">—</i>');
      });
    });
  });$('.view-reason-btn').click(function () {
  const reason = $(this).data('reason');
  $('#modalReasonText').text(reason);
});

</script>
</body>
</html>
