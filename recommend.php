<?php
session_start();
if (!$_SESSION["id"]) {
    echo "<script> alert('로그인이 필요합니다.');
        location.replace('login.php'); </script>";
}

// 디버깅을 위해 오류 보고 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류: " . mysqli_connect_error());

// 감정 데이터 확인
// if (!isset($_POST['emotion']) || empty($_POST['emotion'])) {
//     die("감정 데이터가 전달되지 않았습니다.");
// }

// 사용자 ID와 감정을 받아옴 (로그인 상태라고 가정)
$user_id = mysqli_real_escape_string($conn, $_SESSION['id']);
$emotion = mysqli_real_escape_string($conn, $_POST['emotion']);

// 디버깅을 위한 출력
// echo "사용자 ID: " . $user_id . "<br>";
// echo "선택한 감정: " . $emotion . "<br>";

// 사용자의 감정에 따른 선호 장르를 가져옴
$user_query = "SELECT {$emotion}_genre FROM membertable WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);

if (!$user_result) {
    die("사용자 쿼리 오류: " . mysqli_error($conn));
}

$user_row = mysqli_fetch_assoc($user_result);
$preferred_genre = $user_row["{$emotion}_genre"];

// 선호 장르와 감정을 기반으로 영화를 추천
$query = "SELECT * FROM movie 
WHERE genre LIKE '%$preferred_genre%' OR genre LIKE '%$emotion%' 
ORDER BY RAND() 
LIMIT 5";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("영화 쿼리 오류: " . mysqli_error($conn));
}

// 결과 행 수 확인
$movie_count = mysqli_num_rows($result);

?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./css/recommend.css">
    <title>추천 영화 목록</title>
</head>

<body>
    <div class="container">
        <h2>추천 영화 목록</h2>
        <div class="info-line">
            <p>제목을 눌러 정보 페이지로 이동할 수 있습니다.</p>
            <button id="reset-btn" onclick="initPic();">새로고침</button>
        </div>

        <?php if ($movie_count > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>제목</th>
                        <th>년도</th>
                        <th>장르</th>
                        <th>평점</th>
                        <th>포스터</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($result)): ?>
                        <tr>
                            <td data-label="번호"><?php echo $row["no"]; ?></td>
                            <td data-label="제목">
                                <a href="javascript:void(0);"
                                    onclick="searchMovie('<?php echo htmlspecialchars(string: $row['title'], flags: ENT_QUOTES); ?>');">
                                    <?php echo htmlspecialchars($row["title"]); ?>
                                </a>
                            </td>
                            <td data-label="년도"><?php echo $row["year"]; ?></td>
                            <td data-label="장르"><?php echo $row["genre"]; ?></td>
                            <td data-label="평점"><?php echo $row["rating"]; ?></td>
                            <td data-label="포스터">
                                <?php if (!empty($row["poster"]) && $row["poster"] != "NULL"): ?>
                                    <img class="img-poster" src="<?php echo $row["poster"]; ?>" alt="Movie Poster">
                                <?php else: ?>
                                    포스터 없음
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-results">
                <p>추천 영화가 검색되지 않습니다. AI도우미를 사용해보세요!</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>

<?php
mysqli_close($conn);
?>