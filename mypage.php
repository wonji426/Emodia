<?php
session_start();
if (!isset($_SESSION["id"])) {
    echo "<script> alert('로그인이 필요합니다.');
        location.replace('login.php'); </script>";
    exit;
}

$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT name, email FROM membertable WHERE id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$user_name = $user_data['name'];
$user_email = $user_data['email'];

// 이메일 마스킹 함수
function maskEmail($email) {
    $parts = explode('@', $email);
    $name = $parts[0];
    $domain = $parts[1];
    
    $maskedName = substr($name, 0, 4) . str_repeat('*', strlen($name) - 4);
    $maskedDomain = substr($domain, 0, 2) . str_repeat('*', strlen($domain) - 4) . substr($domain, -3);
    
    return $maskedName . '@' . $maskedDomain;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 현재 비밀번호 확인
    $stmt = $conn->prepare("SELECT pw FROM membertable WHERE id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (isset($_POST["delete_account"])) {
        $user_id = $_SESSION['id'];
        $stmt = $conn->prepare("DELETE FROM membertable WHERE id = ?");
        $stmt->bind_param("s", $user_id);
        if ($stmt->execute()) {
            session_destroy();
            echo "<script>alert('회원 탈퇴가 완료되었습니다.'); window.location.href='login.php';</script>";
            exit;
        } else {
            echo "<script>alert('회원 탈퇴에 실패했습니다. 다시 시도해주세요.'); window.location.href='mypage.php';</script>";
        }
    }

    if (password_verify($current_password, $user['pw'])) {
        // 현재 비밀번호가 일치하는 경우
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE membertable SET pw = ? WHERE id = ?");
            $update_stmt->bind_param("ss", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                echo "<script>alert('비밀번호가 성공적으로 변경되었습니다.'); window.location.href='mypage.php';</script>";
            } else {
                echo "<script>alert('비밀번호 변경에 실패했습니다. 다시 시도해주세요.'); window.location.href='mypage.php';</script>";
            }
        } else {
            echo "<script>alert('새 비밀번호와 확인 비밀번호가 일치하지 않습니다.'); window.location.href='mypage.php';</script>";
        }
    } else {
        echo "<script>alert('현재 비밀번호가 일치하지 않습니다.'); window.location.href='mypage.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="Title" content="감정을 읽는 미디어 추천 사이트">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <link rel="stylesheet" href="./css/mypage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <script src="./js/mypage.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>마이페이지</title>
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
        <h1>마이페이지</h1>

        <div class="user-id-display">
            <p>환영합니다, <?php echo $user_name; ?>님!</p>
            <small>@<?php echo $_SESSION['id']; ?></small>
        </div>

        <h2>이메일 관리</h2>
        <div class="email-section">
            <div class="email-display-container">
                <p class="masked-email">현재 이메일: <?php echo maskEmail($user_email); ?></p>
                <form action="change_email.php" method="get">
                    <input type="submit" name="change-email-btn" value="이메일 변경하기" class="email-submit-btn">
                </form>
            </div>
        </div>

        <h2>비밀번호 변경</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="password-container">
                <input type="password" id="current_password-a" name="current_password" placeholder="현재 비밀번호" required>
                <span id="togglePassword-a" class="toggle-password">
                    <i class="far fa-eye" id="eyeIcon-a"></i>
                </span>
            </div>
            <div class="password-container">
                <input type="password" id="new_password-b" name="new_password" placeholder="새 비밀번호" required>
                <span id="togglePassword-b" class="toggle-password">
                    <i class="far fa-eye" id="eyeIcon-b"></i>
                </span>
            </div>
            <div class="password-container">
                <input type="password" id="confirm_password-c" name="confirm_password" placeholder="새 비밀번호 확인" required>
                <span id="togglePassword-c" class="toggle-password">
                    <i class="far fa-eye" id="eyeIcon-c"></i>
                </span>
            </div>
            <input type="submit" name="change_password" value="비밀번호 변경">
        </form>

        <h2>감정에 따른 선호 장르 변경</h2>
        <form action="change_genre.php" method="get">
            <input type="submit" value="변경 페이지로 이동" name="change-genre-btn">
        </form>

        <h2>회원 탈퇴</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return confirm('정말로 탈퇴하시겠습니까?');">
            <input type="submit" name="delete_account" value="회원 탈퇴">
        </form>
    </div>
</body>

</html>

<?php
mysqli_close($conn);
?>