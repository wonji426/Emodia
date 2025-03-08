document.addEventListener('DOMContentLoaded', function() {

    // 비밀번호 유효성 검사 함수
    function validatePassword(password) {
        const regex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[0-9])(?=.*[a-z]).{8,}$/;
        return regex.test(password);
    }

    // 비밀번호 입력 필드에 이벤트 리스너 추가
    new_password.addEventListener('input', function() {
        if (validatePassword(this.value)) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity('비밀번호는 8자 이상이어야 하며, 영문 대문자, 특수문자, 숫자를 각각 1개 이상 포함해야 합니다.');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword-b');
    const new_password = document.getElementById('new_password-b');
    const eyeIcon = document.getElementById('eyeIcon-b');

    togglePassword.addEventListener('click', function () {
        if (new_password.type === 'password') {
            new_password.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            new_password.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword-c');
    const confirm_password = document.getElementById('confirm_password-c');
    const eyeIcon = document.getElementById('eyeIcon-c');

    togglePassword.addEventListener('click', function () {
        if (confirm_password.type === 'password') {
            confirm_password.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            confirm_password.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword-a');
    const current_password = document.getElementById('current_password-a');
    const eyeIcon = document.getElementById('eyeIcon-a');

    togglePassword.addEventListener('click', function () {
        if (current_password.type === 'password') {
            current_password.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            current_password.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    const body = document.body;

    navbarToggle.addEventListener('click', function () {
        navbarMenu.classList.toggle('active');
    });
});