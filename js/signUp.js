document.addEventListener('DOMContentLoaded', function() {
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

document.addEventListener('DOMContentLoaded', function() {
    // 비밀번호 유효성 검사 함수
    function validatePassword(password) {
        const regex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[0-9])(?=.*[a-z]).{8,}$/;
        return regex.test(password);
    }

    // 비밀번호 입력 필드에 이벤트 리스너 추가
    const passwordInput = document.querySelector('#passwordInput'); // ID 선택자 사용 예시

    // 비밀번호 입력 필드에 이벤트 리스너 추가
    passwordInput.addEventListener('input', function() {
        if (validatePassword(this.value)) {
            this.setCustomValidity('');
            this.style.borderColor = '#4CAF50'; // 유효할 때 초록색
            this.style.backgroundColor = '#E8F5E9'; // 옅은 초록색 배경
        } else {
            this.setCustomValidity('비밀번호는 8자 이상이어야 하며, 영문 대문자, 특수문자, 숫자를 각각 1개 이상 포함해야 합니다.');
            this.style.borderColor = '#FF0000'; // 유효하지 않을 때 빨간색
            this.style.backgroundColor = '#FFEBEE'; // 옅은 빨간색 배경
        }
    });
});

// 기존의 checkCustomQuestion 함수는 그대로 유지
function checkCustomQuestion(selectElement) {
    var customInput = document.getElementById('custom_security_question');
    if (selectElement.value === 'custom') {
        customInput.style.display = 'block';
        customInput.required = true;
    } else {
        customInput.style.display = 'none';
        customInput.required = false;
    }
}