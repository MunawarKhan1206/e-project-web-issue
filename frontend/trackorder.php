<?php session_start(); ?>
<?php
require_once '../includes/db.inc.php';
$products = $pdo->query("SELECT * FROM products ORDER BY RAND()")->fetchAll(PDO::FETCH_ASSOC);


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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #f3f4f6;
}

.track-container {
  max-width: 700px;
  margin: 60px auto 80px;
  background: #fff;
  padding: 40px 30px;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.07);
}

h3 {
  text-align: center;
  font-weight: 700;
  font-size: 24px;
  margin-bottom: 25px;
  color: #1f2937;
}

input[type="text"] {
  width: 100%;
  padding: 14px 16px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 15px;
  margin-bottom: 18px;
}

button {
  width: 100%;
  padding: 12px;
  background: linear-gradient(90deg, #f97316, #f59e0b);
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: bold;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s ease;
}

button:hover {
  background: linear-gradient(90deg, #ea580c, #d97706);
}

.order-info {
  background: #f9fafb;
  padding: 25px 20px;
  margin-top: 30px;
  border-radius: 14px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  text-align: left;
}

.order-info p {
  font-size: 15px;
  margin: 12px 0;
  color: #374151;
}

.order-info strong {
  color: #111827;
}

.badge {
  padding: 6px 16px;
  border-radius: 999px;
  font-size: 13px;
  color: white;
  font-weight: 500;
}

.badge-warning { background-color: #f59e0b; }
.badge-success { background-color: #10b981; }
.badge-danger  { background-color: #ef4444; }
.badge-primary { background-color: #3b82f6; }
.badge-secondary { background-color: #6b7280; }

.progress-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  margin: 40px 0 10px;
}

.progress-line {
  position: absolute;
  top: 22px;
  left: 50px;
  right: 50px;
  height: 4px;
  background: linear-gradient(to right, #10b981 0%, #d1d5db 100%);
  z-index: 0;
  border-radius: 2px;
}

.progress-step {
  flex: 1;
  text-align: center;
  position: relative;
  z-index: 2;
}

.progress-step .icon {
  font-size: 20px;
  width: 40px;
  height: 40px;
  line-height: 40px;
  border-radius: 50%;
  margin: 0 auto 8px;
  background: #d1d5db;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.4s ease;
}

.progress-step.active .icon {
  background: #10b981;
}

.progress-step .label {
  font-size: 13px;
  color: #9ca3af;
}

.progress-step.active .label {
  color: #111827;
  font-weight: 600;
}

.center-btn {
  display: flex;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
  margin-top: 25px;
}

.btn-back {
  background: linear-gradient(135deg, #FDC830, #F37335);
  color: #fff;
  font-weight: 600;
  padding: 10px 24px;
  border-radius: 50px;
  text-decoration: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
}

.btn-back:hover {
  background: linear-gradient(135deg, #fbb034, #ffdd00);
  color: #000;
  transform: scale(1.05);
}

section.ftco-gallery {
  margin-top: 100px; /* Adds space before Instagram */
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
     <?php
	include ("navbar.php");
   ?>
    <!-- END nav -->

    <div class="hero-wrap hero-bread" style="background-image: url('images/bg_6.jpg');">
      <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center">
          	<p class="breadcrumbs"><span class="mr-2"><a href="index.php">Home</a></span> <span>Track Order</span></p>
            <h1 class="mb-0 bread">Track Order</h1>
          </div>
        </div>
      </div>
    </div>
	<section class="ftco-section bg-light">
<div class="track-container">
  <h3>Track Your Order</h3>
  <form method="POST">
    <input type="text" name="order_number" placeholder="Enter your Order Number" required>
    <button type="submit" name="track">Check Status</button>
  </form>

  <?php
  if (isset($_POST['track'])) {
      $order_number = trim($_POST['order_number']);
      $stmt = $pdo->prepare("SELECT * FROM orders2 WHERE order_number = ?");
      $stmt->execute([$order_number]);
      $order = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($order) {
          $status_raw = strtolower($order['status'] ?? 'processed');
          $status = ucfirst($status_raw);
          $status_class = match($status_raw) {
              'processed' => 'badge-warning',
              'shipped'   => 'badge-primary',
              'delivered' => 'badge-success',
              default     => 'badge-secondary'
          };

          echo '<div class="order-info">';
          echo "<p><strong>Order Number:</strong> " . htmlspecialchars($order['order_number']) . "</p>";
          echo "<p><strong>Status:</strong> <span class='badge {$status_class}'>" . htmlspecialchars($status) . "</span></p>";
          echo "<p><strong>Order Date:</strong> " . date("F j, Y", strtotime($order['created_at'])) . "</p>";
          echo "<p><strong>Total:</strong> Rs. " . number_format($order['grand_total'], 0) . "</p>";
          echo "<p><strong>Payment:</strong> " . htmlspecialchars($order['payment_method']) . "</p>";

          $steps = [
              'processed' => ['label' => 'Processed', 'icon' => 'fa-solid fa-gear'],
              'shipped'   => ['label' => 'Shipped', 'icon' => 'fa-solid fa-truck'],
              'delivered' => ['label' => 'Delivered', 'icon' => 'fa-solid fa-box-open']
          ];
          $statusKeys = array_keys($steps);
          $currentIndex = array_search($status_raw, $statusKeys);

          echo '<div class="progress-container">';
          echo '<div class="progress-line"></div>';

          $i = 0;
          foreach ($steps as $key => $step) {
              $isActive = $i <= $currentIndex ? 'active' : '';
              echo "<div class='progress-step {$isActive}'>";
              echo "<div class='icon'><i class='{$step['icon']}'></i></div>";
              echo "<div class='label'>{$step['label']}</div>";
              echo "</div>";
              $i++;
          }
          echo '</div>'; // progress-container
          echo '</div>'; // order-info
      } else {
          echo '<div class="order-info"><p style="color:red;">⚠ No order found with that number.</p></div>';
      }
  }
  ?>

   
</section>

		<section class="ftco-gallery">
    	<div class="container">
    		<div class="row justify-content-center">
    			<div class="col-md-8 heading-section text-center mb-4 ftco-animate">
            <h2 class="mb-4">Follow Us On Instagram</h2>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in</p>
          </div>
    		</div>
    	</div>
    	<div class="container-fluid px-0">
    		<div class="row no-gutters">
					<div class="col-md-4 col-lg-2 ftco-animate">
						<a href="images/gallery-1.jpg" class="gallery image-popup img d-flex align-items-center" style="background-image: url(images/gallery-1.jpg);">
							<div class="icon mb-4 d-flex align-items-center justify-content-center">
    						<span class="icon-instagram"></span>
    					</div>
						</a>
					</div>
					<div class="col-md-4 col-lg-2 ftco-animate">
						<a href="images/gallery-2.jpg" class="gallery image-popup img d-flex align-items-center" style="background-image: url(images/gallery-2.jpg);">
							<div class="icon mb-4 d-flex align-items-center justify-content-center">
    						<span class="icon-instagram"></span>
    					</div>
						</a>
					</div>
					<div class="col-md-4 col-lg-2 ftco-animate">
						<a href="images/gallery-3.jpg" class="gallery image-popup img d-flex align-items-center" style="background-image: url(images/gallery-3.jpg);">
							<div class="icon mb-4 d-flex align-items-center justify-content-center">
    						<span class="icon-instagram"></span>
    					</div>
						</a>
					</div>
					<div class="col-md-4 col-lg-2 ftco-animate">
						<a href="images/gallery-4.jpg" class="gallery image-popup img d-flex align-items-center" style="background-image: url(images/gallery-4.jpg);">
							<div class="icon mb-4 d-flex align-items-center justify-content-center">
    						<span class="icon-instagram"></span>
    					</div>
						</a>
					</div>
					<div class="col-md-4 col-lg-2 ftco-animate">
						<a href="images/gallery-5.jpg" class="gallery image-popup img d-flex align-items-center" style="background-image: url(images/gallery-5.jpg);">
							<div class="icon mb-4 d-flex align-items-center justify-content-center">
    						<span class="icon-instagram"></span>
    					</div>
						</a>
					</div>
					<div class="col-md-4 col-lg-2 ftco-animate">
						<a href="images/gallery-6.jpg" class="gallery image-popup img d-flex align-items-center" style="background-image: url(images/gallery-6.jpg);">
							<div class="icon mb-4 d-flex align-items-center justify-content-center">
    						<span class="icon-instagram"></span>
    					</div>
						</a>
					</div>
        </div>
    	</div>
    </section>

   <?php
	include ("footer.php");
	?>
    
  

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
    
  </body>
</html>
