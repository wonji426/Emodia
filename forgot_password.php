<?php
session_start();
$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$show_security_question = false;
$user_id = '';

$show_forgot_password_main = true; // 기본적으로 forgot-password-main을 표시

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF 토큰 검증 실패');
    }

    if (isset($_POST['step']) && $_POST['step'] == 'find_account') {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

        try {
            $stmt = $mysqli->prepare("SELECT no, id, security_question FROM membertable WHERE id = ?");
            if (!$stmt) {
                throw new Exception("쿼리 준비 실패: " . $mysqli->error);
            }
            $stmt->bind_param('s', $id);
            if (!$stmt->execute()) {
                throw new Exception("쿼리 실행 실패: " . $mysqli->error);
            }
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception("결과 가져오기 실패: " . $mysqli->error);
            }
            $user = $result->fetch_assoc();

            if ($user) {
                $show_security_question = true;
                $user_id = $user['id'];
                $_SESSION['reset_user_no'] = $user['no'];
                $security_question = $user['security_question'];
            } else {
                $show_login_button = false;
                $message = '해당 아이디를 찾을 수 없습니다.';
            }
        } catch (Exception $e) {
            error_log("계정 찾기 오류: " . $e->getMessage());
            $message = '오류가 발생했습니다. 나중에 다시 시도해주세요.';
        }
    } elseif (isset($_POST['step']) && $_POST['step'] == 'verify_answer') {
        $answer = filter_input(INPUT_POST, 'security_answer', FILTER_SANITIZE_STRING);
        $user_no = $_SESSION['reset_user_no'] ?? 0;

        try {
            $stmt = $mysqli->prepare("SELECT id FROM membertable WHERE no = ? AND security_answer = ?");
            if (!$stmt) {
                throw new Exception("쿼리 준비 실패: " . $mysqli->error);
            }
            $stmt->bind_param('is', $user_no, $answer);
            if (!$stmt->execute()) {
                throw new Exception("쿼리 실행 실패: " . $mysqli->error);
            }
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception("결과 가져오기 실패: " . $mysqli->error);
            }
            $user = $result->fetch_assoc();

            if ($user) {
                $new_password = bin2hex(random_bytes(8));
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $update_stmt = $mysqli->prepare("UPDATE membertable SET pw = ? WHERE no = ?");
                if (!$update_stmt) {
                    throw new Exception("쿼리 준비 실패: " . $mysqli->error);
                }
                $update_stmt->bind_param('si', $hashed_password, $user_no);
                if (!$update_stmt->execute()) {
                    throw new Exception("쿼리 실행 실패: " . $mysqli->error);
                }

                $message = "임시 비밀번호가 생성되었습니다: $new_password";
                $show_login_button = true;
                $show_forgot_password_main = false;

                $_SESSION['temp_password_set'] = true;
            } else {
                $message = '보안 질문의 답변이 일치하지 않습니다.';
                $show_login_button = false;
            }
        } catch (Exception $e) {
            error_log("비밀번호 재설정 오류: " . $e->getMessage());
            $message = '오류가 발생했습니다. 나중에 다시 시도해주세요.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <link rel="stylesheet" href="./css/forgot_password.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggle = document.querySelector('.navbar-toggle');
            const navbarMenu = document.querySelector('.navbar-menu');
            const body = document.body;

            navbarToggle.addEventListener('click', function() {
                navbarMenu.classList.toggle('active');
            });
        });
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
    <title>비밀번호 찾기</title>
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
                <li><a href="index.php">홈으로</a></li>
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
    <div class="container">
        <h2>비밀번호 찾기</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php if ($show_login_button): ?>
                <a href="login.php" class="login-button" onclick="return redirectToLogin()">로그인 페이지로 이동</a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($show_forgot_password_main): ?>
            <div class="forgot-password-main">
                <?php if (!$show_security_question): ?>
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="step" value="find_account">
                        <input type="text" name="id" placeholder="아이디" required>
                        <input type="submit" value="계정 확인">
                        <button class="back-btn" type="button" onClick="location.href='login.php'">돌아가기</button>
                    </form>
                <?php elseif ($show_security_question): ?>
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="step" value="verify_answer">
                        <p>아이디: <?php echo htmlspecialchars($user_id); ?></p>
                        <p>보안 질문: <?php echo htmlspecialchars($security_question); ?></p>
                        <input type="text" name="security_answer" placeholder="보안 질문 답변" required>
                        <input type="submit" value="비밀번호 재설정">
                        <button class="back-btn" type="button" onClick="location.href='login.php'">돌아가기</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>