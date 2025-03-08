document.addEventListener('DOMContentLoaded', function () {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    const body = document.body;

    navbarToggle.addEventListener('click', function () {
        navbarMenu.classList.toggle('active');
        if (navbarMenu.classList.contains('active')) {
            body.style.paddingTop = navbarMenu.offsetHeight + 60 + 'px';
        } else {
            body.style.paddingTop = '60px';
        }
    });
});