<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['customer', 'admin'])) {
    $showLoginModal = true;
} else {
    $showLoginModal = false;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>Minishop - Cart</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

	<link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
	<link rel="stylesheet" href="css/animate.css">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

	<link rel="stylesheet" href="css/owl.carousel.min.css">
	<link rel="stylesheet" href="css/owl.theme.default.min.css">
	<link rel="stylesheet" href="css/magnific-popup.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	<link rel="stylesheet" href="css/aos.css">

	<link rel="stylesheet" href="css/ionicons.min.css">

	<link rel="stylesheet" href="css/bootstrap-datepicker.css">
	<link rel="stylesheet" href="css/jquery.timepicker.css">


	<link rel="stylesheet" href="css/flaticon.css">
	<link rel="stylesheet" href="css/icomoon.css">
	<link rel="stylesheet" href="css/style.css">
	<style>
		/* Customize toastr container position */
#toast-container {
    top: 90px !important;
    right: 20px !important;
}

/* General toast style */
.toast {
    border-radius: 12px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    font-family: 'Open Sans', sans-serif;
    font-size: 14px;
    padding: 16px 20px;
    line-height: 1.4;
}

/* Success toast */
.toast-success {
    background-color: #738277ff !important;
    color: #0e4c2dff !important;
    border-left: 5px solid #27ae60;
}

/* Error toast */
.toast-error {
    background-color: #fdecea !important;
    color: #a94442 !important;
    border-left: 5px solid #e74c3c;
}

/* Info toast */
.toast-info {
    background-color: #e8f4fd !important;
    color: #31708f !important;
    border-left: 5px solid #3498db;
}

/* Warning toast */
.toast-warning {
    background-color: #fff8e5 !important;
    color: #8a6d3b !important;
    border-left: 5px solid #f39c12;
}
.modal-content {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 16px;
  color: #fff;
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
}
.modal.fade .modal-dialog {
  transition: transform 0.3s ease-out, opacity 0.3s ease-out;
  transform: scale(0.95);
  opacity: 0;
}
.modal.show .modal-dialog {
  transform: scale(1);
  opacity: 1;
}

	</style>
</head>
<?php
// session_start();
require_once("../includes/db.inc.php");
// session_start();

// Validate the action
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

if ($action) {
    switch ($action) {
        case 'remove':
            if ($id && isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
                $_SESSION['toast_success'] = "Item removed from cart.";
            }
            break;

        case 'clear':
            $_SESSION['cart'] = [];
            $_SESSION['toast_success'] = "Cart cleared.";
            break;
    }

    // Redirect to avoid repeating action on refresh
    header("Location: cart.php", true, 303);
    exit;
}

// ✅ No "else" needed — just let the page render





// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
	$_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$products = [];
$total = 0;
$deliveryFee = 200;
$discount = 0;

// Fetch product details from DB if cart is not empty
if (!empty($cart)) {
	$placeholders = implode(',', array_fill(0, count($cart), '?'));
	$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
	$stmt->execute(array_keys($cart));
	$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<body class="goto-here">
	<div class="py-1 bg-black">
		<div class="container">
			<div class="row no-gutters d-flex align-items-start align-items-center px-md-0">
				<div class="col-lg-12 d-block">
					<div class="row d-flex">
						<div class="col-md pr-4 d-flex topper align-items-center">
							<div class="icon mr-2 d-flex justify-content-center align-items-center"><span class="icon-phone2"></span></div>
							<span class="text">+ 1235 2355 98</span>
						</div>
						<div class="col-md pr-4 d-flex topper align-items-center">
							<div class="icon mr-2 d-flex justify-content-center align-items-center"><span class="icon-paper-plane"></span></div>
							<span class="text">minishop@email.com</span>
						</div>
						<div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right">
							<span class="text">3-5 Business days delivery &amp; Free Returns</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	 <?php
	include ("navbar.php");
   ?>
	<!-- END nav -->
<?php if (isset($showLoginModal) && $showLoginModal): ?>
<!-- Bootstrap Modal -->
<div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-labelledby="authModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white" id="authModalLabel">Please log in to access the cart.</h5>
      </div>
      <div class="modal-body text-white">
        Redirecting you to the login page in 4 seconds...
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

	<div class="hero-wrap hero-bread" style="background-image: url('images/bg_6.jpg');">
		<div class="container">
			<div class="row no-gutters slider-text align-items-center justify-content-center">
				<div class="col-md-9 ftco-animate text-center">
					<p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home</a></span> <span>Cart</span></p>
					<h1 class="mb-0 bread">My Wishlist</h1>
				</div>
			</div>
		</div>
	</div>

	<section class="ftco-section ftco-cart">
		<div class="container">
			<div class="row">
				<div class="col-md-12 ftco-animate">
					<div class="cart-list">
						        <div class="table-responsive d-none d-md-block">
            <table class="table table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $productId => $item) {
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt->execute([$productId]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($product) {
                            $subtotal = $product['price'] * $item['quantity'];
                            $total += $subtotal;
                    ?>
                    <tr>
                        <td class="image-prod">
							<div class="img" style="background-image:url('product/<?= htmlspecialchars($product['image']) ?>');"></div>
						</td>
					
	<td class="product-name">
		<h3><?= htmlspecialchars($product['name']) ?></h3>
		<p><?= htmlspecialchars($product['description']) ?></p>
	</td>
                        <td>Rs <?= $product['price'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>Rs <?= $subtotal ?></td>
                        <td>
                            <a href="remove-from-cart.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger">Remove</a>
                        </td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            <?php
            foreach ($_SESSION['cart'] as $productId => $item) {
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $subtotal = $product['price'] * $item['quantity'];
            ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <img src="frontend/product/<?= htmlspecialchars($product['image']) ?>" alt="Product Image" class="img-fluid mb-2" style="max-width: 100px;">
                    <p class="card-text mb-1"><strong>Price:</strong> Rs <?= $product['price'] ?></p>
                    <p class="card-text mb-1"><strong>Quantity:</strong> <?= $item['quantity'] ?></p>
                    <p class="card-text mb-1"><strong>Subtotal:</strong> Rs <?= $subtotal ?></p>
                    <a href="remove-from-cart.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger mt-2">Remove</a>
                </div>
            </div>
            <?php } } ?>
        </div>
					</div>
				</div>
			</div>
			<?php
			if ($total >= 6000) {
				$discount = 700;
			} elseif ($total >= 4000) {
				$discount = 500;
			} elseif ($total >= 2000) {
				$discount = 400;
			} else {
				$discount = 0;
			}

			// Final total
			$grandTotal = $total + $deliveryFee - $discount;
			
			if (!empty($products)) {
    $_SESSION['totals'] = [
        'subtotal' => $total,
        'discount' => $discount,
        'delivery' => $deliveryFee,
        'grand_total' => $grandTotal
    ];
}

			?>
			<div class="row justify-content-start">
				<div class="col col-lg-5 col-md-6 mt-5 cart-wrap ftco-animate">
					<div class="cart-total mb-3">
						<h3>Cart Totals</h3>
						<p class="d-flex">
							<span>Subtotal</span>
							<span>Rs <?= number_format($total) ?></span>
						</p>
						<p class="d-flex">
							<span>Delivery</span>
							<span>Rs <?= number_format($deliveryFee) ?></span>
						</p>
						<p class="d-flex">
							<span>Discount</span>
							<span>- Rs <?= number_format($discount) ?></span>
						</p>
						<hr>
						<p class="d-flex total-price">
							<span>Total</span>
							<span>Rs <?= number_format($grandTotal) ?></span>
						</p>
					</div>
<?php
				// Save totals in session for checkout.php

?>
					<?php if (!empty($products)): ?>
    <p class="text-center"><a href="checkout.php" class="btn btn-primary py-3 px-4">Proceed to Checkout</a></p>
<?php else: ?>
    <p class="text-center text-danger">Your cart is empty.</p>
<?php endif; ?>

				</div>
			</div>
		</div>
	</section>


<?php
	include ("footer.php");
	?>



	<!-- loader -->
	<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
			<circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
			<circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00" />
		</svg></div>


	<script src="js/jquery.min.js"></script>
	<script src="js/jquery-migrate-3.0.1.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.easing.1.3.js"></script>
	<script src="js/jquery.waypoints.min.js"></script>
	<script src="js/jquery.stellar.min.js"></script>
	<script src="js/owl.carousel.min.js"></script>
	<script src="js/jquery.magnific-popup.min.js"></script>
	<script src="js/aos.js"></script>
	<script src="js/jquery.animateNumber.min.js"></script>
	<script src="js/bootstrap-datepicker.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<script src="js/scrollax.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
	<script src="js/google-map.js"></script>
	<script src="js/main.js"></script>
<?php if ($showLoginModal): ?>
<script>
window.addEventListener("DOMContentLoaded", () => {
  $('#authModal').modal('show');
  setTimeout(() => {
    window.location.href = "../form/index.php";
  }, 4000);
});
</script>
<?php endif; ?>

	<script>
		$(document).ready(function() {

			var quantitiy = 0;
			$('.quantity-right-plus').click(function(e) {

				// Stop acting like a button
				e.preventDefault();
				// Get the field name
				var quantity = parseInt($('#quantity').val());

				// If is not undefined

				$('#quantity').val(quantity + 1);


				// Increment

			});

			$('.quantity-left-minus').click(function(e) {
				// Stop acting like a button
				e.preventDefault();
				// Get the field name
				var quantity = parseInt($('#quantity').val());

				// If is not undefined

				// Increment
				if (quantity > 0) {
					$('#quantity').val(quantity - 1);
				}
			});

		});
	</script>
<?php if (isset($_SESSION['toast_success'])): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			toastr.success("<?= $_SESSION['toast_success'] ?>");
		});
	</script>
	<?php unset($_SESSION['toast_success']); ?>
<?php endif; ?>

</body>

</html>