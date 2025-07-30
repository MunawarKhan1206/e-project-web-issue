
  const toggle = document.getElementById('menu-toggle');
  const sidebar = document.querySelector('.sidebar');

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });

  // Close sidebar if clicked outside on small screens
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
      const isClickInsideSidebar = sidebar.contains(e.target);
      const isClickOnToggle = toggle.contains(e.target);

      if (!isClickInsideSidebar && !isClickOnToggle) {
        sidebar.classList.remove('active');
      }
    }
  });