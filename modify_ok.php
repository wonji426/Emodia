<?php
session_start();
$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

// 사용자가 로그인했는지 확인
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// GET 요청으로 전송된 데이터 받기
$happy_genre = mysqli_real_escape_string($conn, $_GET["happy_genre"]);
$angry_genre = mysqli_real_escape_string($conn, $_GET["angry_genre"]);
$bored_genre = mysqli_real_escape_string($conn, $_GET["bored_genre"]);
$sad_genre = mysqli_real_escape_string($conn, $_GET["sad_genre"]);
$happy_genre_music = mysqli_real_escape_string($conn, $_GET["happy_genre_music"]);
$angry_genre_music = mysqli_real_escape_string($conn, $_GET["angry_genre_music"]);
$bored_genre_music = mysqli_real_escape_string($conn, $_GET["bored_genre_music"]);
$sad_genre_music = mysqli_real_escape_string($conn, $_GET["sad_genre_music"]);

// SQL 쿼리 준비
$sql = "UPDATE users SET 
        happy_genre = ?, 
        angry_genre = ?, 
        bored_genre = ?, 
        sad_genre = ?,
        happy_genre_music = ?,
        angry_genre_music = ?,
        bored_genre_music = ?,
        sad_genre_music = ?
        WHERE id = ?";

// prepared statement 생성
$stmt = $conn->prepare($sql);

// 파라미터 바인딩
$stmt->bind_param("sssssssss", 
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

// 쿼리 실행
if ($stmt->execute()) {
    echo "<script>alert('선호도가 성공적으로 업데이트되었습니다.'); window.location.href='mypage.php';</script>";
} else {
    echo "<script>alert('오류가 발생했습니다. 다시 시도해주세요.'); window.location.href='change_genre.php';</script>";
}

// statement 닫기
$stmt->close();

// 데이터베이스 연결 닫기
$conn->close();
?>