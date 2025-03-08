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
    $user_id = $_POST['id'];
    $user_name = $_POST['name'];  // 이름 필드 추가

    $stmt = $conn->prepare("SELECT email FROM membertable WHERE id = ? AND name = ?");  // SQL 쿼리 수정
    $stmt->bind_param("ss", $user_id, $user_name);  // 바인딩 매개변수 추가
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_email = $user['email'];

        // 임시 비밀번호 생성 (8자리)
        $temp_password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

        // 비밀번호 업데이트
        $update_stmt = $conn->prepare("UPDATE membertable SET pw = ? WHERE id = ? AND name = ?");
        $update_stmt->bind_param("sss", $hashed_password, $user_id, $user_name);

        if ($update_stmt->execute()) {
            // 이메일 발송
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.naver.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'kkjjww0426'; // 개발자 이메일
                $mail->Password = 'Kjw741147~!~'; // Gmail 앱 비밀번호
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('kkjjww0426@naver.com', '감정기반추천시스템');
                $mail->addAddress($user_email);

                $mail->CharSet = 'UTF-8';
                $mail->isHTML(true);
                $mail->Subject = '[감정기반추천시스템] 임시 비밀번호 발급';
                $mail->Body = "
                    <div style='padding: 20px; background-color: #f5f5f5;'>
                        <h2 style='color: #333;'>임시 비밀번호가 발급되었습니다.</h2>
                        <p>회원님의 임시 비밀번호: <strong>{$temp_password}</strong></p>
                        <p>보안을 위해 로그인 후 반드시 비밀번호를 변경해주세요.</p>
                    </div>";

                $mail->send();
                echo "<script>
                    alert('임시 비밀번호가 이메일로 발송되었습니다.');
                    window.location.href='login.php';
                </script>";
            } catch (Exception $e) {
                echo "<script>alert('이메일 발송 실패: {$mail->ErrorInfo}');</script>";
            }
        } else {
            echo "<script>alert('비밀번호 업데이트 실패');</script>";
        }
    } else {
        echo "<script>alert('입력하신 정보가 일치하지 않습니다.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>이메일로 비밀번호 찾기</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/forgot_password.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Jua', sans-serif;
            background-color: #f2f2f2;
        }

        .navbar {
            background-color: #333;
            width: 100%;
            font-family: 'Jua', sans-serif;
            position: fixed;
            /* 고정 위치 */
            top: 0;
            /* 화면 상단에 고정 */
            left: 0;
            z-index: 1000;
            /* 다른 요소들 위에 표시 */
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            height: 60px;
            /* navbar의 높이 지정 */
        }

        .navbar-logo a {
            color: white;
            text-decoration: none;
            font-size: 1.7em;
        }

        .navbar-menu {
            list-style-type: none;
            display: flex;
            margin: 0;
            padding: 0;
            transition: max-height 0.5s ease, opacity 0.5s ease;
            opacity: 1;
            max-height: none;
        }

        .navbar-menu li {
            margin: 0;
        }

        .navbar-menu li a {
            display: block;
            color: white;
            text-align: center;
            padding: 0 10px;
            text-decoration: none;
            line-height: 60px;
            /* navbar의 높이와 동일하게 설정 */
        }

        .navbar-menu li a:hover {
            background-color: #ddd;
            color: black;
        }

        .navbar-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .navbar-toggle .bar {
            height: 3px;
            width: 25px;
            background-color: white;
            border-radius: 10px;
        }

        .toggle-icon {
            font-size: 0.8em;
        }

        /* 반응형 디자인 */
        @media screen and (min-width: 390px) and (max-width: 844px) {
            .navbar-content {
                height: auto;
            }

            .navbar-menu {
                width: 100%;
                flex-direction: column;
                transition: max-height 0.5s ease, opacity 0.5s ease;
                opacity: 0;
                max-height: 0;
                overflow: hidden;
            }

            .navbar-menu.active {
                opacity: 1;
                max-height: 300px;
            }

            .navbar-toggle {
                display: flex;
            }

            .navbar-menu li a {
                padding: 10px;
                line-height: normal;
            }

            body {
                padding-top: 30px;
            }
        }
    </style>
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
        <h2>이메일로 비밀번호 찾기</h2>
        <form method="POST" action="">
            <input type="text" name="id" placeholder="아이디" required>
            <input type="text" name="name" placeholder="이름" required> <!-- 이름 입력 필드 추가 -->
            <input type="submit" value="비밀번호 찾기">
            <button type="button" class="back-btn" onclick="location.href='login.php'">돌아가기</button>
        </form>
    </div>
</body>

</html>