<?php
session_start();

$change_password_message = '';
if (isset($_SESSION['temp_password_set']) && $_SESSION['temp_password_set']) {
    $change_password_message = "임시 비밀번호를 발급하셨습니다. 보안을 위해 로그인 후 비밀번호를 변경해주세요.";
    unset($_SESSION['temp_password_set']); // 메시지를 표시한 후 세션 변수 제거
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Emodia 로그인</title>
    <link rel="stylesheet" type="text/css" href="./css/login.css">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <meta name="Title" content="감정을 읽는 미디어 추천 사이트">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <script src="./js/login.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <form name="login" id="loginBox" method="get" action="loginCheck.php">
        <h1>로그인</h1>
        <?php if ($change_password_message): ?>
            <p class="change-password-message"><?php echo htmlspecialchars($change_password_message); ?></p>
        <?php endif; ?>
        아이디<input type="text" name="id"><br>
        <div class="password-container">
            비밀번호<input type="password" id="passwordInput" name="pw">
            <span id="togglePassword" class="toggle-password">
                <i class="far fa-eye" id="eyeIcon"></i>
            </span><br>
        </div>
        <label>
            <input type="checkbox" name="auto_login" value="true"> 로그인 유지
        </label>
        <input type="submit" value="로그인">
        <button type="button" onClick="location.href='signUp.php'">회원가입</button>
        <div class="button-group">
            <button type="button" id="findIdBtn">아이디 찾기</button>
            <button type="button" id="findPasswordBtn">비밀번호 찾기</button>
        </div>
    </form>
    <!-- 비밀번호 찾기 옵션 -->
    <div id="passwordRecoveryOptions" style="display: none;">
        <h1>비밀번호 찾기</h1>
        <button type="button" id="securityQuestionBtn">보안질문으로 찾기</button>
        <button type="button" id="emailRecoveryBtn">이메일로 찾기</button>
        <button type="button" id="backToLoginBtn">로그인으로 돌아가기</button>
    </div>
</body>

</html>