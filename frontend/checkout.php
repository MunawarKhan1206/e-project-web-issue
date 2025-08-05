<?php
session_start();

// Check login status
$isUserLoggedIn = isset($_SESSION['user_id']) && in_array($_SESSION['user_role'], ['customer', 'admin']);

// Control modal visibility
$showLoginModal = !$isUserLoggedIn;

$user_email = $_SESSION['user_email'] ?? '';

// Continue only if user is logged in
if ($isUserLoggedIn && !empty($_SESSION['cart'])) {
  $cart = $_SESSION['cart'];

  require_once("../includes/db.inc.php");

  $placeholders = implode(',', array_fill(0, count($cart), '?'));
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
  $stmt->execute(array_keys($cart));
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $total = 0;
  $discount = 0;
  $deliveryFee = 200;

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

  $_SESSION['totals'] = [
    'subtotal' => $total,
    'discount' => $discount,
    'delivery' => $deliveryFee,
    'grand_total' => $grandTotal
  ];
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <title>Minishop -Checkout</title>
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

.input-error-message {
  color: #dc3545;
  font-size: 0.85rem;
  margin-top: 2px;
  display: none;
}

  .custom-blur-modal .modal-content {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 16px;
    color: #fff;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
  }

  .custom-blur-modal.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
    transform: scale(0.95);
    opacity: 0;
  }

  .custom-blur-modal.modal.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
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
              <span class="text">minishop@gmail.com</span>
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
  include 'navbar.php';
  ?>
  <!-- END nav -->
<!-- Login Modal -->
<div class="modal fade custom-blur-modal" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content p-4">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Please Log In</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.href='index.php';">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
<p>You need to be logged in to proceed to checkout.</p>
<p class="text-warning font-weight-bold">
  Redirecting in <span id="countdown">5</span> second<span id="s">s</span>...
</p>
<!-- <a href="../form/index.php" class="btn btn-primary btn-block">Go to Login Now</a> -->


      </div>
    </div>
  </div>
</div>



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
                <small class="input-error-message"></small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
                <small class="input-error-message"></small>
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
                  <small class="input-error-message"></small>
                </div>
              </div>
            </div>

            <div class="w-100"></div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="streetaddress">Street Address</label>
                <input type="text" name="address" class="form-control" placeholder="House number and street name" required>
                <small class="input-error-message"></small>
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
                <small class="input-error-message"></small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="postcodezip">Postcode / ZIP *</label>
                <input type="text" name="postcode" class="form-control" required>
                <small class="input-error-message"></small>
              </div>
            </div>

            <div class="w-100"></div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" class="form-control" required>
                <small class="input-error-message"></small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user_email) ?>" readonly>
                <small class="input-error-message"></small>
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
                $subtotal = $totals['subtotal'] ?? 0;
                $discount = $totals['discount'] ?? 0;
                $delivery = $totals['delivery'] ?? 0;
                $grand_total = $totals['grand_total'] ?? 0;
                ?>

                <p class="d-flex justify-content-between">
                  <span>Total</span>
                  <span>Rs <?= number_format($subtotal, 2) ?></span>
                </p>
                <p class="d-flex justify-content-between">
                  <span>Discount</span>
                  <span>- Rs <?= number_format($discount, 2) ?></span>
                </p>
                <p class="d-flex justify-content-between">
                  <span>Delivery</span>
                  <span>Rs <?= number_format($delivery, 2) ?></span>
                </p>
                <hr>
                <p class="d-flex justify-content-between total-price">
                  <strong>Grand Total</strong>
                  <strong>Rs <?= number_format($grand_total, 2) ?></strong>
                </p>

                <input type="hidden" name="total_amount" value="<?= $grand_total ?>">
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
        </form>
      </div>
    </div>
  </div>
</section>




  <?php
  include("footer.php");
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
  <script src="js/scrollax.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
  <script src="js/google-map.js"></script>
  <script src="js/main.js"></script>
  <?php if ($showLoginModal): ?>
  <script>
    $(document).ready(function () {
      $('#loginModal').modal('show');

      let seconds = 5;
      const countdownEl = document.getElementById("countdown");
      const pluralEl = document.getElementById("s");

      const interval = setInterval(function () {
        seconds--;

        if (countdownEl) countdownEl.textContent = seconds;
        if (pluralEl) pluralEl.style.display = seconds === 1 ? 'none' : 'inline';

        if (seconds <= 0) {
          clearInterval(interval);
          window.location.href = '../form/index.php';
        }
      }, 1000);
    });
  </script>
<?php endif; ?>



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
 <script>
function showToast(message) {
  const toastMessage = document.getElementById("toastMessage");
  toastMessage.textContent = message;
  $('#errorToast').toast('show');
}


function validateCheckout() {
  const form = document.getElementById("checkout-form");
  let isValid = true;

  const nameRegex = /^[A-Za-z]{2,}(?: [A-Za-z]{2,})*$/; // First/Last name (no digits or symbols, at least 2 chars each word)
  const addressRegex = /^[A-Za-z0-9\s,'#.-]{5,}$/;      // Letters, numbers, basic punctuations
  const cityRegex = /^[A-Za-z\s]{2,}$/;                 // Min 2 letters, letters and spaces only
  const postcodeRegex = /^[0-9]{4,6}$/;                 // 4 to 6 digits only
  const phoneRegex = /^03[0-9]{9}$/;                    // Starts with 03 and 11 digits total
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;      // Standard email format

  const fields = {
    first_name: {
      regex: nameRegex,
      message: "Enter a valid first name (letters only, no symbols, min 2 characters)."
    },
    last_name: {
      regex: nameRegex,
      message: "Enter a valid last name (letters only, no symbols, min 2 characters)."
    },
    country: {
      regex: cityRegex,
      message: "Please select a valid city."
    },
    address: {
      regex: addressRegex,
      message: "Enter a valid address (at least 5 characters, no symbols)."
    },
    city: {
      regex: cityRegex,
      message: "Enter a valid city (letters only, min 2 characters)."
    },
    postcode: {
      regex: postcodeRegex,
      message: "Enter a valid postcode (4 to 6 digits)."
    },
    phone: {
      regex: phoneRegex,
      message: "Enter a valid Pakistani number (e.g., 03XXXXXXXXX)."
    },
    email: {
      regex: emailRegex,
      message: "Enter a valid email address."
    }
  };

  // Remove previous error styling
  Object.keys(fields).forEach(id => {
    const input = form.querySelector(`[name="${id}"]`);
    if (input) input.classList.remove("border-danger");
  });

  for (let key in fields) {
    const input = form.querySelector(`[name="${key}"]`);
    if (input) {
      const value = input.value.trim();
      if (!value || value.length < 2 || !fields[key].regex.test(value)) {
        input.classList.add("border-danger");
        showToast(fields[key].message);
        return false;
      }
    }
  }

  // Validate payment method
  const paymentMethods = form.querySelectorAll("input[name='payment_method']");
  let paymentSelected = false;
  paymentMethods.forEach(method => {
    if (method.checked) paymentSelected = true;
  });

  if (!paymentSelected) {
    showToast("Please select a payment method.");
    return false;
  }

  return true;
}
</script>


 <script>
document.getElementById('checkout-form').addEventListener('submit', function(e) {
  if (!validateCheckout()) {
    e.preventDefault();
    return;
  }

  const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
  if (selectedMethod && selectedMethod.value === 'card') {
    e.preventDefault();
    this.action = 'mockgateway.php';
    this.submit();
  }
});
</script>




</body>

</html>