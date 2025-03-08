<?php
session_start();
$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

$uid = $_GET['id'];
$pwd = $_GET['pw'];
$auto_login = isset($_GET['auto_login']) ? $_GET['auto_login'] : '';

$sql = "SELECT * FROM membertable WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($pwd, $row['pw'])) {
        $_SESSION['id'] = $uid;

        // 자동 로그인 체크시 쿠키 생성
        if ($auto_login == 'true') {
            $token = bin2hex(random_bytes(32));
            setcookie('auto_login', $token, time() + (86400 * 30), '/'); //24시간 1440분 86400초

            // 토큰을 데이터베이스에 저장
            $update_sql = "UPDATE membertable SET remember_token = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $token, $uid);
            $update_stmt->execute();
        }

        echo "<script> alert('로그인 성공');
            location.replace('index.php'); </script>";
    } else {
        echo "<script> alert('로그인 실패: 비밀번호가 일치하지 않습니다.'); 
            location.replace('login.php'); </script>";
    }
} else {
    echo "<script> alert('로그인 실패: 사용자를 찾을 수 없습니다.'); 
        location.replace('login.php'); </script>";
}
