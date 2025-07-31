<?php
session_start();

// Only accept POST data from the checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store POST data temporarily in session
    $_SESSION['mock_post'] = $_POST;

    // Optional: Set a flag to track that payment process started
    $_SESSION['order_initiated'] = true;
} else {
    // If user tries to access this directly, redirect to checkout
    header("Location: checkout.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Processing Payment...</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    /* (Your CSS styles here - unchanged) */
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to right, #f8f9fa, #e0e0e0);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      overflow: hidden;
    }

    .gateway-container {
      background: #ffffff;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 500px;
      width: 90%;
      z-index: 2;
      animation: fadeIn 1s ease-in-out;
    }

    .gateway-container h2 {
      font-weight: 700;
      color: #28a745;
      margin-bottom: 20px;
    }

    .loading-circle {
      width: 80px;
      height: 80px;
      border: 6px solid #28a745;
      border-top-color: transparent;
      border-radius: 50%;
      margin: 20px auto;
      animation: spin 1s linear infinite;
    }

    .card-icons {
      margin-top: 25px;
    }

    .card-icons img {
      height: 35px;
      margin: 0 10px;
      opacity: 0.9;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Background logo or pattern */
    .background-logo {
      position: absolute;
      bottom: -50px;
      right: -50px;
      opacity: 0.08;
      z-index: 1;
      width: 400px;
    }

    @media (max-width: 576px) {
      .gateway-container {
        padding: 30px 20px;
      }

      .card-icons img {
        height: 28px;
        margin: 5px;
      }
    }
  </style>

  <script>
    // Simulate payment processing delay
    setTimeout(function() {
      // Redirect to place-order.php which will finalize the order
      window.location.href = 'place-order.php?payment=success';
    }, 5000); // 5 seconds
  </script>
</head>
<body>

  <div class="gateway-container">
    <h2>Processing Your Payment...</h2>
    <div class="loading-circle"></div>
    <p>Please wait while we confirm your transaction.</p>

    <div class="card-icons">
      <img src="https://img.icons8.com/color/48/000000/visa.png" alt="Visa">
      <img src="https://img.icons8.com/color/48/000000/mastercard-logo.png" alt="MasterCard">
      <img src="https://img.icons8.com/color/48/000000/amex.png" alt="Amex">
      <img src="https://img.icons8.com/color/48/000000/paypal.png" alt="PayPal">
    </div>
  </div>

  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/49/Shop.svg/512px-Shop.svg.png" alt="Background Illustration" class="background-logo">

</body>
</html>
