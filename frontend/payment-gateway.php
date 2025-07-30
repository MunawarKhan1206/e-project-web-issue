
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['first_name'])) {
    header("Location: checkout.php");
    exit;
}

// Save billing details in session temporarily
$_SESSION['billing'] = $_POST;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Payment Gateway</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f9f9f9;
        }
        .card-box {
            background: white;
            padding: 30px;
            max-width: 500px;
            margin: 80px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="card-box">
        <h4 class="mb-4">Enter Card Details</h4>
        <form method="POST" action="confirm_payment.php">
            <div class="form-group">
                <label>Cardholder Name</label>
                <input type="text" name="card_name" required class="form-control" placeholder="John Doe">
            </div>
            <div class="form-group">
                <label>Card Number</label>
                <input type="text" name="card_number" required class="form-control" placeholder="4111 1111 1111 1111" pattern="\d{16}">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Expiry (MM/YY)</label>
                    <input type="text" name="expiry" required class="form-control" placeholder="12/28" pattern="\d{2}/\d{2}">
                </div>
                <div class="form-group col-md-6">
                    <label>CVV</label>
                    <input type="password" name="cvv" required class="form-control" maxlength="4" pattern="\d{3,4}">
                </div>
            </div>
            <button class="btn btn-success btn-block">Pay Now</button>
        </form>
    </div>
</body>
</html>
