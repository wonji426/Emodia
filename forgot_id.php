<!-- find_id.php -->
<?php
session_start();
$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

$showForm = true;
$foundId = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // 아이디 전체보기 버튼이 클릭된 경우
    if (isset($_POST['show_all_ids'])) {
        // POST로 데이터 전송
        echo "<form id='redirectForm' action='email_id.php' method='post'>
                <input type='hidden' name='name' value='{$name}'>
                <input type='hidden' name='email' value='{$email}'>
              </form>
              <script>
                document.getElementById('redirectForm').submit();
              </script>";
        exit();
    }

    $sql = "SELECT id FROM membertable WHERE name = ? AND email = ?";
    $stmt = $conn->prepare(query: $sql);
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $foundId = substr($id, 0, 4) . str_repeat("*", strlen($id) - 3);
        $showForm = false;
    } else {
        $errorMessage = '일치하는 정보가 없습니다.';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>아이디 찾기</title>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/forgot_id.css">
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
        <?php if ($showForm): ?>
            <div class="form-container">
                <h2>아이디 찾기</h2>
                <p>이름과 이메일을 입력하시면 아이디를 찾을 수 있습니다.</p>
                <?php if ($errorMessage): ?>
                    <p class="error-message"><?php echo $errorMessage; ?></p>
                <?php endif; ?>
                <form method="post">
                    <input type="text" name="name" placeholder="이름" required>
                    <input type="email" name="email" placeholder="이메일" required>
                    <input type="submit" value="아이디 찾기">
                    <button type="button" class="back-btn" onclick="window.location.href='login.php'">로그인 페이지로 돌아가기</button>
                </form>
            </div>
        <?php else: ?>
            <div class="result-container">
                <h2>아이디 찾기 결과</h2>
                <p>회원님의 아이디는</p>
                <div class="found-id"><?php echo $foundId; ?></div>
                <p>입니다.</p>
                <form method="post">
                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <button type="button" class="back-btn" onclick="window.location.href='login.php'">로그인 하러가기</button>
                    <input type="submit" name="show_all_ids" value="아이디 전체보기" class="back-btn">
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>