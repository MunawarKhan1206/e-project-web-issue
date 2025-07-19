<?php
session_start();
require_once '../../includes/db.inc.php'; // PDO connection

// if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
//   header('Location: ../index.php');
//   exit;
// }

// $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
// $orderCount   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
// $userCount    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
// $adminCount   = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'admin'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease-in-out;
    }
    body {
      background: #f5f1e9;
      margin: 0;
    }
    .sidebar {
      background: #3c2f26;
      width: 230px;
      height: 100vh;
      position: fixed;
      padding-top: 40px;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }
    .sidebar h4 {
      color: #fff;
      text-align: center;
      margin-bottom: 30px;
    }
    .sidebar a {
      display: block;
      color: #f5f1e9;
      padding: 12px 30px;
      text-decoration: none;
      font-weight: 500;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #c29958;
      color: #fff;
      border-radius: 0 30px 30px 0;
    }
    .main-content {
      margin-left: 230px;
      padding: 40px;
    }
    .dashboard-title {
      font-size: 28px;
      color: #4d3a24;
      margin-bottom: 30px;
      font-weight: 600;
    }
    .card-box {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.06);
      padding: 25px 30px;
      border-left: 5px solid #c29958;
      transition: transform 0.3s ease;
    }
    .card-box:hover {
      transform: translateY(-5px);
    }
    .card-box h4 {
      font-size: 18px;
      color: #5c4434;
    }
    .card-box h2 {
      font-size: 30px;
      font-weight: 600;
      color: #c29958;
    }
    .row.g-4 > div {
      padding: 15px;
    }
    .section {
      margin-top: 40px;
    }
    .section h5 {
      margin-bottom: 20px;
      font-weight: 600;
      color: #4d3a24;
    }
    .table th {
      background-color: #e9e1d4;
      color: #4d3a24;
    }
    .table td, .table th {
      vertical-align: middle;
    }
    .btn-view {
      background: #c29958;
      color: #fff;
      border: none;
      padding: 5px 15px;
      border-radius: 6px;
    }
    .btn-view:hover {
      background: #a8823e;
    }
    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 20px;
      }
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        padding-bottom: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h4>Admin Panel</h4>
    <a href="dashboard.php" class="active">&#128200; Dashboard</a>
    <a href="products.php">&#128230; Products</a>
    <a href="orders.php">&#128179; Orders</a>
    <a href="customers.php">&#128101; Customers</a>
    <a href="logout.php">&#128682; Logout</a>
  </div>

  <div class="main-content">
    <div class="dashboard-title">Welcome, Admin 👋</div>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="card-box">
          <h4>Total Products</h4>
          <h2><?= $productCount ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-box">
          <h4>Total Orders</h4>
          <h2><?= $orderCount ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-box">
          <h4>Total Users</h4>
          <h2><?= $userCount ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-box">
          <h4>Total Admins</h4>
          <h2><?= $adminCount ?></h2>
        </div>
      </div>
    </div>

    <div class="section">
      <h5>Recent Orders</h5>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1001</td>
            <td>Ali Raza</td>
            <td>Rs. 2500</td>
            <td><span class="badge badge-success">Completed</span></td>
            <td><button class="btn-view">View</button></td>
          </tr>
          <tr>
            <td>1002</td>
            <td>Sana Malik</td>
            <td>Rs. 1700</td>
            <td><span class="badge badge-warning">Pending</span></td>
            <td><button class="btn-view">View</button></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="section">
      <h5>Top Products</h5>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>01</td>
            <td>Gift Wrap Box</td>
            <td>Stationery</td>
            <td>35</td>
            <td>Rs. 350</td>
          </tr>
          <tr>
            <td>02</td>
            <td>Premium Pen Set</td>
            <td>Accessories</td>
            <td>12</td>
            <td>Rs. 1250</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
