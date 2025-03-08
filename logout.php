<?php
session_start();
$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

// 데이터베이스에서 remember_token 삭제
if (isset($_SESSION['id'])) {
	$sql = "UPDATE membertable SET remember_token = NULL WHERE id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $_SESSION['id']);
	$stmt->execute();
}

// 자동 로그인 쿠키 삭제
if (isset($_COOKIE['auto_login'])) {
	setcookie('auto_login', '', time() - 3600, '/');
}

// 세션 삭제
session_unset();
session_destroy();

// 로그인 페이지로 리다이렉트
echo "<script> alert('로그아웃 되었습니다.');
	location.replace('index.php'); </script>";
exit();
