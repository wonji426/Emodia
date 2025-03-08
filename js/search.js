document.addEventListener('DOMContentLoaded', function () {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    const body = document.body;

    navbarToggle.addEventListener('click', function () {
        navbarMenu.classList.toggle('active');
    });
});