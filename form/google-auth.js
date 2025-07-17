window.onload = function () {
  const clientId = "547746583380-r0a21nabutv0jrurr3152ufnbqokvbo6.apps.googleusercontent.com";

  google.accounts.id.initialize({
    client_id: clientId,
    callback: handleGoogleCredential,
    auto_select: false,
    context: "signin"
  });

  // Sign In button
  const signinDiv = document.getElementById("g_id_signin");
  if (signinDiv) {
    google.accounts.id.renderButton(signinDiv, {
      theme: "outline",
      size: "large",
      text: "continue_with"
    });
  }

  // Sign Up button
  const signupDiv = document.getElementById("g_id_signup");
  if (signupDiv) {
    google.accounts.id.renderButton(signupDiv, {
      theme: "outline",
      size: "large",
      text: "signup_with"
    });
  }
};

function handleGoogleCredential(response) {
  fetch("../includes/google-auth.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_token: response.credential })
  })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      // Optionally redirect
      // window.location.href = "dashboard.php";
    })
    .catch(err => console.error(err));
}
