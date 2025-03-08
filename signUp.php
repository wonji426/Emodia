<?php

session_start();

$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

$id_checked = false;

if (isset($_POST['check_id'])) {
    $id = $_POST['id'];
    $sql = "SELECT * FROM membertable WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['isDuplicate' => true]);
    } else {
        echo json_encode(['isDuplicate' => false]);
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $pw = password_hash($_POST['passwordInput'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $security_question = $_POST['security_question'];
    $custom_security_question = $_POST['custom_security_question'];
    $security_answer = $_POST['security_answer'];
    $happy_genre = $_POST['happy_genre'];
    $angry_genre = $_POST['angry_genre'];
    $bored_genre = $_POST['bored_genre'];
    $sad_genre = $_POST['sad_genre'];
    $happy_genre_music = $_POST['happy_genre_music'];
    $angry_genre_music = $_POST['angry_genre_music'];
    $bored_genre_music = $_POST['bored_genre_music'];
    $sad_genre_music = $_POST['sad_genre_music'];
    

    if (!isset($_SESSION['id_checked']) || $_SESSION['id_checked'] !== $id) {
        echo "<script>alert('아이디 중복 체크를 먼저 해주세요.'); history.back();</script>";
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Emodia 회원가입</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            var idChecked = false;

            $('#checkDuplicate').click(function() {
                var id = $('#id').val();
                if (id) {
                    $.ajax({
                        url: 'signUp.php',
                        type: 'POST',
                        data: {
                            check_id: true,
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.isDuplicate) {
                                document.getElementById('id-result').textContent = '이미 사용 중인 아이디입니다.';
                                document.getElementById('id-result').style.color = 'red';
                                idChecked = false;
                            } else {
                                document.getElementById('id-result').textContent = '사용 가능한 아이디입니다.';
                                document.getElementById('id-result').style.color = 'green';
                                idChecked = true;
                            }
                        }
                    });
                } else {
                    alert('아이디를 입력해주세요.');
                }
            });

            $('form').submit(function(e) {
                if (!idChecked) {
                    e.preventDefault();
                    alert('아이디 중복 체크를 먼저 해주세요.');
                }
            });

            $('#id').change(function() {
                idChecked = false;
            });
        });
    </script>
    <meta name="Title" content="감정을 읽는 미디어 추천 사이트">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <!-- <link rel="shortcut icon" href="web_images/Smaile.jpg"> -->
    <script src="./js/signUp.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/signUp.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <form name="insert" method="get" action="signUp_ok.php">
        <h1>사용자 정보 입력</h1>
        <label for="id">아이디</label>
        <input type="text" id="id" name="id" placeholder="아이디 입력" required>
        <button type="button" id="checkDuplicate">중복 체크</button><br>
        <span id="id-result"></span><br>
        <div class="form-group">
            <label for="passwordInput">비밀번호</label>
            <div class="input-group">
                <div class="password-container">
                    <input type="password" class="form-control" id="passwordInput" name="pw" placeholder="비밀번호 입력" required>
                    <span id="togglePassword" class="toggle-password">
                        <i class="far fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
            </div>
            <small id="passwordHelpBlock" class="form-text text-muted">
                비밀번호는 8자 이상이어야 하며, 영문 대문자, 특수문자, 숫자를 각각 1개 이상 포함해야 합니다.
            </small>
        </div>
        <label for="name">이름</label>
        <input type="text" id="name" name="name" placeholder="이름 입력" required>
        <label for="email">email</label>
        <input type="email" id="email" name="email" placeholder="이메일 입력" required>
        <div class="security-section">
            <h2>보안 질문</h2>
            <label for="security_question">질문 선택:</label>
            <select name="security_question" id="security_question" onchange="checkCustomQuestion(this)" required>
                <option value="">보안 질문을 선택하세요</option>
                <option value="어머니의 성함은?">어머니의 성함은?</option>
                <option value="태어난 도시는?">태어난 도시는?</option>
                <option value="첫 애완동물의 이름은?">첫 애완동물의 이름은?</option>
                <option value="가장 좋아하는 영화는?">가장 좋아하는 영화는?</option>
                <option value="초등학교 첫 담임 선생님 성함은?">초등학교 첫 담임 선생님 성함은?</option>
                <option value="custom">직접 입력</option>
            </select><br>
            <input type="text" name="custom_security_question" id="custom_security_question" style="display:none;" placeholder="직접 질문을 입력하세요">
            <label for="security_answer">보안 답변:</label>
            <input type="text" name="security_answer" id="security_answer" placeholder="보안답변 입력" required>
        </div>

        <div>
            <h2>감정 및 선호하는 영화 장르</h2>

            <div id="emotion-movie" class="collapsible">
                <label for="happy_genre">행복할 때 선호하는 영화 장르:</label>
                <select name="happy_genre" id="happy_genre" required>
                    <option value="">선택하세요</option>
                    <option value="comedy"> 코미디</option>
                    <option value="romance"> 로맨스</option>
                    <option value="musical"> 뮤지컬</option>
                    <option value="adventure"> 모험</option>
                    <option value="action"> 액션</option>
                    <option value="thriller"> 스릴러</option>
                    <option value="crime"> 범죄</option>
                    <option value="war"> 전쟁</option>
                    <option value="SF"> SF</option>
                    <option value="fantasy"> 판타지</option>
                    <option value="mystery"> 미스터리</option>
                    <option value="documentary"> 다큐멘터리</option>
                    <option value="drama"> 드라마</option>
                    <option value="melodrama"> 멜로드라마</option>
                    <option value="animation"> 애니메이션</option>
                    <option value="indie"> 인디</option>
                </select><br>

                <label for="angry_genre">화날 때 선호하는 영화 장르 : </label>
                <select name="angry_genre" id="angry_genre" required>
                    <option value="">선택하세요</option>
                    <option value="comedy"> 코미디</option>
                    <option value="romance"> 로맨스</option>
                    <option value="musical"> 뮤지컬</option>
                    <option value="adventure"> 모험</option>
                    <option value="action"> 액션</option>
                    <option value="thriller"> 스릴러</option>
                    <option value="crime"> 범죄</option>
                    <option value="war"> 전쟁</option>
                    <option value="SF"> SF</option>
                    <option value="fantasy"> 판타지</option>
                    <option value="mystery"> 미스터리</option>
                    <option value="documentary"> 다큐멘터리</option>
                    <option value="drama"> 드라마</option>
                    <option value="melodrama"> 멜로드라마</option>
                    <option value="animation"> 애니메이션</option>
                    <option value="indie"> 인디</option>
                </select><br>

                <label for="bored_genre">지루할 때 선호하는 영화 장르:</label>
                <select name="bored_genre" id="bored_genre" required>
                    <option value="">선택하세요</option>
                    <option value="comedy"> 코미디</option>
                    <option value="romance"> 로맨스</option>
                    <option value="musical"> 뮤지컬</option>
                    <option value="adventure"> 모험</option>
                    <option value="action"> 액션</option>
                    <option value="thriller"> 스릴러</option>
                    <option value="crime"> 범죄</option>
                    <option value="war"> 전쟁</option>
                    <option value="SF"> SF</option>
                    <option value="fantasy"> 판타지</option>
                    <option value="mystery"> 미스터리</option>
                    <option value="documentary"> 다큐멘터리</option>
                    <option value="drama"> 드라마</option>
                    <option value="melodrama"> 멜로드라마</option>
                    <option value="animation"> 애니메이션</option>
                    <option value="indie"> 인디</option>
                </select><br>

                <label for="sad_genre">슬플 때 선호하는 영화 장르 : </label>
                <select name="sad_genre" id="sad_genre" required>
                    <option value="">선택하세요</option>
                    <option value="comedy"> 코미디</option>
                    <option value="romance"> 로맨스</option>
                    <option value="musical"> 뮤지컬</option>
                    <option value="adventure"> 모험</option>
                    <option value="action"> 액션</option>
                    <option value="thriller"> 스릴러</option>
                    <option value="crime"> 범죄</option>
                    <option value="war"> 전쟁</option>
                    <option value="SF"> SF</option>
                    <option value="fantasy"> 판타지</option>
                    <option value="mystery"> 미스터리</option>
                    <option value="documentary"> 다큐멘터리</option>
                    <option value="drama"> 드라마</option>
                    <option value="melodrama"> 멜로드라마</option>
                    <option value="animation"> 애니메이션</option>
                    <option value="indie"> 인디</option>
                </select><br>
            </div>

            <div>
                <h2>감정 및 선호하는 음악 장르</h2>

                <div id="emotion-music" class="collapsible">
                    <label for="happy_genre_music">행복할 때 선호하는 음악 장르:</label>
                    <select name="happy_genre_music" id="happy_genre_music" required>
                        <option value="">선택하세요</option>
                        <option value="댄스"> 댄스
                        <option value="발라드"> 발라드
                        <option value="랩/힙합"> 랩/힙합
                        <option value="r&b/soul"> R&B/Soul
                        <option value="인디"> 인디
                        <option value="rock/metal"> 록/메탈
                        <option value="트로트"> 트로트
                        <option value="포크/블루스"> 포크/블루스
                        <option value="pop"> POP
                        <option value="ost"> OST
                        <option value="클래식"> 클래식
                        <option value="j-pop"> J-Pop
                        <option value="재즈"> 재즈
                        <option value="애니메이션"> 애니메이션
                    </select><br>

                    <label for="angry_genre_music">화날 때 선호하는 음악 장르 : </label>
                    <select name="angry_genre_music" id="angry_genre_music" required>
                        <option value="">선택하세요</option>
                        <option value="댄스"> 댄스
                        <option value="발라드"> 발라드
                        <option value="랩/힙합"> 랩/힙합
                        <option value="r&b/soul"> R&B/Soul
                        <option value="인디"> 인디
                        <option value="rock/metal"> 록/메탈
                        <option value="트로트"> 트로트
                        <option value="포크/블루스"> 포크/블루스
                        <option value="pop"> POP
                        <option value="ost"> OST
                        <option value="클래식"> 클래식
                        <option value="j-pop"> J-Pop
                        <option value="재즈"> 재즈
                        <option value="애니메이션"> 애니메이션
                    </select><br>

                    <label for="bored_genre_music">지루할 때 선호하는 음악 장르:</label>
                    <select name="bored_genre_music" id="bored_genre_music" required>
                        <option value="">선택하세요</option>
                        <option value="댄스"> 댄스
                        <option value="발라드"> 발라드
                        <option value="랩/힙합"> 랩/힙합
                        <option value="r&b/soul"> R&B/Soul
                        <option value="인디"> 인디
                        <option value="rock/metal"> 록/메탈
                        <option value="트로트"> 트로트
                        <option value="포크/블루스"> 포크/블루스
                        <option value="pop"> POP
                        <option value="ost"> OST
                        <option value="클래식"> 클래식
                        <option value="j-pop"> J-Pop
                        <option value="재즈"> 재즈
                        <option value="애니메이션"> 애니메이션
                    </select><br>

                    <label for="sad_genre_music">슬플 때 선호하는 음악 장르 : </label>
                    <select name="sad_genre_music" id="sad_genre_music" required>
                        <option value="">선택하세요</option>
                        <option value="댄스"> 댄스
                        <option value="발라드"> 발라드
                        <option value="랩/힙합"> 랩/힙합
                        <option value="r&b/soul"> R&B/Soul
                        <option value="인디"> 인디
                        <option value="rock/metal"> 록/메탈
                        <option value="트로트"> 트로트
                        <option value="포크/블루스"> 포크/블루스
                        <option value="pop"> POP
                        <option value="ost"> OST
                        <option value="클래식"> 클래식
                        <option value="j-pop"> J-Pop
                        <option value="재즈"> 재즈
                        <option value="애니메이션"> 애니메이션
                    </select><br>
                </div>
                <input type="submit" value="회원가입">
                <input type="reset" value="취소" onClick="location.href='login.php'">
            </div>
    </form>
</body>

</html>