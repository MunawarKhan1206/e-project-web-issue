<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    echo "<script>
        alert('Please log in as a customer to continue.');
        window.location.href = 'form/index.php';
    </script>";
    exit;
}

require_once("../includes/db.inc.php");
if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as &$product) {
        $id = $product['id'];
        $quantity = $cart[$id]['quantity'];
        $subtotal = $product['price'] * $quantity;
        $product['quantity'] = $quantity;
        $product['subtotal'] = $subtotal;
        $total += $subtotal;
    }

    if ($total > 2000) {
        $discount = 300;
    }

    $grandTotal = $total + $deliveryFee - $discount;
}

// session_start();


$subtotal = $_SESSION['totals']['subtotal'] ?? 0;
$discount = $_SESSION['totals']['discount'] ?? 0;
$delivery = $_SESSION['totals']['delivery'] ?? 0;
$grand_total = $_SESSION['totals']['grand_total'] ?? 0;




?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Minishop - Free Bootstrap 4 Template by Colorlib</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">

    
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
      .border-danger {
    border: 2px solid #dc3545 !important;
}

    </style>
  </head>
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
						    <span class="text">youremail@email.com</span>
					    </div>
					    <div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right">
						    <span class="text">3-5 Business days delivery &amp; Free Returns</span>
					    </div>
				    </div>
			    </div>
		    </div>
		  </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="index.html">Minishop</a>
	      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> Menu
	      </button>

	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav ml-auto">
	          <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
	          <li class="nav-item dropdown active">
              <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Catalog</a>
              <div class="dropdown-menu" aria-labelledby="dropdown04">
              	<a class="dropdown-item" href="shop.html">Shop</a>
                <a class="dropdown-item" href="product-single.html">Single Product</a>
                <a class="dropdown-item" href="cart.html">Cart</a>
                <a class="dropdown-item" href="checkout.html">Checkout</a>
              </div>
            </li>
	          <li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
	          <li class="nav-item"><a href="blog.html" class="nav-link">Blog</a></li>
	          <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li>
	          <li class="nav-item cta cta-colored"><a href="cart.html" class="nav-link"><span class="icon-shopping_cart"></span>[0]</a></li>

	        </ul>
	      </div>
	    </div>
	  </nav>
    <!-- END nav -->

    <div class="hero-wrap hero-bread" style="background-image: url('images/bg_6.jpg');">
      <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center">
          	<p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home</a></span> <span>Checkout</span></p>
            <h1 class="mb-0 bread">Checkout</h1>
          </div>
        </div>
      </div>
    </div>

   <section class="ftco-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-10 ftco-animate">
        <form action="place-order.php" method="POST" id="checkout-form" class="billing-form" onsubmit="return validateCheckout()">
          <h3 class="mb-4 billing-heading">Billing Details</h3>
          <div class="row align-items-end">
            <div class="col-md-6">
              <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" name="first_name" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
              </div>
            </div>

            <div class="w-100"></div>

            <div class="col-md-12">
              <div class="form-group">
                <label for="country">City</label>
                <div class="select-wrap">
                  <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                  <select name="country" class="form-control" required>
                    <option value="">Select City</option>
                    <option value="Karachi">Karachi</option>
                    <option value="Lahore">Lahore</option>
                    <option value="Islamabad">Islamabad</option>
                    <option value="Faisalabad">Faisalabad</option>
                    <option value="Quetta">Quetta</option>
                    <option value="Peshawar">Peshawar</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="w-100"></div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="streetaddress">Street Address</label>
                <input type="text" name="address" class="form-control" placeholder="House number and street name" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" name="apartment" class="form-control" placeholder="Apartment, suite, unit etc. (optional)">
              </div>
            </div>

            <div class="w-100"></div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="towncity">Town / City</label>
                <input type="text" name="city" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="postcodezip">Postcode / ZIP *</label>
                <input type="text" name="postcode" class="form-control" required>
              </div>
            </div>

            <div class="w-100"></div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="emailaddress">Email Address</label>
                <input type="email" name="email" class="form-control" required>
              </div>
            </div>

            <div class="w-100"></div>

          
          </div>

          <!-- Cart Total and Payment -->
          <div class="row mt-5 pt-3 d-flex">
            <div class="col-md-6 d-flex">
              <div class="cart-detail cart-total bg-light p-3 p-md-4">
                <h3 class="billing-heading mb-4">Cart Total</h3>

               <?php
$totals = $_SESSION['totals'] ?? [
    'subtotal' => 0,
    'discount' => 0,
    'delivery' => 0,
    'grand_total' => 0
];
// Fallback if session is missing
$totals = $_SESSION['totals'] ?? [];

$subtotal = $totals['subtotal'] ?? 0;
$discount = $totals['discount'] ?? 0;
$delivery = $totals['delivery'] ?? 0;
$grand_total = $totals['grand_total'] ?? 0;

// Optional: store in session
// $_SESSION['cart_total'] = $subtotal;
// $_SESSION['cart_discount'] = $discount;
// $_SESSION['cart_delivery'] = $delivery;
// $_SESSION['cart_grand_total'] = $grandTotal;
?>

<p class="d-flex justify-content-between">
    <span>Total</span>
    <span>Rs <?= number_format($totals['subtotal'], 2) ?></span>
</p>
<p class="d-flex justify-content-between">
    <span>Discount</span>
    <span>- Rs <?= number_format($totals['discount'], 2) ?></span>
</p>
<p class="d-flex justify-content-between">
    <span>Delivery</span>
    <span>Rs <?= number_format($totals['delivery'], 2) ?></span>
</p>
<hr>
<p class="d-flex justify-content-between total-price">
    <strong>Grand Total</strong>
    <strong>Rs <?= number_format($totals['grand_total'], 2) ?></strong>
</p>

<input type="hidden" name="total_amount" value="<?= $totals['grand_total'] ?>">
              </div>
            </div>

    <div class="col-md-6">
  <div class="cart-detail bg-light p-3 p-md-4">
    <h3 class="billing-heading mb-4">Payment Method</h3>

   <div class="form-group">
  <div class="col-md-12">
    <div class="radio">
      <label><input type="radio" name="payment_method" value="cod" class="mr-2" checked> Cash on Delivery</label>
    </div>
    <div class="radio">
      <label><input type="radio" name="payment_method" value="card" class="mr-2"> Credit/Debit Card</label>
    </div>
  </div>
</div>

    <div class="form-group mt-4">
      <div class="col-md-12">
        <p>
          <button type="submit" class="btn btn-primary py-3 px-4 w-100">Place an order</button>
        </p>
      </div>
    </div>
  </div>
</div>


            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

		

    <footer class="ftco-footer ftco-section">
      <div class="container">
      	<div class="row">
      		<div class="mouse">
						<a href="#" class="mouse-icon">
							<div class="mouse-wheel"><span class="ion-ios-arrow-up"></span></div>
						</a>
					</div>
      	</div>
        <div class="row mb-5">
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Minishop</h2>
              <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia.</p>
              <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
                <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4 ml-md-5">
              <h2 class="ftco-heading-2">Menu</h2>
              <ul class="list-unstyled">
                <li><a href="#" class="py-2 d-block">Shop</a></li>
                <li><a href="#" class="py-2 d-block">About</a></li>
                <li><a href="#" class="py-2 d-block">Journal</a></li>
                <li><a href="#" class="py-2 d-block">Contact Us</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md-4">
             <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Help</h2>
              <div class="d-flex">
	              <ul class="list-unstyled mr-l-5 pr-l-3 mr-4">
	                <li><a href="#" class="py-2 d-block">Shipping Information</a></li>
	                <li><a href="#" class="py-2 d-block">Returns &amp; Exchange</a></li>
	                <li><a href="#" class="py-2 d-block">Terms &amp; Conditions</a></li>
	                <li><a href="#" class="py-2 d-block">Privacy Policy</a></li>
	              </ul>
	              <ul class="list-unstyled">
	                <li><a href="#" class="py-2 d-block">FAQs</a></li>
	                <li><a href="#" class="py-2 d-block">Contact</a></li>
	              </ul>
	            </div>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
            	<h2 class="ftco-heading-2">Have a Questions?</h2>
            	<div class="block-23 mb-3">
	              <ul>
	                <li><span class="icon icon-map-marker"></span><span class="text">203 Fake St. Mountain View, San Francisco, California, USA</span></li>
	                <li><a href="#"><span class="icon icon-phone"></span><span class="text">+2 392 3929 210</span></a></li>
	                <li><a href="#"><span class="icon icon-envelope"></span><span class="text">info@yourdomain.com</span></a></li>
	              </ul>
	            </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">

            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
						  Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart color-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
						  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
						</p>
          </div>
        </div>
      </div>
    </footer>
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>


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
  <script src="js/scrollax.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
  <script src="js/google-map.js"></script>
  <script src="js/main.js"></script>
<div aria-live="polite" aria-atomic="true" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;">
  <div id="errorToast" class="toast" data-delay="4000">
    <div class="toast-header bg-danger text-white">
      <strong class="mr-auto">Form Error</strong>
      <small>Now</small>
      <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button>
    </div>
    <div class="toast-body" id="toastMessage">
      Something went wrong.
    </div>
  </div>
</div>

  <script>
		$(document).ready(function(){

		var quantitiy=0;
		   $('.quantity-right-plus').click(function(e){
		        
		        // Stop acting like a button
		        e.preventDefault();
		        // Get the field name
		        var quantity = parseInt($('#quantity').val());
		        
		        // If is not undefined
		            
		            $('#quantity').val(quantity + 1);

		          
		            // Increment
		        
		    });

		     $('.quantity-left-minus').click(function(e){
		        // Stop acting like a button
		        e.preventDefault();
		        // Get the field name
		        var quantity = parseInt($('#quantity').val());
		        
		        // If is not undefined
		      
		            // Increment
		            if(quantity>0){
		            $('#quantity').val(quantity - 1);
		            }
		    });
		    
		});
	</script>
   <script>
function showToast(message) {
  const toastMessage = document.getElementById("toastMessage");
  toastMessage.textContent = message;
  $('#errorToast').toast('show');
}

function validateCheckout() {
    const form = document.getElementById("checkoutForm");
    const requiredFields = form.querySelectorAll("[required]");
    let isValid = true;
    let errorMsg = "";

    requiredFields.forEach(field => field.classList.remove("border-danger"));

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add("border-danger");
            isValid = false;
        }
    });

    const emailField = form.querySelector("input[name='email']");
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (emailField && !emailRegex.test(emailField.value.trim())) {
        emailField.classList.add("border-danger");
        isValid = false;
        errorMsg = "Please enter a valid email address.";
        showToast(errorMsg);
        return false;
    }

    const phoneField = form.querySelector("input[name='phone']");
    const phoneRegex = /^03[0-9]{9}$/;
    if (phoneField && !phoneRegex.test(phoneField.value.trim())) {
        phoneField.classList.add("border-danger");
        isValid = false;
        errorMsg = "Please enter a valid Pakistani phone number (e.g. 03xxxxxxxxx).";
        showToast(errorMsg);
        return false;
    }

    const zipField = form.querySelector("input[name='postcode']");
    const zipRegex = /^[0-9]{4,6}$/;
    if (zipField && !zipRegex.test(zipField.value.trim())) {
        zipField.classList.add("border-danger");
        isValid = false;
        errorMsg = "Please enter a valid postcode (4-6 digits).";
        showToast(errorMsg);
        return false;
    }

    const paymentMethods = form.querySelectorAll("input[name='payment_method']");
    let paymentSelected = false;
    paymentMethods.forEach(method => {
        if (method.checked) paymentSelected = true;
    });
    if (!paymentSelected) {
        errorMsg = "Please select a payment method.";
        showToast(errorMsg);
        return false;
    }

    // Final check for empty required fields
    if (!isValid) {
        showToast("Please fill all required fields correctly.");
    }

    return isValid;
}
</script>
<script>
document.getElementById('checkout-form').addEventListener('submit', function(e) {
  const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
  if (selectedMethod && selectedMethod.value === 'card') {
    e.preventDefault();              // Prevent default submit
    this.action = 'mockgateway.php'; // Redirect form to mockgateway.php for card payment
    this.submit();                  // Submit the form normally to mockgateway.php
  }
  // If COD is selected, form will submit to place-order.php as usual
});
</script>



  </body>
</html>