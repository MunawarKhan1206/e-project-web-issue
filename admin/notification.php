<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../form/index.php");
    exit;
}
require_once '../includes/db.inc.php';

// Handle new notification submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_notice'])) {
    $title = $_POST['title'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO site_notices (title, message, active, created_at) VALUES (?, ?, 1, NOW())");
    $stmt->execute([$title, $message]);

    header("Location: notification.php?success=1");
    exit;
}

// Handle toggle (active/inactive)
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = $_GET['toggle'];

    $stmt = $pdo->prepare("UPDATE site_notices SET active = NOT active WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: notification.php");
    exit;
}

// Fetch all notices
$notices = $pdo->query("SELECT * FROM site_notices ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin - Notifications</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Reuse your existing styles -->
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
</head>

<body>
    <div class="dashboard">
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
            <a href="products.php"><i class="fas fa-box"></i> Products</a>
            <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="returns.php"><i class="fas fa-undo-alt"></i> Returns</a>
            <a href="employees.php"><i class="fas fa-user-tie"></i> Employees</a>
            <a href="customers.php"><i class="fas fa-user-tie"></i> Customers</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            <a href="notification.php" class="active"><i class="fas fa-bell"></i> Notifications</a>
            <a href="../frontend/index.php"><i class="fas fa-store"></i> Go to Shop</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="main">
            <button id="menu-toggle" class="menu-toggle"><i class="fas fa-bars"></i></button>
            <h2 class="mb-4">Manage Notifications</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Notification added successfully!</div>
            <?php endif; ?>

            <!-- Add New Notification -->
            <form method="POST" class="mb-4">
                <div class="form-group">
                    <label><strong>Title</strong></label>
                    <input type="text" name="title" class="form-control" placeholder="Enter title" required>
                </div>
                <div class="form-group">
                    <label><strong>Message</strong></label>
                    <textarea name="message" class="form-control" rows="3" placeholder="Enter message" required></textarea>
                </div>
                <button type="submit" name="submit_notice" class="btn btn-dark">Add Notification</button>
            </form>

            <!-- Existing Notices Table -->
            <!-- Existing Notices Table -->
            <div class="table-responsive d-none d-lg-block"> <!-- Hide on phones -->
                <table class="table table-bordered table-striped custom-table">
                    <thead thead style="background: #f2ebe0; color: #5c4434;">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notices as $index => $notice): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($notice['title']) ?></td>
                                <td><?= htmlspecialchars($notice['message']) ?></td>
                                <td>
                                    <?php if ($notice['active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date("d M Y h:i A", strtotime($notice['created_at'])) ?></td>
                                <td>
                                    <a href="?toggle=<?= $notice['id'] ?>" class="btn btn-sm btn-warning">
                                        <?= $notice['active'] ? 'Deactivate' : 'Activate' ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($notices)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No notifications yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Mobile Card View -->
            <div class="d-lg-none">
                <?php foreach ($notices as $index => $notice): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($notice['title']) ?></h5>
                            <p class="card-text mb-2"><?= htmlspecialchars($notice['message']) ?></p>
                            <p class="card-text">
                                <strong>Status:</strong>
                                <?php if ($notice['active']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactive</span>
                                <?php endif; ?>
                            </p>
                            <p class="card-text text-muted mb-2">
                                <strong>Created:</strong> <?= date("d M Y h:i A", strtotime($notice['created_at'])) ?>
                            </p>
                            <a href="?toggle=<?= $notice['id'] ?>" class="btn btn-sm btn-warning">
                                <?= $notice['active'] ? 'Deactivate' : 'Activate' ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($notices)): ?>
                    <p class="text-muted text-center">No notifications yet.</p>
                <?php endif; ?>
            </div>


        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        // GSAP
        gsap.from(".sidebar", {
            x: -200,
            duration: 0.8,
            ease: "power2.out"
        });
        gsap.from(".main", {
            opacity: 0,
            y: 30,
            duration: 0.6,
            delay: 0.2
        });
        gsap.from(".stat-card", {
            y: 50,
            opacity: 0,
            stagger: 0.2,
            duration: 0.8,
            delay: 0.5,
            ease: "back.out(1.7)"
        });
    </script>
    <script>
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
    </script>

</body>

</html>