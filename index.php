<?php
session_start();
$conn = mysqli_connect('localhost', '', '', '')
	or die("데이터베이스 연결 오류");

// 자동 로그인 체크
if(!isset($_SESSION['id']) && isset($_COOKIE['auto_login'])) {
    $token = $_COOKIE['auto_login'];
    $sql = "SELECT * FROM membertable WHERE remember_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['id'] = $row['id'];
    }
}

$isLoggedIn = isset($_SESSION['id']);

$where = "";

if (isset($_GET["search"]) && $_GET["search"] != "") {
    // 사용자 입력 이스케이프 처리
    $search = mysqli_real_escape_string($conn, $_GET["search"]);
    $where = "where no = '$search' or title = '$search' or year = '$search' or genre = '$search' or rating = '$search' or poster = '$search'";
}

$query = "select * from movie " . $where;
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>

<head>
	<meta name="Title" content="감정을 읽는 미디어 추천 사이트">
	<title>Emodia</title>
	<link rel="stylesheet" href="./css/style.css">
	<link rel="icon" href="data:;base64,iVBORw0KGgo=">
	<script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
	<script
		src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
	<script src="https://d3js.org/d3.v6.min.js"></script>
	<script src="./js/script.js"></script>
	<script src="./js/chatgpt.js" defer></script>
	<script src="./js/KobisOpenAPIRestService.js"></script>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script>
		var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
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

	<div id="loading" style="display: none;">
		<div class="spinner"></div>
		<p class="loading-text">사진을 분석하는 중입니다...</p>
	</div>
	
	<button id="scrollToTopBtn" title="Go to top">↑</button>

	<h1>감정을 읽는 미디어 추천 사이트</h1>
	<h2>"당신의 감정을 읽고 추천 컨텐츠를 보여드립니다."</h2>
	<button class="camera-start-btn" type="button" onclick="if(checkLoginStatusMain('webcam')) init()">웹캠 사진 감정 분석</button>

	<div id="image-content" class="image-content">
		<div class="file-upload-container">
			<div class="file-upload">
				<button class="file-upload-btn" type="button" onclick="if(checkLoginStatusMain('webcam')) $('.file-upload-input').trigger('click')">
					<span class="text-wide">사진 감정 분석</span>
					<span class="text-narrow">카메라 감정 분석(사진)</span>
				</button>
				<div class="image-upload-wrap">
					<input class="file-upload-input" type='file' onchange="if(checkLoginStatusMain('webcam')) readURL(this);" accept="image/*" />
					<div class="drag-text">
						<h3 class="drag-text-h3">파일을 끌어다 놓거나 이미지 추가를 선택합니다.</h3>
					</div>
				</div>
				<div class="file-upload-content">
					<img class="file-upload-image" id="face-image" src="#" alt="your image" />
					<div class="image-title-wrap">
						<button type="button" onclick="removeUpload()" class="remove-image">
							<span class="image-title">Uploaded Image</span> 지우기
						</button>
						<button type="button" class="initpic-btn" onclick="if(checkLoginStatusMain('webcam')) initPic()" style.display="none">분석
							시작</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="webcam-content" style="display:none">
		<button type="button" onclick="stopWebcam()" class="stop-Webcam">취소하기</button>
		<div class="webcam-container">
			<div id="webcam-container"></div>
		</div>
	</div>

	<div id="chart-container"></div>
	<div id="label-container"></div>
	<div id="highest-prediction-container"></div>
	<div id="recommendation-container"></div>
	<div class="video-container" style="display:none">
		<div id="player" class="player" style="display:none"></div>
	</div>
	<div id="music-recommendation-container" style="display: none;">
		<h2>추천 음악</h2>
		<div class="music-scroll-container">
			<!-- 여기에 음악 아이템들이 추가됨 -->
		</div>
	</div>
	<!-- <div id="music-recommendation-container"></div> -->

	<!-- 챗봇 버튼 -->
	<button id="chatbotBtn" class="chatbot-btn">AI 추천</button>

	<!-- 챗봇 모달 -->
	<div id="chatbotModal" class="modal">
		<div class="modal-content">
			<div class="modal-header">
				<h2>AI 미디어 추천도우미</h2>
				<span class="close">&times;</span>
			</div>
			<div class="modal-body">
				<div id="chat-messages" class="chat-messages"></div>
				<div class="chat-input">
					<input type="text" id="user-input" placeholder="메시지를 입력하세요...">
					<button onclick="sendMessage()">전송</button>
				</div>
			</div>
		</div>
	</div>

</body>

<!-- Copyright (c) 2020 by Aaron Vanston (https://codepen.io/aaronvanston/pen/yNYOXR)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. -->

</html>

<?php
mysqli_close($conn);
?>