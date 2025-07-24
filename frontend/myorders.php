<?php
session_start();
require_once '../includes/db.inc.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../form/index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Orders</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styling -->
    <style>
        body {
            background: #f3f4f6;
            font-family: 'Segoe UI', sans-serif;
        }
        .card-gradient {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border: none;
            border-radius: 15px;
        }
        .table thead th {
            background-color: #343a40;
            color: #fff;
            border: none;
        }
        .table td, .table th {
            vertical-align: middle !important;
        }
        .badge {
            font-size: 0.85rem;
            padding: 0.45em 0.75em;
            border-radius: 12px;
            transition: 0.3s ease;
        }
        .badge-warning { background-color: #f6ad55; }
        .badge-primary { background-color: #4299e1; }
        .badge-success { background-color: #48bb78; }
        .badge-info { background-color: #63b3ed; }
        .badge-danger { background-color: #f56565; }
        .table-hover tbody tr:hover {
            background-color: #f1f5f9;
        }
        .btn-outline-info {
            transition: all 0.3s ease;
        }
        .btn-outline-info:hover {
            background-color: #3182ce;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card card-gradient shadow-lg mb-4 p-4">
        <h2 class="mb-0"><i class="fas fa-box-open mr-2"></i> My Orders</h2>
    </div>

    <?php if ($orders): ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered table-hover bg-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order Number</th>
                        <th>Tracking ID</th>
                        <th>Date</th>
                        <th>Total (PKR)</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $index => $order): ?>
                    <?php
                    $status = $order['status'];
                    $badge = 'secondary';
                    if ($status === 'Pending') $badge = 'warning';
                    elseif ($status === 'Confirmed') $badge = 'primary';
                    elseif ($status === 'Shipped') $badge = 'info';
                    elseif ($status === 'Delivered') $badge = 'success';
                    elseif ($status === 'Cancelled') $badge = 'danger';
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                        <td><?= htmlspecialchars($order['tracking_id']) ?></td>
                        <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                        <td><strong>PKR <?= number_format($order['grand_total'], 0) ?></strong></td>
                        <td><span class="badge badge-<?= $badge ?>"><?= $status ?></span></td>
                        <td>
                            <a href="order_details.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info shadow-sm">
            <i class="fas fa-info-circle"></i> You haven’t placed any orders yet.
        </div>
    <?php endif; ?>
</div>

</body>
</html>
