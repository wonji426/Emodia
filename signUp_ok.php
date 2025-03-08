<?php
// 데이터베이스 연결
$conn = mysqli_connect('localhost', '', '', '')
or die("데이터베이스 연결 오류");

// POST 데이터 받기
$id = $_GET['id'];
$pw = password_hash($_GET["pw"], PASSWORD_DEFAULT);
$name = $_GET['name'];
$email = $_GET['email'];
$security_question = $_GET['security_question'];
$custom_security_question = $_GET['custom_security_question'];
$security_answer = $_GET['security_answer'];
$happy_genre = $_GET['happy_genre'];
$angry_genre = $_GET['angry_genre'];
$bored_genre = $_GET['bored_genre'];
$sad_genre = $_GET['sad_genre'];
$happy_genre_music = $_GET['happy_genre_music'];
$angry_genre_music = $_GET['angry_genre_music'];
$bored_genre_music = $_GET['bored_genre_music'];
$sad_genre_music = $_GET['sad_genre_music'];


if($security_question == 'custom'){
    $security_question = $custom_security_question;
}

// 비밀번호 유효성 검사
if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[0-9])(?=.*[a-z]).{8,}$/', $pw)) {
    die("<script> alert('비밀번호는 8자 이상이어야 하며, 영문 대문자, 특수문자, 숫자를 각각 1개 이상 포함해야 합니다.');
    location.replace('login.php'); </script>");
}

// 데이터베이스에 사용자 정보 삽입
$sql = "INSERT INTO membertable (id, pw, name, email, security_question, security_answer, happy_genre, angry_genre, bored_genre, sad_genre, happy_genre_music, angry_genre_music, bored_genre_music, sad_genre_music) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssssss", $id, $pw, $name, $email, $security_question, $security_answer, $happy_genre, $angry_genre, $bored_genre, $sad_genre, $happy_genre_music, $angry_genre_music, $bored_genre_music, $sad_genre_music);

if ($stmt->execute()) {
    echo "<script> alert('회원가입이 완료되었습니다.');
            location.replace('login.php'); </script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$stmt->close();
$conn->close();
?>
