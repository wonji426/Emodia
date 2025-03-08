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
$movie_id = $_GET['movie_id'];

// 영화 정보 가져오기
$movie_query = "SELECT * FROM movie WHERE no = '$movie_id'";
$movie_result = mysqli_query($conn, $movie_query);
$movie = mysqli_fetch_assoc($movie_result);
$movie_title = $movie['title'];

// 사용자의 기존 평점 확인
$check_query = "SELECT rating FROM movie_ratings WHERE user_id = '$user_id' AND movie_id = '$movie_id'";
$check_result = mysqli_query($conn, $check_query);
$existing_rating = null;

if (mysqli_num_rows($check_result) > 0) {
    $existing_rating = mysqli_fetch_assoc($check_result)['rating'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];
    
    if ($existing_rating !== null) {
        // 기존 평점 업데이트
        $update_query = "UPDATE movie_ratings SET rating = '$rating' WHERE user_id = '$user_id' AND movie_id = '$movie_id'";
        mysqli_query($conn, $update_query);
    } else {
        // 새 평점 저장
        $insert_query = "INSERT INTO movie_ratings (user_id, movie_id, rating) VALUES ('$user_id', '$movie_id', '$rating')";
        mysqli_query($conn, $insert_query);
    }
    
    // 영화 테이블의 평점 업데이트
    $update_movie_query = "UPDATE movie SET rating = (SELECT AVG(rating) FROM movie_ratings WHERE movie_id = '$movie_id'), rating_count = (SELECT COUNT(*) FROM movie_ratings WHERE movie_id = '$movie_id') WHERE no = '$movie_id'";
    mysqli_query($conn, $update_movie_query);
    
    echo "<script>
        alert('평점이 성공적으로 저장되었습니다.');
        window.location.href = 'search.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="Title" content="감정을 읽는 미디어 추천 사이트">
    <title>영화 평점 등록</title>
    <!-- <link rel="shortcut icon" href="web_images/Smaile.jpg"> -->
    <link rel="stylesheet" href="./css/rate_movie.css">
    <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <script src="./js/script.js"></script>
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

    <h1><?php echo htmlspecialchars($movie_title); ?> 평점 <?php echo $existing_rating !== null ? '수정' : '등록'; ?></h1>

    <div class="movie-main-container">
        <div class="movie-info">
            <?php if (!empty($movie['poster'])): ?>
                <img src="<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie_title); ?> 포스터" class="movie-poster">
            <?php else: ?>
                <p class="no-poster-message">포스터 이미지가 없습니다.</p>
            <?php endif; ?>
            <div class="movie-info-sub">
                <p data-type="info"><strong>제목:</strong> <?php echo htmlspecialchars($movie_title); ?></p>
                <p data-type="info"><strong>장르:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
                <p data-type="info"><strong>개봉년도:</strong> <?php echo htmlspecialchars($movie['year']); ?></p>
                <p data-type="info"><strong>현재 평점:</strong> <?php echo number_format($movie['rating'], 1); ?> (<?php echo $movie['rating_count']; ?>명 참여)</p>
            </div>
        </div>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="rating">평점 (0-5):</label>
            <input type="number" id="rating" name="rating" min="0" max="5" step="0.1" required value="<?php echo $existing_rating !== null ? $existing_rating : ''; ?>">
            <button type="submit">평점 <?php echo $existing_rating !== null ? '수정' : '등록'; ?></button>
        </form>
    </div>

    <a class="search-back" href="search.php">검색 페이지로 돌아가기</a>
</body>

</html>