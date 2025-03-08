<?php
$conn = mysqli_connect('localhost', '', '', '')
or die("데이터베이스 연결 오류");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['name'];
    $user_email = $_POST['email'];

    // 이름과 이메일로 사용자 ID 조회
    $stmt = $conn->prepare("SELECT id FROM membertable WHERE name = ? AND email = ?");
    $stmt->bind_param("ss", $user_name, $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // 이메일 발송
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.naver.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kkjjww0426';
            $mail->Password = 'Kjw741147~!~';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('kkjjww0426@naver.com', '감정기반추천시스템');
            $mail->addAddress($user_email);
            
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = '[감정기반추천시스템] 아이디 찾기 결과';
            $mail->Body = "
                <div style='padding: 20px; background-color: #f5f5f5;'>
                    <h2 style='color: #333;'>회원님의 아이디를 알려드립니다.</h2>
                    <p>회원님의 아이디는 <strong>{$user_id}</strong> 입니다.</p>
                    <p>로그인 페이지에서 확인된 아이디로 로그인해주세요.</p>
                </div>";

            $mail->send();
            echo "<script>
                alert('회원님의 아이디가 이메일로 발송되었습니다.');
                window.location.href='login.php';
            </script>";
        } catch (Exception $e) {
            echo "<script>alert('이메일 발송 실패: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('입력하신 정보와 일치하는 회원정보가 없습니다.');</script>";
    }
}
?>