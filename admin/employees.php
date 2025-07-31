<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}
require_once '../includes/db.inc.php';

// Fetch all employees
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'employee'");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Employees</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="products-style.css">
  <style>
    @media (max-width: 768px) {
      .table thead {
        display: none;
      }
      .table tbody tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 10px;
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      }
      .table tbody td {
        display: flex;
        justify-content: space-between;
        padding: 8px 10px;
        border: none;
        font-size: 14px;
      }
      .table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #555;
      }
    }
  </style>
</head>
<body>
<div class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar" id="adminSidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="returns.php"><i class="fas fa-undo"></i> Returns</a>
    <a href="employees.php" class="active"><i class="fas fa-user-tie"></i> Employees</a>
    <a href="../frontend/index.php"><i class="fas fa-store"></i> Shop</a>
    <a href="contact.php"><i class="fas fa-envelope"></i> Contact Messages</a>

    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <button id="menu-toggle" class="menu-toggle"><i class="fas fa-bars"></i></button>

    <div class="dashboard-title d-flex justify-content-between align-items-center">
      <span>Manage Employees</span>
      <button class="btn btn-dark" data-toggle="modal" data-target="#addEmployeeModal">
        <i class="fas fa-user-plus"></i> Add Employee
      </button>
    </div>

    <div class="bg-white p-3 shadow-sm rounded" style="overflow-x: auto;">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead  style="background: #f2ebe0; color: #5c4434;">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Registered</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($employees) > 0): ?>
              <?php foreach ($employees as $e): ?>
                <tr>
                  <td data-label="Name"><?= htmlspecialchars($e['name']) ?></td>
                  <td data-label="Email"><?= htmlspecialchars($e['email']) ?></td>
                  <td data-label="Registered"><?= date("d M Y", strtotime($e['created_at'])) ?></td>
                  <td data-label="Actions">
                    <form method="POST" action="delete_employee.php" onsubmit="return confirm('Are you sure?');">
                      <input type="hidden" name="id" value="<?= $e['id'] ?>">
                      <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center text-muted">No employees found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="add_employee.php" class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Add New Employee</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Name</label>
          <input required type="text" name="name" class="form-control">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input required type="email" name="email" class="form-control">
        </div>
        <div class="form-group">
          <label>Password</label>
          <input required type="password" name="password" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-dark w-100">Add Employee</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
  // GSAP Animations
  gsap.from(".sidebar", { x: -200, duration: 0.8, ease: "power2.out" });
  gsap.from(".main", { opacity: 0, y: 30, duration: 0.6, delay: 0.2 });
  gsap.from(".btn-add, .dashboard-title, .section", {
    y: 50,
    opacity: 0,
    stagger: 0.2,
    duration: 0.8,
    delay: 0.5,
    ease: "back.out(1.7)",
  });
</script>
</body>
</html>
