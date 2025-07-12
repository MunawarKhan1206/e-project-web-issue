window.onload = function () {
  const clientId = "547746583380-r0a21nabutv0jrurr3152ufnbqokvbo6.apps.googleusercontent.com";

  // Render Google Sign-In for Login
  google.accounts.id.initialize({
    client_id: clientId,
    callback: handleGoogleCredential,
    auto_select: false,
    context: "signin"
  });

  google.accounts.id.renderButton(
    document.getElementById("g_id_signin"),
    { theme: "outline", size: "large", text: "continue_with" }
  );

  // Render Google Sign-Up button
  google.accounts.id.renderButton(
    document.getElementById("g_id_signup"),
    { theme: "outline", size: "large", text: "signup_with" }
  );
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
      // window.location.href = "dashboard.php"; // Optional
    })
    .catch(err => console.error(err));
}
