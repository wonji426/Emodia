<?php
session_start();
header('Content-Type: application/json');

$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

$user_id = mysqli_real_escape_string($conn, $_SESSION['id'] ?? '');
$emotion = mysqli_real_escape_string($conn, $_POST['emotion'] ?? '');

if (empty($emotion)) {
    die(json_encode(["error" => "감정 정보가 제공되지 않았습니다."]));
}

$genre_field = $emotion . "_genre_music";
$query = "SELECT $genre_field FROM membertable WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die(json_encode(["error" => "쿼리 오류: " . mysqli_error($conn)]));
}

$row = mysqli_fetch_assoc($result);
$preferred_genre = $row[$genre_field] ?? '';

echo json_encode(["genre" => $preferred_genre]);

mysqli_close($conn);
?>