<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Minishop</title>
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
</head>
<?php
require_once '../includes/db.inc.php';
try {
	$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
	$products = $stmt->fetchAll();
} catch (PDOException $e) {
	echo "Error fetching products: " . $e->getMessage();
	$products = [];
}

$show_modal = false;
$notice = null;
$notice_title = '';
$notice_message = '';

if (!isset($_COOKIE['hide_outofstock_modal'])) {
    $stmt = $pdo->query("SELECT * FROM site_notices WHERE active = 1 ORDER BY created_at DESC LIMIT 1");
    $notice = $stmt->fetch();

    if ($notice) {
        $notice_title = $notice['title'];
        $notice_message = $notice['message'];
        $show_modal = true;
    }
}



?>
<style>
.modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(8px);
  z-index: 9999;
}

.glass-modal {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 30px;
  text-align: center;
  color: #fff;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
  border: 1px solid rgba(255, 255, 255, 0.18);
}

.glass-modal h2 {
  margin-bottom: 1rem;
  font-size: 24px;
  font-weight: 700;
}

.glass-modal p {
  font-size: 16px;
  margin-bottom: 1.5rem;
  white-space: pre-wrap;
}

.glass-modal button {
  background: #fff;
  border: none;
  color: #333;
  padding: 0.6rem 1.2rem;
  border-radius: 30px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s ease;
}

.glass-modal button:hover {
  background: #eee;
}
</style>

<body class="goto-here">
	<div class="py-1 bg-black">
		<div class="container">
			<div class="row no-gutters d-flex align-items-start align-items-center px-md-0">
				<div class="col-lg-12 d-block">
					<div class="row d-flex">
						<div class="col-md pr-4 d-flex topper align-items-center">
							<div class="icon mr-2 d-flex justify-content-center align-items-center"><span
									class="icon-phone2"></span></div>
							<span class="text">+ 1235 2355 98</span>
						</div>
						<div class="col-md pr-4 d-flex topper align-items-center">
							<div class="icon mr-2 d-flex justify-content-center align-items-center"><span
									class="icon-paper-plane"></span></div>
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

<section id="home-section" class="hero js-fullheight" style="position: relative; overflow: hidden;">
	<!-- Background Image -->
	<img src="images/artboard-studio-c-KRSHct7Ho-unsplash.jpg" alt="Hero Background"
		style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">

	<!-- Text Content -->
	<div class="container-fluid p-0 h-100">
		<div class="row no-gutters align-items-center justify-content-center text-center h-100"
			style="position: relative; z-index: 2;">
			<div class="text px-4 px-md-5 ftco-animate">
				<span class="subheading d-block mb-3" style="
					font-size: 1.1rem;
					color: #fff;
					text-transform: uppercase;
					letter-spacing: 2px;
					font-weight: bolder;
					text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);">
					#New Arrivals
				</span>

				<h1 class="mb-4 mt-2" style="
					color: #fff;
					font-size: 2.4rem;
					font-weight: 800;
					text-shadow: 4px 4px 8px rgba(0,0,0,0.8);">
					Charming Gifts & Stationery
				</h1>

				<p class="mb-4 px-2 px-md-5" style="
					color: #f8f9fa;
					font-size: 1.2rem;
					text-shadow: 2px 2px 5px rgba(0,0,0,0.7);
					font-weight:bold;
					">
					Discover elegant gifts, creative stationery, and unique accessories that make every occasion special.
				</p>

				<p>
					<a href="#newarrival" style="scroll-behavior: smooth !important;" class="btn btn-warning px-4 py-2 rounded-pill font-weight-bold shadow-lg">
						Discover Now
					</a>
				</p>
			</div>
		</div>
	</div>
</section>








	<section class="ftco-section ftco-no-pt ftco-no-pb">
		<div class="container">
			<div class="row no-gutters ftco-services">
				<div class="col-lg-4 text-center d-flex align-self-stretch ftco-animate">
					<div class="media block-6 services p-4 py-md-5">
						<div class="icon d-flex justify-content-center align-items-center mb-4">
							<span class="flaticon-bag"></span>
						</div>
						<div class="media-body">
							<h3 class="heading">Free Shipping</h3>
							<p>Enjoy fast and free delivery on all orders above PKR 1500. We bring your gifts and
								stationery right to your doorstep—nationwide.</p>
						</div>

					</div>
				</div>
				<div class="col-lg-4 text-center d-flex align-self-stretch ftco-animate">
					<div class="media block-6 services p-4 py-md-5">
						<div class="icon d-flex justify-content-center align-items-center mb-4">
							<span class="flaticon-customer-service"></span>
						</div>
						<div class="media-body">
							<h3 class="heading">Customer Support</h3>
							<p>Have questions or need help with your order? Our support team is always ready to assist
								you — before and after your purchase.</p>
						</div>

					</div>
				</div>
				<div class="col-lg-4 text-center d-flex align-self-stretch ftco-animate">
					<div class="media block-6 services p-4 py-md-5">
						<div class="icon d-flex justify-content-center align-items-center mb-4">
							<span class="flaticon-payment-security"></span>
						</div>
						<div class="media-body">
							<h3 class="heading">Secure Payments</h3>
							<p>Pay confidently with our secure checkout. We support multiple trusted payment methods to
								ensure your information stays safe.</p>
						</div>

					</div>
				</div>
			</div>
		</div>
	</section>
<?php if ($notice): ?>
<div id="outOfStockModal" class="modal-overlay">
  <div class="glass-modal">
    <h4 class="mb-3 font-weight-bold">Important Notice</h4>
    <p class="mb-4"><?= htmlspecialchars($notice['message']) ?></p>
    <button id="dismissModalBtn" class="btn btn-primary px-4">Got it</button>
  </div>
</div>
<?php endif; ?>

	<section class="ftco-section bg-light">
		<div class="container" id="newarrival">
			<div class="row justify-content-center mb-3 pb-3">
				<div class="col-md-12 heading-section text-center ftco-animate">
					<h2 class="mb-4" >New Arrivals</h2>
					<p>Discover our latest collection of gifts, stationery, accessories, bags and bottles — perfect for
						every occasion, from school to celebrations.</p>
				</div>

			</div>
		</div>
		<div class="container">
			<div class="row">
				<?php foreach ($products as $row): ?>
					<div class="col-md-3 d-flex">
						<div class="product d-flex flex-column position-relative">
							<?php if ($row['stock'] > 0): ?>
								<span class="badge badge-success position-absolute" style="top: 10px; left: 10px;">In
									Stock</span>
							<?php else: ?>
								<span class="badge badge-danger position-absolute" style="top: 10px; left: 10px;">Out of
									Stock</span>
							<?php endif; ?>

							<a href="#" class="img-prod">
								<img class="img-fluid" src="product/<?= htmlspecialchars($row['image']) ?>"
									alt="<?= htmlspecialchars($row['name']) ?>" style="height: 274px !important; ">
								<div class="overlay"></div>
							</a>

							<div class="text py-3 pb-4 px-3">
								<div class="d-flex">
									<div class="cat"><span><?= htmlspecialchars($row['category']) ?></span></div>

									<div class="rating">
										<p class="text-right mb-0">
											<a href="#"><span class="ion-ios-star-outline"></span></a>
											<a href="#"><span class="ion-ios-star-outline"></span></a>
											<a href="#"><span class="ion-ios-star-outline"></span></a>
											<a href="#"><span class="ion-ios-star-outline"></span></a>
											<a href="#"><span class="ion-ios-star-outline"></span></a>
										</p>
									</div>
								</div>

								<h3><a href="#"><?= htmlspecialchars($row['name']) ?></a></h3>
								<!-- <p class="mb-2"><?= htmlspecialchars($row['description']) ?></p> -->
								<div class="pricing">
									<p class="price"><span>Rs <?= number_format($row['price']) ?></span></p>
								</div>

								<p class="bottom-area d-flex px-3">
									<?php if ($row['stock'] > 0): ?>
										<a href="cart-handler.php?action=add&id=<?= $row['id'] ?>"
											class="add-to-cart text-center py-2 btn-block">
											Add to cart <span><i class="ion-ios-cart ml-1"></i></span>
										</a>
									<?php else: ?>
										<button class="btn btn-secondary btn-block text-center py-2" disabled>
											<i class="ion-ios-cart"></i> Out of Stock
										</button>
									<?php endif; ?>
								</p>


								</p>
							</div>
						</div>
					</div>
				<?php endforeach; ?>


			</div>
			<div class="d-flex justify-content-center mt-4">
				<a href="shop.php" class="btn btn-primary px-4 py-2" style="font-weight:bolder;">
					See More Products
				</a>
			</div>

	</section>



	<section class="ftco-section ftco-choose ftco-no-pb ftco-no-pt">
		<div class="container">
			<div class="row no-gutters">
				<div class="col-lg-4">
					<div class="choose-wrap divider-one img p-5 d-flex align-items-end"
						style="background-image: url(product/main-col-1.png);">

						<div class="text text-center text-white px-2">
							<!-- <span class="subheading">Gift Sets for Him</span> -->
							<h2>Stationery Highlights</h2>
							<p>Explore premium notebooks, stylish pens, organizers, and creative supplies — perfect for
								school, office, or gifting.</p>

							<p><a href="shop.php" class="btn btn-black px-3 py-2">Shop now</a></p>
						</div>
					</div>
				</div>
				<div class="col-lg-8">
					<div class="row no-gutters choose-wrap divider-two align-items-stretch">
						<div class="col-md-12">
							<div class="choose-wrap full-wrap img align-self-stretch d-flex align-item-center justify-content-end"
								style="background-image: url(product/WhatsApp\ Image\ 2025-07-14\ at\ 12.18.47_27dfa101.jpg);">
								<div class="col-md-7 d-flex align-items-center">
									<div class="text text-white px-5">
										<!-- <span class="subheading">Gift Sets for Her</span> -->
									<h2>Nike Bags Collection</h2>
<p>Discover our exclusive range of authentic Nike backpacks, duffel bags, and gym sacks — perfect for school, sports, travel, and everyday use. Built for durability and iconic style.</p>

										<p><a href="shop.php" class="btn btn-black px-3 py-2">Shop now</a></p>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="row no-gutters">
								<div class="col-md-6">
									<div
										class="choose-wrap wrap img align-self-stretch bg-light d-flex align-items-center">
										<div class="text text-center px-5">
											<span class="subheading">Featured Collection</span>
											<h2>Exclusive Gift Picks</h2>
											<p>Discover our handpicked range of premium gifts, planners, and creative
												essentials — perfect for any occasion.</p>

											<p><a href="#" class="btn btn-black px-3 py-2">Shop now</a></p>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="choose-wrap wrap img align-self-stretch d-flex align-items-center"
										style="background-image: url(product/image.png);">
										<div class="text text-center text-white px-5">
											<span class="subheading">Top Picks</span>
											<h2>Best Sellers</h2>
											<p>Discover our most popular items — from stylish bags and eco-friendly
												bottles to premium notebooks and thoughtful gifts. Handpicked favorites
												our customers love.</p>

											<p><a href="#" class="btn btn-black px-3 py-2">Shop now</a></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- <section class="ftco-section ftco-deal bg-primary">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<img src="images/prod-1.png" class="img-fluid" alt="">
				</div>
				<div class="col-md-6">
					<div class="heading-section heading-section-white">
						<span class="subheading">Deal of the month</span>
						<h2 class="mb-3">Deal of the month</h2>
					</div>
					<div id="timer" class="d-flex mb-4">
						<div class="time" id="days"></div>
						<div class="time pl-4" id="hours"></div>
						<div class="time pl-4" id="minutes"></div>
						<div class="time pl-4" id="seconds"></div>
					</div>
					<div class="text-deal">
						<h2><a href="#">Nike Free RN 2019 iD</a></h2>
						<p class="price"><span class="mr-2 price-dc">$120.00</span><span
								class="price-sale">$80.00</span></p>
						<ul class="thumb-deal d-flex mt-4">
							<li class="img" style="background-image: url(images/product-6.png);"></li>
							<li class="img" style="background-image: url(images/product-2.png);"></li>
							<li class="img" style="background-image: url(images/product-4.png);"></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section> -->

	<section class="ftco-section testimony-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-5">
					<div class="services-flow">
						<div class="services-2 p-4 d-flex ftco-animate">
							<div class="icon">
								<span class="flaticon-bag"></span>
							</div>
							<div class="text">
								<h3>Free Shipping</h3>
								<p class="mb-0">Enjoy free delivery on all orders — no minimum spend required. Fast,
									secure, and hassle-free.</p>

							</div>
						</div>
						<div class="services-2 p-4 d-flex ftco-animate">
							<div class="icon">
								<span class="flaticon-heart-box"></span>
							</div>
							<div class="text">
								<h3>Valuable Gifts</h3>
								<p class="mb-0">Discover handpicked gifts that are thoughtful, elegant, and perfect for
									every occasion.</p>

							</div>
						</div>
						<div class="services-2 p-4 d-flex ftco-animate">
							<div class="icon">
								<span class="flaticon-payment-security"></span>
							</div>
							<div class="text">
								<h3>All Day Support</h3>
								<p class="mb-0">Our team is here to assist you 24/7 with orders, returns, and product
									inquiries.</p>

							</div>
						</div>
						<div class="services-2 p-4 d-flex ftco-animate">
							<div class="icon">
								<span class="flaticon-customer-service"></span>
							</div>
							<div class="text">
								<h3>All Day Support</h3>
								<p class="mb-0">Need help? Our support team is available around the clock to assist you
									anytime.</p>

							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-7">
					<div class="heading-section ftco-animate mb-5">
						<h2 class="mb-4">What Our Customers Say</h2>
						<p>We value your trust! Read what our happy customers have to say about their experience
							shopping for gifts, bags, and bottles at our store.</p>

					</div>
					<div class="carousel-testimony owl-carousel">
						<div class="item">
							<div class="testimony-wrap">
								<div class="user-img mb-4" style="background-image: url(images/person_1.jpg)">
									<span class="quote d-flex align-items-center justify-content-center">
										<i class="icon-quote-left"></i>
									</span>
								</div>
								<div class="text">
									<p class="mb-4 pl-4 line">I was impressed with the quality of products and fast
										delivery. The gift packaging was thoughtful and elegant—perfect for every
										occasion!</p>
									<p class="name">Garreth Smith</p>
									<span class="position">Marketing Manager</span>

								</div>
							</div>
						</div>
						<div class="item">
							<div class="testimony-wrap">
								<div class="user-img mb-4" style="background-image: url(images/person_2.jpg)">
									<span class="quote d-flex align-items-center justify-content-center">
										<i class="icon-quote-left"></i>
									</span>
								</div>
								<div class="text">
									<p class="mb-4 pl-4 line">This store has become my go-to for premium gifts and
										stationery. Everything—from the wrapping to the delivery—was flawless and
										timely.</p>
									<p class="name">Garreth Smith</p>
									<span class="position">Interface Designer</span>
								</div>
							</div>
						</div>
						<div class="item">
							<div class="testimony-wrap">
								<div class="user-img mb-4" style="background-image: url(images/person_3.jpg)">
									<span class="quote d-flex align-items-center justify-content-center">
										<i class="icon-quote-left"></i>
									</span>
								</div>
								<div class="text">
									<p class="mb-4 pl-4 line">Absolutely loved the range of creative stationery and gift
										options! The packaging was elegant, and the support team was incredibly helpful.
									</p>
									<p class="name">Garreth Smith</p>
									<span class="position">UI Designer</span>

								</div>
							</div>
						</div>
						<div class="item">
							<div class="testimony-wrap">
								<div class="user-img mb-4" style="background-image: url(images/person_1.jpg)">
									<span class="quote d-flex align-items-center justify-content-center">
										<i class="icon-quote-left"></i>
									</span>
								</div>
								<div class="text">
									<p class="mb-4 pl-4 line">A fantastic place for finding thoughtful gifts and quality
										stationery. The website is smooth, and the delivery was right on time!</p>
									<p class="name">Garreth Smith</p>
									<span class="position">Web Developer</span>
									>
								</div>
							</div>
						</div>
						<div class="item">
							<div class="testimony-wrap">
								<div class="user-img mb-4" style="background-image: url(images/person_1.jpg)">
									<span class="quote d-flex align-items-center justify-content-center">
										<i class="icon-quote-left"></i>
									</span>
								</div>
								<div class="text">
									<p class="mb-4 pl-4 line">Absolutely loved the unique collection of gifts and
										stationery. The design and quality exceeded my expectations!</p>
									<p class="name">Zaid Malik</p>
									<span class="position">System Analyst</span>
									>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="ftco-gallery">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-8 heading-section text-center mb-4 ftco-animate">
					<h2 class="mb-4">Follow Us On Instagram</h2>
					<p>Discover the latest in gifts, stylish bags, and eco-friendly bottles. Follow us for daily
						inspiration, product updates, and exclusive offers!</p>

				</div>
			</div>
		</div>
		<div class="container-fluid px-0">
			<div class="row no-gutters">
				<div class="col-md-4 col-lg-2 ftco-animate">
					<a href="images/gallery-1.jpg" class="gallery image-popup img d-flex align-items-center"
						style="background-image: url(images/gallery-1.jpg);">
						<div class="icon mb-4 d-flex align-items-center justify-content-center">
							<span class="icon-instagram"></span>
						</div>
					</a>
				</div>
				<div class="col-md-4 col-lg-2 ftco-animate">
					<a href="images/gallery-2.jpg" class="gallery image-popup img d-flex align-items-center"
						style="background-image: url(images/gallery-2.jpg);">
						<div class="icon mb-4 d-flex align-items-center justify-content-center">
							<span class="icon-instagram"></span>
						</div>
					</a>
				</div>
				<div class="col-md-4 col-lg-2 ftco-animate">
					<a href="images/gallery-3.jpg" class="gallery image-popup img d-flex align-items-center"
						style="background-image: url(images/gallery-3.jpg);">
						<div class="icon mb-4 d-flex align-items-center justify-content-center">
							<span class="icon-instagram"></span>
						</div>
					</a>
				</div>
				<div class="col-md-4 col-lg-2 ftco-animate">
					<a href="images/gallery-4.jpg" class="gallery image-popup img d-flex align-items-center"
						style="background-image: url(images/gallery-4.jpg);">
						<div class="icon mb-4 d-flex align-items-center justify-content-center">
							<span class="icon-instagram"></span>
						</div>
					</a>
				</div>
				<div class="col-md-4 col-lg-2 ftco-animate">
					<a href="images/gallery-5.jpg" class="gallery image-popup img d-flex align-items-center"
						style="background-image: url(images/gallery-5.jpg);">
						<div class="icon mb-4 d-flex align-items-center justify-content-center">
							<span class="icon-instagram"></span>
						</div>
					</a>
				</div>
				<div class="col-md-4 col-lg-2 ftco-animate">
					<a href="images/gallery-6.jpg" class="gallery image-popup img d-flex align-items-center"
						style="background-image: url(images/gallery-6.jpg);">
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
	<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
			<circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
			<circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10"
				stroke="#F96D00" />
		</svg></div>

	<script>
		$(document).ready(function () {
			$('.add-to-cart').click(function (e) {
				e.preventDefault();

				const productId = $(this).data('id');

				$.get('cart-handler.php', { action: 'add', id: productId }, function (cartCount) {
					$('.cart-count').text(cartCount); // Update cart icon count
					alert('Added to cart!');
				});
			});
		});
	</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("outOfStockModal");
  const btn = document.getElementById("dismissModalBtn");

  // Only show modal if cookie is NOT set
  if (modal && !getCookie("hide_outofstock_modal")) {
    modal.style.display = "flex"; // or "block" depending on your styling
  }

  if (btn) {
    btn.addEventListener("click", function () {
      modal.style.display = "none";
      // Set cookie to hide for 1 day
      document.cookie = "hide_outofstock_modal=true; path=/; max-age=86400";
    });
  }

  // Helper function to get cookie by name
  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    return parts.length === 2 ? parts.pop().split(";").shift() : null;
  }
});
</script>





	</script>
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
	<script
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
	<script src="js/google-map.js"></script>
	<script src="js/main.js"></script>

</body>

</html>