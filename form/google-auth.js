function initGoogleLogin() {
  const clientId = "547746583380-r0a21nabutv0jrurr3152ufnbqokvbo6.apps.googleusercontent.com";

  google.accounts.id.initialize({
    client_id: clientId,
    callback: handleCredentialResponse
  });

  const loginBtn1 = document.getElementById("googleLoginSignin");
  const loginBtn2 = document.getElementById("googleLoginSignup");

  function triggerGoogleLogin(e) {
    e.preventDefault();

    google.accounts.id.prompt((notification) => {
      if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
        console.log("Google prompt was skipped or blocked. Trying fallback...");
        // You can add a fallback like redirect here if needed
      }
    });
  }

  if (loginBtn1) loginBtn1.addEventListener("click", triggerGoogleLogin);
  if (loginBtn2) loginBtn2.addEventListener("click", triggerGoogleLogin);
}

function handleCredentialResponse(response) {
  fetch("../includes/google-auth.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_token: response.credential })
  })
    .then(res => res.text())
    .then(msg => alert(msg))
    .catch(err => console.error(err));
}

// 🔁 Wait until Google is loaded before initializing
let checkGoogle = setInterval(() => {
  if (window.google && google.accounts && google.accounts.id) {
    clearInterval(checkGoogle);
    initGoogleLogin();
  }
}, 200);
