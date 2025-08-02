<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);

// Absolute path to dashboard based on user role
$dashboardLink = '/e-project/frontend/index.php'; // default

if (isset($_SESSION['user_role'])) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            $dashboardLink = '/e-project/admin/dashboard.php';
            break;
        case 'customer':
            $dashboardLink = '/e-project/frontend/customer/customer_dashboard.php';
            break;
        case 'employee':
            $dashboardLink = '/e-project/frontend/employee/employee_dashboard.php';
            break;
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
	<div class="container">
		<a class="navbar-brand" href="/e-project/frontend/index.php">Minishop</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
			aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="oi oi-menu"></span> Menu
		</button>

		<div class="collapse navbar-collapse" id="ftco-nav">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item <?= $current_page == 'index.php' ? 'active' : '' ?>">
					<a href="/e-project/frontend/index.php" class="nav-link">Home</a>
				</li>
				<li class="nav-item <?= $current_page == 'shop.php' ? 'active' : '' ?>">
					<a href="/e-project/frontend/shop.php" class="nav-link">Shop</a>
				</li>
				<li class="nav-item <?= $current_page == 'about.php' ? 'active' : '' ?>">
					<a href="/e-project/frontend/about.php" class="nav-link">About</a>
				</li>
				<li class="nav-item <?= $current_page == 'contact.php' ? 'active' : '' ?>">
					<a href="/e-project/frontend/contact.php" class="nav-link">Contact</a>
				</li>
				<li class="nav-item <?= $current_page == 'trackorder.php' ? 'active' : '' ?>">
					<a href="/e-project/frontend/trackorder.php" class="nav-link">Track Order</a>
				</li>
				<li class="nav-item <?= strpos($current_page, 'dashboard') !== false ? 'active' : '' ?>">
	<a href="<?= isset($_SESSION['user_role']) ? $dashboardLink : '/e-project/form/index.php' ?>" class="nav-link">Dashboard</a>
</li>

                	<li class="nav-item cta cta-colored <?= $current_page == 'cart.php' ? 'active' : '' ?>">
					<a href="/e-project/frontend/cart.php" class="nav-link">
						<span class="icon-shopping_cart"></span>
						[<span class="cart-count">
							<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
						</span>]
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>
