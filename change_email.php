<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['new_email'];
    $password_confirm = $_POST['password_confirm']; // 해시하지 않은 원본 비밀번호
    $user_id = $_SESSION['id'];

    // 비밀번호 확인
    $stmt = $conn->prepare("SELECT pw FROM membertable WHERE id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // 원본 비밀번호와 저장된 해시 비밀번호를 비교
    if ($user && password_verify($password_confirm, $user['pw'])) {
        // 이메일 업데이트
        $update_stmt = $conn->prepare("UPDATE membertable SET email = ? WHERE id = ?");
        $update_stmt->bind_param("ss", $new_email, $user_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('이메일이 성공적으로 변경되었습니다.'); window.location.href='mypage.php';</script>";
        } else {
            echo "<script>alert('이메일 변경에 실패했습니다.'); window.location.href='change_email.php';</script>";
        }
    } else {
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); window.location.href='change_email.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="Title" content="감정을 읽는 미디어 추천 사이트">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <link rel="stylesheet" href="./css/mypage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>이메일 변경</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailForm = document.querySelector('.email-form');
            if (emailForm) {
                emailForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const newEmail = this.querySelector('input[name="new_email"]').value;
                    const passwordConfirm = this.querySelector('input[name="password_confirm"]').value;

                    if (!newEmail || !passwordConfirm) {
                        alert('모든 필드를 입력해주세요.');
                        return;
                    }

                    // 이메일 형식 검증
                    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    if (!emailRegex.test(newEmail)) {
                        alert('올바른 이메일 형식이 아닙니다.');
                        return;
                    }

                    this.submit();
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggle = document.querySelector('.navbar-toggle');
            const navbarMenu = document.querySelector('.navbar-menu');
            const body = document.body;

            navbarToggle.addEventListener('click', function() {
                navbarMenu.classList.toggle('active');
                if (navbarMenu.classList.contains('active')) {
                    body.style.paddingTop = navbarMenu.offsetHeight + 60 + 'px';
                } else {
                    body.style.paddingTop = '60px';
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const current_password = document.getElementById('current_password');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function() {
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
    </script>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-logo">
                <a href="index.php">Emodia</a>
            </div>
            <button class="navbar-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <ul class="navbar-menu">
                <li><a href="search.php">영화 검색</a></li>
                <?php if (isset($_SESSION["id"])): ?>
                    <li><a href="mypage.php" class="user-id">마이페이지</a></li>
                    <li><a href="logout.php">로그아웃</a></li>
                <?php else: ?>
                    <li><a href="login.php">로그인</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="mypage-container">
        <h1>이메일 변경</h1>
        <div class="email-display-container">
            <form class="email-form" action="" method="post">
                <div class="form-group">
                    <input type="email" name="new_email" class="email-input" placeholder="새로운 이메일" required>
                </div>
                <div class="form-group" id="password-container">
                    <input type="password" id="current_password" name="password_confirm" class="email-input" placeholder="비밀번호 확인" required>
                    <span id="togglePassword" class="toggle-password">
                        <i class="far fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
                <input type="submit" value="이메일 변경" class="email-submit-btn">
            </form>
        </div>
    </div>
</body>

</html>