<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
	<div class="container">
		<a class="navbar-brand" href="index.php">Minishop</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
			aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="oi oi-menu"></span> Menu
		</button>
		<div class="collapse navbar-collapse" id="ftco-nav">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item <?= $current_page == 'index.php' ? 'active' : '' ?>">
					<a href="index.php" class="nav-link">Home</a>
				</li>
				<li class="nav-item <?= $current_page == 'shop.php' ? 'active' : '' ?>">
					<a href="shop.php" class="nav-link">Shop</a>
				</li>
				<li class="nav-item <?= $current_page == 'about.php' ? 'active' : '' ?>">
					<a href="about.php" class="nav-link">About</a>
				</li>
				<li class="nav-item <?= $current_page == 'contact.php' ? 'active' : '' ?>">
					<a href="contact.php" class="nav-link">Contact</a>
				</li>
				<li class="nav-item <?= $current_page == 'trackorder.php' ? 'active' : '' ?>">
					<a href="trackorder.php" class="nav-link">Track Order</a>
				</li>
				<li class="nav-item cta cta-colored <?= $current_page == 'cart.php' ? 'active' : '' ?>">
					<a href="cart.php" class="nav-link">
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
