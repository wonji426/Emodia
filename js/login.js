document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});

$(document).ready(function () {
    $("#loginForm").submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "loginCheck.php",
            data: $(this).serialize(),
            success: function (response) {
                if (response == "success") {
                    window.location.href = "index.php";
                } else if (response == "wrong_password") {
                    alert("비밀번호가 일치하지 않습니다.");
                } else {
                    alert("존재하지 않는 사용자입니다.");
                }
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const loginBox = document.getElementById('loginBox');
    const passwordRecoveryOptions = document.getElementById('passwordRecoveryOptions');
    const findIdBtn = document.getElementById('findIdBtn');
    const findPasswordBtn = document.getElementById('findPasswordBtn');
    const backToLoginBtn = document.getElementById('backToLoginBtn');
    const securityQuestionBtn = document.getElementById('securityQuestionBtn');
    const emailRecoveryBtn = document.getElementById('emailRecoveryBtn');

    // 아이디 찾기 버튼 클릭
    findIdBtn.addEventListener('click', function () {
        window.location.href = 'forgot_id.php';
    });

    // 비밀번호 찾기 버튼 클릭
    findPasswordBtn.addEventListener('click', function () {
        loginBox.style.display = 'none';
        passwordRecoveryOptions.style.display = 'block';
    });

    // 로그인으로 돌아가기 버튼 클릭
    backToLoginBtn.addEventListener('click', function () {
        passwordRecoveryOptions.style.display = 'none';
        loginBox.style.display = 'block';
    });

    // 보안질문으로 찾기 버튼 클릭
    securityQuestionBtn.addEventListener('click', function () {
        // 보안질문 페이지로 이동하는 로직 구현
        window.location.href = 'forgot_password.php';
    });

    // 이메일로 찾기 버튼 클릭
    emailRecoveryBtn.addEventListener('click', function () {
        // 이메일 인증 페이지로 이동하는 로직 구현
        window.location.href = 'email.php';
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
