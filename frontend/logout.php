<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging Out...</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="refresh" content="3;url=../frontend/index.php">
  <style>
    body {
      background: #f7f6f3;
      font-family: 'Inter', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
      color: #3a2f27;
    }

    .logout-box {
      text-align: center;
      padding: 30px 20px;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      max-width: 400px;
      width: 100%;
      animation: fadeIn 0.8s ease-in-out;
    }

    .logout-box h2 {
      font-size: 22px;
      margin-bottom: 15px;
    }

    .logout-box p {
      font-size: 16px;
      color: #555;
    }

    .spinner {
      margin: 20px auto;
      width: 40px;
      height: 40px;
      border: 4px solid #e0e0e0;
      border-top: 4px solid #c29958;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 480px) {
      .logout-box {
        padding: 20px 15px;
      }

      .logout-box h2 {
        font-size: 20px;
      }

      .logout-box p {
        font-size: 14px;
      }

      .spinner {
        width: 35px;
        height: 35px;
      }
    }
  </style>
</head>
<body>
  <div class="logout-box">
    <h2>You’ve been logged out</h2>
    <div class="spinner"></div>
    <p>Redirecting to login page...</p>
  </div>
</body>
</html>
