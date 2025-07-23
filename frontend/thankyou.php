<?php
session_start();
$name = isset($_SESSION['recipient_name']) ? $_SESSION['recipient_name'] : 'Dear Customer';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Thank You - Minishop</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    body {
      background: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      font-family: 'Open Sans', sans-serif;
    }

    .thankyou-box {
      background: white;
      padding: 40px;
      text-align: center;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      width: 100%;
      animation: fadeInUp 1s ease-out;
    }

    .thankyou-box h1 {
      font-size: 32px;
      color: #28a745;
      margin-bottom: 20px;
    }

    .thankyou-box p {
      font-size: 18px;
      margin-bottom: 30px;
    }

    .thankyou-box .btn {
      padding: 10px 25px;
      border-radius: 30px;
    }

    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }
  </style>

  <script>
    setTimeout(function() {
      window.location.href = 'index.php';
    }, 5000);
  </script>
</head>

<body>
  <div class="thankyou-box">
    <h1>🎉 Thank You, <?= htmlspecialchars($name) ?>!</h1>
    <p>Your order has been placed successfully.<br>We're preparing your gift now!</p>
    <a href="index.php" class="btn btn-success">Back to Home</a>
  </div>
</body>
</html>
