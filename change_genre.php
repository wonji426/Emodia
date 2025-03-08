<?php
session_start();
$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

// 사용자가 로그인되어 있는지 확인
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// 사용자의 감정별 장르 가져오기
$user_id = $_SESSION['id'];
$sql = "SELECT happy_genre, angry_genre, bored_genre, sad_genre,happy_genre_music, angry_genre_music, bored_genre_music, sad_genre_music FROM membertable WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $happy_genre = $_POST['happy_genre'];
    $angry_genre = $_POST['angry_genre'];
    $bored_genre = $_POST['bored_genre'];
    $sad_genre = $_POST['sad_genre'];
    $happy_genre_music = $_POST['happy_genre_music'];
    $angry_genre_music = $_POST['angry_genre_music'];
    $bored_genre_music = $_POST['bored_genre_music'];
    $sad_genre_music = $_POST['sad_genre_music'];

    // 데이터베이스 업데이트 쿼리
    $sql = "UPDATE membertable SET 
            happy_genre = ?, 
            angry_genre = ?, 
            bored_genre = ?, 
            sad_genre = ?,
            happy_genre_music = ?,
            angry_genre_music = ?,
            bored_genre_music = ?,
            sad_genre_music = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssss",
        $happy_genre,
        $angry_genre,
        $bored_genre,
        $sad_genre,
        $happy_genre_music,
        $angry_genre_music,
        $bored_genre_music,
        $sad_genre_music,
        $user_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('선호 장르가 성공적으로 업데이트되었습니다.'); window.location.href='mypage.php';</script>";
    } else {
        echo "<script>alert('오류가 발생했습니다. 다시 시도해주세요.');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>감정에 따른 장르 변경</title>
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="./js/change_genre.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/change_genre.css">
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
                <li><a href="search.php">영화 검색</a></li>
                <?php if (isset($_SESSION["id"])): ?>
                    <li><a href="mypage.php">마이페이지</a></li>
                    <li><a href="logout.php">로그아웃</a></li>
                <?php else: ?>
                    <li><a href="login.php">로그인</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <form name="update" method="post">
        <div>
            <h2>감정 및 선호하는 영화 장르</h2>

            <div id="emotion-movie" class="collapsible">
                <label for="happy_genre">행복할 때 선호하는 영화 장르:</label>
                <select name="happy_genre" id="happy_genre" required>
                    <option value="comedy" <?php echo ($user['happy_genre'] == 'comedy') ? 'selected' : ''; ?>> 코미디</option>
                    <option value="romance" <?php echo ($user['happy_genre'] == 'romance') ? 'selected' : ''; ?>> 로맨스</option>
                    <option value="musical" <?php echo ($user['happy_genre'] == 'musical') ? 'selected' : ''; ?>> 뮤지컬</option>
                    <option value="adventure" <?php echo ($user['happy_genre'] == 'adventure') ? 'selected' : ''; ?>> 모험</option>
                    <option value="action" <?php echo ($user['happy_genre'] == 'action') ? 'selected' : ''; ?>> 액션</option>
                    <option value="thriller" <?php echo ($user['happy_genre'] == 'thriller') ? 'selected' : ''; ?>> 스릴러</option>
                    <option value="crime" <?php echo ($user['happy_genre'] == 'crime') ? 'selected' : ''; ?>> 범죄</option>
                    <option value="war" <?php echo ($user['happy_genre'] == 'war') ? 'selected' : ''; ?>> 전쟁</option>
                    <option value="SF" <?php echo ($user['happy_genre'] == 'SF') ? 'selected' : ''; ?>> SF</option>
                    <option value="fantasy" <?php echo ($user['happy_genre'] == 'fantasy') ? 'selected' : ''; ?>> 판타지</option>
                    <option value="mystery" <?php echo ($user['happy_genre'] == 'mystery') ? 'selected' : ''; ?>> 미스터리</option>
                    <option value="documentary" <?php echo ($user['happy_genre'] == 'documentary') ? 'selected' : ''; ?>> 다큐멘터리</option>
                    <option value="drama" <?php echo ($user['happy_genre'] == 'drama') ? 'selected' : ''; ?>> 드라마</option>
                    <option value="melodrama" <?php echo ($user['happy_genre'] == 'melodrama') ? 'selected' : ''; ?>> 멜로드라마</option>
                    <option value="animation" <?php echo ($user['happy_genre'] == 'animation') ? 'selected' : ''; ?>> 애니메이션</option>
                    <option value="indie" <?php echo ($user['happy_genre'] == 'indie') ? 'selected' : ''; ?>> 인디</option>
                </select><br>

                <label for="angry_genre">화날 때 선호하는 영화 장르 : </label>
                <select name="angry_genre" id="angry_genre" required>
                    <option value="comedy" <?php echo ($user['angry_genre'] == 'comedy') ? 'selected' : ''; ?>> 코미디</option>
                    <option value="romance" <?php echo ($user['angry_genre'] == 'romance') ? 'selected' : ''; ?>> 로맨스</option>
                    <option value="musical" <?php echo ($user['angry_genre'] == 'musical') ? 'selected' : ''; ?>> 뮤지컬</option>
                    <option value="adventure" <?php echo ($user['angry_genre'] == 'adventure') ? 'selected' : ''; ?>> 모험</option>
                    <option value="action" <?php echo ($user['angry_genre'] == 'action') ? 'selected' : ''; ?>> 액션</option>
                    <option value="thriller" <?php echo ($user['angry_genre'] == 'thriller') ? 'selected' : ''; ?>> 스릴러</option>
                    <option value="crime" <?php echo ($user['angry_genre'] == 'crime') ? 'selected' : ''; ?>> 범죄</option>
                    <option value="war" <?php echo ($user['angry_genre'] == 'war') ? 'selected' : ''; ?>> 전쟁</option>
                    <option value="SF" <?php echo ($user['angry_genre'] == 'SF') ? 'selected' : ''; ?>> SF</option>
                    <option value="fantasy" <?php echo ($user['angry_genre'] == 'fantasy') ? 'selected' : ''; ?>> 판타지</option>
                    <option value="mystery" <?php echo ($user['angry_genre'] == 'mystery') ? 'selected' : ''; ?>> 미스터리</option>
                    <option value="documentary" <?php echo ($user['angry_genre'] == 'documentary') ? 'selected' : ''; ?>> 다큐멘터리</option>
                    <option value="drama" <?php echo ($user['angry_genre'] == 'drama') ? 'selected' : ''; ?>> 드라마</option>
                    <option value="melodrama" <?php echo ($user['angry_genre'] == 'melodrama') ? 'selected' : ''; ?>> 멜로드라마</option>
                    <option value="animation" <?php echo ($user['angry_genre'] == 'animation') ? 'selected' : ''; ?>> 애니메이션</option>
                    <option value="indie" <?php echo ($user['angry_genre'] == 'indie') ? 'selected' : ''; ?>> 인디</option>
                </select><br>

                <label for="bored_genre">지루할 때 선호하는 영화 장르:</label>
                <select name="bored_genre" id="bored_genre" required>
                    <option value="comedy" <?php echo ($user['bored_genre'] == 'comedy') ? 'selected' : ''; ?>> 코미디</option>
                    <option value="romance" <?php echo ($user['bored_genre'] == 'romance') ? 'selected' : ''; ?>> 로맨스</option>
                    <option value="musical" <?php echo ($user['bored_genre'] == 'musical') ? 'selected' : ''; ?>> 뮤지컬</option>
                    <option value="adventure" <?php echo ($user['bored_genre'] == 'adventure') ? 'selected' : ''; ?>> 모험</option>
                    <option value="action" <?php echo ($user['bored_genre'] == 'action') ? 'selected' : ''; ?>> 액션</option>
                    <option value="thriller" <?php echo ($user['bored_genre'] == 'thriller') ? 'selected' : ''; ?>> 스릴러</option>
                    <option value="crime" <?php echo ($user['bored_genre'] == 'crime') ? 'selected' : ''; ?>> 범죄</option>
                    <option value="war" <?php echo ($user['bored_genre'] == 'war') ? 'selected' : ''; ?>> 전쟁</option>
                    <option value="SF" <?php echo ($user['bored_genre'] == 'SF') ? 'selected' : ''; ?>> SF</option>
                    <option value="fantasy" <?php echo ($user['bored_genre'] == 'fantasy') ? 'selected' : ''; ?>> 판타지</option>
                    <option value="mystery" <?php echo ($user['bored_genre'] == 'mystery') ? 'selected' : ''; ?>> 미스터리</option>
                    <option value="documentary" <?php echo ($user['bored_genre'] == 'documentary') ? 'selected' : ''; ?>> 다큐멘터리</option>
                    <option value="drama" <?php echo ($user['bored_genre'] == 'drama') ? 'selected' : ''; ?>> 드라마</option>
                    <option value="melodrama" <?php echo ($user['bored_genre'] == 'melodrama') ? 'selected' : ''; ?>> 멜로드라마</option>
                    <option value="animation" <?php echo ($user['bored_genre'] == 'animation') ? 'selected' : ''; ?>> 애니메이션</option>
                    <option value="indie" <?php echo ($user['bored_genre'] == 'indie') ? 'selected' : ''; ?>> 인디</option>
                </select><br>

                <label for="sad_genre">슬플 때 선호하는 영화 장르 : </label>
                <select name="sad_genre" id="sad_genre" required>
                    <option value="comedy" <?php echo ($user['sad_genre'] == 'comedy') ? 'selected' : ''; ?>> 코미디</option>
                    <option value="romance" <?php echo ($user['sad_genre'] == 'romance') ? 'selected' : ''; ?>> 로맨스</option>
                    <option value="musical" <?php echo ($user['sad_genre'] == 'musical') ? 'selected' : ''; ?>> 뮤지컬</option>
                    <option value="adventure" <?php echo ($user['sad_genre'] == 'adventure') ? 'selected' : ''; ?>> 모험</option>
                    <option value="action" <?php echo ($user['sad_genre'] == 'action') ? 'selected' : ''; ?>> 액션</option>
                    <option value="thriller" <?php echo ($user['sad_genre'] == 'thriller') ? 'selected' : ''; ?>> 스릴러</option>
                    <option value="crime" <?php echo ($user['sad_genre'] == 'crime') ? 'selected' : ''; ?>> 범죄</option>
                    <option value="war" <?php echo ($user['sad_genre'] == 'war') ? 'selected' : ''; ?>> 전쟁</option>
                    <option value="SF" <?php echo ($user['sad_genre'] == 'SF') ? 'selected' : ''; ?>> SF</option>
                    <option value="fantasy" <?php echo ($user['sad_genre'] == 'fantasy') ? 'selected' : ''; ?>> 판타지</option>
                    <option value="mystery" <?php echo ($user['sad_genre'] == 'mystery') ? 'selected' : ''; ?>> 미스터리</option>
                    <option value="documentary" <?php echo ($user['sad_genre'] == 'documentary') ? 'selected' : ''; ?>> 다큐멘터리</option>
                    <option value="drama" <?php echo ($user['sad_genre'] == 'drama') ? 'selected' : ''; ?>> 드라마</option>
                    <option value="melodrama" <?php echo ($user['sad_genre'] == 'melodrama') ? 'selected' : ''; ?>> 멜로드라마</option>
                    <option value="animation" <?php echo ($user['sad_genre'] == 'animation') ? 'selected' : ''; ?>> 애니메이션</option>
                    <option value="indie" <?php echo ($user['sad_genre'] == 'indie') ? 'selected' : ''; ?>> 인디</option>
                </select><br>
            </div>

            <div>
                <h2>감정 및 선호하는 음악 장르</h2>

                <div id="emotion-music" class="collapsible">
                    <label for="happy_genre_music">행복할 때 선호하는 음악 장르:</label>
                    <select name="happy_genre_music" id="happy_genre_music" required>
                        <option value="댄스" <?php echo ($user['happy_genre_music'] == '댄스') ? 'selected' : ''; ?>> 댄스</option>
                        <option value="발라드" <?php echo ($user['happy_genre_music'] == '발라드') ? 'selected' : ''; ?>> 발라드</option>
                        <option value="랩/힙합" <?php echo ($user['happy_genre_music'] == '랩/힙합') ? 'selected' : ''; ?>> 랩/힙합</option>
                        <option value="r&b/soul" <?php echo ($user['happy_genre_music'] == 'r&b/soul') ? 'selected' : ''; ?>> R&B/Soul</option>
                        <option value="인디" <?php echo ($user['happy_genre_music'] == '인디') ? 'selected' : ''; ?>> 인디</option>
                        <option value="rock/metal" <?php echo ($user['happy_genre_music'] == 'rock/metal') ? 'selected' : ''; ?>> 록/메탈</option>
                        <option value="트로트" <?php echo ($user['happy_genre_music'] == '트로트') ? 'selected' : ''; ?>> 트로트</option>
                        <option value="포크/블루스" <?php echo ($user['happy_genre_music'] == '포크/블루스') ? 'selected' : ''; ?>> 포크/블루스</option>
                        <option value="pop" <?php echo ($user['happy_genre_music'] == 'pop') ? 'selected' : ''; ?>> POP</option>
                        <option value="ost" <?php echo ($user['happy_genre_music'] == 'ost') ? 'selected' : ''; ?>> OST</option>
                        <option value="클래식" <?php echo ($user['happy_genre_music'] == '클래식') ? 'selected' : ''; ?>> 클래식</option>
                        <option value="j-pop" <?php echo ($user['happy_genre_music'] == 'j-pop') ? 'selected' : ''; ?>> J-Pop</option>
                        <option value="재즈" <?php echo ($user['happy_genre_music'] == '재즈') ? 'selected' : ''; ?>> 재즈</option>
                        <option value="애니메이션" <?php echo ($user['happy_genre_music'] == '애니메이션') ? 'selected' : ''; ?>> 애니메이션</option>
                    </select><br>

                    <label for="angry_genre_music">화날 때 선호하는 음악 장르 : </label>
                    <select name="angry_genre_music" id="angry_genre_music" required>
                        <option value="댄스" <?php echo ($user['angry_genre_music'] == '댄스') ? 'selected' : ''; ?>> 댄스</option>
                        <option value="발라드" <?php echo ($user['angry_genre_music'] == '발라드') ? 'selected' : ''; ?>> 발라드</option>
                        <option value="랩/힙합" <?php echo ($user['angry_genre_music'] == '랩/힙합') ? 'selected' : ''; ?>> 랩/힙합</option>
                        <option value="r&b/soul" <?php echo ($user['angry_genre_music'] == 'r&b/soul') ? 'selected' : ''; ?>> R&B/Soul</option>
                        <option value="인디" <?php echo ($user['angry_genre_music'] == '인디') ? 'selected' : ''; ?>> 인디</option>
                        <option value="rock/metal" <?php echo ($user['angry_genre_music'] == 'rock/metal') ? 'selected' : ''; ?>> 록/메탈</option>
                        <option value="트로트" <?php echo ($user['angry_genre_music'] == '트로트') ? 'selected' : ''; ?>> 트로트</option>
                        <option value="포크/블루스" <?php echo ($user['angry_genre_music'] == '포크/블루스') ? 'selected' : ''; ?>> 포크/블루스</option>
                        <option value="pop" <?php echo ($user['angry_genre_music'] == 'pop') ? 'selected' : ''; ?>> POP</option>
                        <option value="ost" <?php echo ($user['angry_genre_music'] == 'ost') ? 'selected' : ''; ?>> OST</option>
                        <option value="클래식" <?php echo ($user['angry_genre_music'] == '클래식') ? 'selected' : ''; ?>> 클래식</option>
                        <option value="j-pop" <?php echo ($user['angry_genre_music'] == 'j-pop') ? 'selected' : ''; ?>> J-Pop</option>
                        <option value="재즈" <?php echo ($user['angry_genre_music'] == '재즈') ? 'selected' : ''; ?>> 재즈</option>
                        <option value="애니메이션" <?php echo ($user['angry_genre_music'] == '애니메이션') ? 'selected' : ''; ?>> 애니메이션</option>
                    </select><br>

                    <label for="bored_genre_music">지루할 때 선호하는 음악 장르:</label>
                    <select name="bored_genre_music" id="bored_genre_music" required>
                        <option value="댄스" <?php echo ($user['bored_genre_music'] == '댄스') ? 'selected' : ''; ?>> 댄스</option>
                        <option value="발라드" <?php echo ($user['bored_genre_music'] == '발라드') ? 'selected' : ''; ?>> 발라드</option>
                        <option value="랩/힙합" <?php echo ($user['bored_genre_music'] == '랩/힙합') ? 'selected' : ''; ?>> 랩/힙합</option>
                        <option value="r&b/soul" <?php echo ($user['bored_genre_music'] == 'r&b/soul') ? 'selected' : ''; ?>> R&B/Soul</option>
                        <option value="인디" <?php echo ($user['bored_genre_music'] == '인디') ? 'selected' : ''; ?>> 인디</option>
                        <option value="rock/metal" <?php echo ($user['bored_genre_music'] == 'rock/metal') ? 'selected' : ''; ?>> 록/메탈</option>
                        <option value="트로트" <?php echo ($user['bored_genre_music'] == '트로트') ? 'selected' : ''; ?>> 트로트</option>
                        <option value="포크/블루스" <?php echo ($user['bored_genre_music'] == '포크/블루스') ? 'selected' : ''; ?>> 포크/블루스</option>
                        <option value="pop" <?php echo ($user['bored_genre_music'] == 'pop') ? 'selected' : ''; ?>> POP</option>
                        <option value="ost" <?php echo ($user['bored_genre_music'] == 'ost') ? 'selected' : ''; ?>> OST</option>
                        <option value="클래식" <?php echo ($user['bored_genre_music'] == '클래식') ? 'selected' : ''; ?>> 클래식</option>
                        <option value="j-pop" <?php echo ($user['bored_genre_music'] == 'j-pop') ? 'selected' : ''; ?>> J-Pop</option>
                        <option value="재즈" <?php echo ($user['bored_genre_music'] == '재즈') ? 'selected' : ''; ?>> 재즈</option>
                        <option value="애니메이션" <?php echo ($user['bored_genre_music'] == '애니메이션') ? 'selected' : ''; ?>> 애니메이션</option>
                    </select><br>

                    <label for="sad_genre_music">슬플 때 선호하는 음악 장르 : </label>
                    <select name="sad_genre_music" id="sad_genre_music" required>
                        <option value="댄스" <?php echo ($user['sad_genre_music'] == '댄스') ? 'selected' : ''; ?>> 댄스</option>
                        <option value="발라드" <?php echo ($user['sad_genre_music'] == '발라드') ? 'selected' : ''; ?>> 발라드</option>
                        <option value="랩/힙합" <?php echo ($user['sad_genre_music'] == '랩/힙합') ? 'selected' : ''; ?>> 랩/힙합</option>
                        <option value="r&b/soul" <?php echo ($user['sad_genre_music'] == 'r&b/soul') ? 'selected' : ''; ?>> R&B/Soul</option>
                        <option value="인디" <?php echo ($user['sad_genre_music'] == '인디') ? 'selected' : ''; ?>> 인디</option>
                        <option value="rock/metal" <?php echo ($user['sad_genre_music'] == 'rock/metal') ? 'selected' : ''; ?>> 록/메탈</option>
                        <option value="트로트" <?php echo ($user['sad_genre_music'] == '트로트') ? 'selected' : ''; ?>> 트로트</option>
                        <option value="포크/블루스" <?php echo ($user['sad_genre_music'] == '포크/블루스') ? 'selected' : ''; ?>> 포크/블루스</option>
                        <option value="pop" <?php echo ($user['sad_genre_music'] == 'pop') ? 'selected' : ''; ?>> POP</option>
                        <option value="ost" <?php echo ($user['sad_genre_music'] == 'ost') ? 'selected' : ''; ?>> OST</option>
                        <option value="클래식" <?php echo ($user['sad_genre_music'] == '클래식') ? 'selected' : ''; ?>> 클래식</option>
                        <option value="j-pop" <?php echo ($user['sad_genre_music'] == 'j-pop') ? 'selected' : ''; ?>> J-Pop</option>
                        <option value="재즈" <?php echo ($user['sad_genre_music'] == '재즈') ? 'selected' : ''; ?>> 재즈</option>
                        <option value="애니메이션" <?php echo ($user['sad_genre_music'] == '애니메이션') ? 'selected' : ''; ?>> 애니메이션</option>
                    </select><br>
                </div>
            </div>

            <input type="submit" value="전송">
            <input type="reset" value="취소" onClick="location.href='mypage.php'">
    </form>
</body>

</html>