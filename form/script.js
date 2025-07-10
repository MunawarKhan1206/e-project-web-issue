
    const container = document.getElementById('container');
    const registerBtn = document.getElementById('register');
    const loginBtn = document.getElementById('login');
    const mobileToggleLinks = document.querySelectorAll('#mobileToggle');

    registerBtn.addEventListener('click', () => {
        container.classList.add("active");
    });

    loginBtn.addEventListener('click', () => {
        container.classList.remove("active");
    });

    mobileToggleLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.toggle("active");
        });
    });

