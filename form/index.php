<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modern Login Page</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="index.css">
  <!-- Google Identity Services API -->
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script src="google-auth.js" defer></script>

</head>

<body>
  <?php include("../includes/db.inc.php"); ?>

  <div class="container" id="container">

    <!-- SIGN UP FORM -->
    <div class="form-container sign-up" id="signup-redirect">
      <form action="../includes/signup.php" method="post">
        <h1>Create Account</h1>
        <div class="social-icons">
          <div id="g_id_signup" class="g_id_signup"></div>
          <!-- <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
          <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
        </div>
        <span>or use your email for registration</span>
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="role" required>
          <option value="" disabled selected>Select Role</option>
          <option value="employee">Employee</option>
          <option value="customer">Customer</option>
        </select>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register_btn">Sign Up</button>
        <!-- <p class="mobile-toggle-link">Already have an account? <a href="#login-redirect" id="mobileToggleSignin">Sign In</a></p> -->
      </form>
    </div>

    <!-- SIGN IN FORM -->
    <div class="form-container sign-in" id="login-redirect">
      <form action="../includes/login.php" method="post">
        <h1>Sign In</h1>
        <div class="social-icons">
          <div id="g_id_signin" class="icon"></div>

          <!-- <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
          <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
        </div>
        <span>or use your email password</span>
        <input type="email" name="email" placeholder="Email" required>
        <select name="role" required>
          <option value="" disabled selected>Select Role</option>
          <option value="admin">Admin</option>
          <option value="employee">Employee</option>
          <option value="customer">Customer</option>
        </select>
        <input type="password" name="password" placeholder="Password" required>
        <a href="#">Forgot Your Password?</a>
        <button type="submit" name="login_btn" value="Sign In">Sign In</button>
        <!-- <p class="mobile-toggle-link">Don’t have an account? <a href="#signup-redirect" id="mobileToggleSignup">Sign Up</a></p> -->
      </form>
    </div>

    <div class="toggle-container">
      <div class="toggle">
        <div class="toggle-panel toggle-left">
          <h1>Welcome Back!</h1>
          <p>Enter your personal details to use all of site features</p>
          <button class="hidden" id="login">Sign In</button>
        </div>
        <div class="toggle-panel toggle-right">
          <h1>Hello, Friend!</h1>
          <p>Register with your personal details to use all of site features</p>
          <button class="hidden" id="register">Sign Up</button>
        </div>
      </div>
    </div>

  </div>

  <script src="script.js"></script>
  <div id="toast" style="display:none; position: fixed; bottom: 20px; right: 20px; background: #333; color: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.2); z-index: 9999;">
    <span id="toastMessage"></span>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const msg = localStorage.getItem("toastMessage");
    if (msg) {
      const toast = document.getElementById("toast");
      const toastMessage = document.getElementById("toastMessage");
      toastMessage.textContent = msg;
      toast.style.display = "block";

      // Hide after 3 seconds
      setTimeout(() => {
        toast.style.display = "none";
        localStorage.removeItem("toastMessage");
      }, 3000);
    }
  });
</script>

</body>

</html>