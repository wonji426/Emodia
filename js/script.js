// More API functions here:
// https://github.com/googlecreativelab/teachablemachine-community/tree/master/libraries/image
// the link to your model provided by Teachable Machine export panel
const URL = "./model/";
let model, webcam, labelContainer, maxPredictions;
// import wait from "waait";

// Load the image model and setup the webcam
async function initPic() {
    try {
        if (!checkLoginStatusMain('webcam')) return;

        // 로딩 화면 표시
        document.getElementById('loading').style.display = 'flex';

        const modelURL = URL + "model.json";
        const metadataURL = URL + "metadata.json";
        //load the model and metadata
        //Refer to tmImage.loadFromFiles() in the API to support files from a file picker
        //or files from your local hard drive
        //Note: the pose library adds "tmImage" object to your window (window.tmImage)

        // 모델 로드
        model = await tmImage.load(modelURL, metadataURL);
        maxPredictions = model.getTotalClasses();

        // 라벨 컨테이너 설정
        labelContainer = document.getElementById("label-container");
        for (let i = 0; i < maxPredictions; i++) {
            labelContainer.appendChild(document.createElement("div"));
        }

        // 예측 실행
        await predictPic();

    } catch (error) {
        console.error('모델 로딩 중 오류 발생:', error);
        alert('모델을 불러오는 중 오류가 발생했습니다.');
    } finally {
        // 로딩 화면 숨기기
        document.getElementById('loading').style.display = 'none';
    }
}

let webcamInitialized = false; // 웹캠 초기화 여부를 확인하는 플래그

// 이미지 모델 로드 및 웹캠 설정
async function init() {
    try {
        if (!checkLoginStatusMain('webcam')) return;

        // 로딩 화면 표시
        document.getElementById('loading').style.display = 'flex';

        const modelURL = URL + "model.json";
        const metadataURL = URL + "metadata.json";

        // 모델 및 메타데이터 로드
        // Note: 포즈 라이브러리는 창에 "tmImage" 개체를 추가합니다(window.tmImage)
        model = await tmImage.load(modelURL, metadataURL);
        maxPredictions = model.getTotalClasses();

        // 웹캠을 설정할 수 있는 편의 기능
        if (!webcamInitialized) {
            const flip = true;
            webcam = new tmImage.Webcam(400, 300, flip);
            await webcam.setup();
            await webcam.play();
            webcamInitialized = true;

            // 웹캠 컨테이너 초기화
            const webcamContainer = document.getElementById("webcam-container");
            webcamContainer.innerHTML = ''; // 기존 내용 제거
            webcamContainer.appendChild(webcam.canvas);

            webcam.canvas.style.width = '100%';
            webcam.canvas.style.height = 'auto';
        }

        window.requestAnimationFrame(loop);

        // DOM에 요소 추가
        document.getElementById("webcam-container").appendChild(webcam.canvas);
        labelContainer = document.getElementById("label-container");
        for (let i = 0; i < maxPredictions; i++) { // 및 클래스 레이블
            labelContainer.appendChild(document.createElement("div"));
        }
    } catch (error) {
        console.error('모델 로딩 중 오류 발생:', error);
        alert('모델을 불러오는 중 오류가 발생했습니다.');
    } finally {
        // 로딩 화면 숨기기
        document.getElementById('loading').style.display = 'none';
    }
}

async function loop() {
    webcam.update(); // 웹캠 프레임 업데이트
    await predict();
    window.requestAnimationFrame(loop);
}

// 이미지 모델을 통해 웹캠 이미지 실행
async function predict() {
    if (!checkLoginStatus('predict')) return;
    // 예측은 이미지, 비디오 또는 캔버스 html 요소를 사용
    const prediction = await model.predict(webcam.canvas);
    let highestPrediction = { className: "", probability: 0 };

    // 차트에 사용할 데이터의 배열 만들기
    const data = prediction.map(pred => ({
        className: pred.className,
        probability: pred.probability
    }));

    for (let i = 0; i < maxPredictions; i++) {
        const classPrediction =
            prediction[i].className + ": " + prediction[i].probability.toFixed(2);

        if (prediction[i].probability > highestPrediction.probability) {
            highestPrediction = prediction[i];
        }
    }
    document.getElementById("chart-container").style.display = "block";
    document.getElementById("webcam-content").style.display = "block";
    document.getElementById("highest-prediction-container").style.display = "block";
    document.getElementById("label-container").style.display = "block";
    document.getElementById("recommendation-container").style.display = "block";
    document.getElementById("music-recommendation-container").style.display = "block";


    document.getElementById("highest-prediction-container").innerHTML =
        "가장 높은 확률의 감정: " + highestPrediction.className + " (" + highestPrediction.probability.toFixed(2) + ")";

    // 차트 업데이트
    updateChart(data);
    recommendMovies(highestPrediction.className);
    pause();
}

// run the webcam image through the image model
async function predictPic() {
    if (!checkLoginStatus('predict')) return;
    // predict can take in an image, video or canvas html element
    var image = document.getElementById("face-image");
    const prediction = await model.predict(image, false);
    let highestPrediction = { className: "", probability: 0 };

    // Create an array for the data to be used in the chart
    const data = prediction.map(pred => ({
        className: pred.className,
        probability: pred.probability
    }));

    for (let i = 0; i < maxPredictions; i++) {
        const classPrediction =
            prediction[i].className + ": " + prediction[i].probability.toFixed(2);
        //labelContainer.childNodes[i].innerHTML = classPrediction;

        if (prediction[i].probability > highestPrediction.probability) {
            highestPrediction = prediction[i];
        }
    }
    document.getElementById("chart-container").style.display = "block";
    document.getElementById("highest-prediction-container").style.display = "block";
    document.getElementById("label-container").style.display = "block";
    document.getElementById("recommendation-container").style.display = "block";
    document.getElementById("music-recommendation-container").style.display = "block";

    document.getElementById("highest-prediction-container").innerHTML =
        "가장 높은 확률의 감정: " + highestPrediction.className + " (" + highestPrediction.probability.toFixed(2) + ")";

    // Update the chart
    updateChart(data);
    recommendMovies(highestPrediction.className);
}

document.addEventListener('DOMContentLoaded', function () {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    const body = document.body;

    navbarToggle.addEventListener('click', function () {
        navbarMenu.classList.toggle('active');
    });
});

function updateChart(data) {

    // 전달받은 data를 currentData에 할당
    currentData = data;

    // 차트 컨테이너의 크기를 가져오고 최소 너비를 설정합니다
    const container = document.getElementById('chart-container');
    if (!container) return;  // 컨테이너가 없으면 함수 종료

    const containerWidth = Math.max(200, container.clientWidth);

    // 차트의 크기와 여백을 설정합니다
    const margin = { top: 30, right: 55, bottom: 30, left: 40 };
    const width = containerWidth - margin.left - margin.right;
    const height = Math.min(300, window.innerHeight * 0.7) - margin.top - margin.bottom;

    // 기존 SVG 요소를 제거합니다
    d3.select("#chart-container").selectAll("*").remove();

    // SVG 객체를 페이지에 추가합니다
    const svg = d3.select("#chart-container")
        .append("svg")
        .attr("width", containerWidth)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`);

    // X 축 스케일을 업데이트합니다
    const x = d3.scaleLinear()
        .domain([0, 1])
        .range([0, width])
        .clamp(true);

    svg.append("g")
        .attr("transform", `translate(0,${height})`)
        .call(d3.axisBottom(x).ticks(5).tickFormat(d3.format(".0%")));

    // Y 축 스케일을 업데이트합니다
    const y = d3.scaleBand()
        .range([0, height])
        .domain(data.map(d => d.className))
        .padding(0.1);

    svg.append("g")
        .call(d3.axisLeft(y));

    // 막대를 업데이트합니다
    svg.selectAll("rect")
        .data(data)
        .enter()
        .append("rect")
        .attr("x", x(0))
        .attr("y", d => y(d.className))
        .attr("width", d => Math.max(0, x(d.probability) - x(0)))
        .attr("height", y.bandwidth())
        .attr("fill", "#69b3a2");

    // 확률 값 표시를 업데이트합니다
    svg.selectAll(".probability-label")
        .data(data)
        .enter()
        .append("text")
        .attr("class", "probability-label")
        .attr("x", d => x(d.probability) + 5)
        .attr("y", d => y(d.className) + y.bandwidth() / 2)
        .attr("dy", ".35em")
        .text(d => d3.format(".1%")(d.probability));
}

// 디바운스 함수
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// 창 크기 변경 시 차트를 다시 그립니다 (디바운스 적용)
const debouncedUpdateChart = debounce(() => {
    updateChart(currentData);
}, 250);

window.addEventListener('resize', debouncedUpdateChart);

// 초기 데이터와 차트 생성
let currentData = [];

// DOM이 로드된 후 차트를 그립니다
document.addEventListener('DOMContentLoaded', () => {
    updateChart(currentData);
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('.image-upload-wrap').hide();
            $('.file-upload-image').attr('src', e.target.result);
            $('.file-upload-content').show();
            $('.image-title').html(input.files[0].name);
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        removeUpload();
    }
}

function removeUpload() {
    $('.file-upload-input').replaceWith($('.file-upload-input').clone());
    $('.file-upload-content').hide();
    $('.image-upload-wrap').show();
    // Hide the chart container when the image is removed
    document.getElementById("chart-container").style.display = "none";
    document.getElementById("webcam-content").style.display = "none";
    document.getElementById("player").style.display = "none";
    const container = document.querySelector(".video-container");
    if (container) {
        container.style.display = "none";
    }
    document.getElementById("highest-prediction-container").style.display = "none";
    document.getElementById("label-container").style.display = "none";
    document.getElementById("recommendation-container").style.display = "none";
    document.getElementById("music-recommendation-container").style.display = "none";
}
$('.image-upload-wrap').bind('dragover', function () {
    $('.image-upload-wrap').addClass('image-dropping');
});
$('.image-upload-wrap').bind('dragleave', function () {
    $('.image-upload-wrap').removeClass('image-dropping');
});

function stopWebcam() {
    if (webcam) {
        webcam.stop();
        webcamInitialized = false;
        const webcamContainer = document.getElementById("webcam-container");
        webcamContainer.innerHTML = '';
    }
    // 관련 요소들을 숨김
    document.getElementById("chart-container").style.display = "none";
    document.getElementById("webcam-content").style.display = "none";
    document.getElementById("player").style.display = "none";
    const container = document.querySelector(".video-container");
    if (container) {
        container.style.display = "none";
    }
    document.getElementById("highest-prediction-container").style.display = "none";
    document.getElementById("label-container").style.display = "none";
    document.getElementById("recommendation-container").style.display = "none";
    document.getElementById("music-recommendation-container").style.display = "none";
}

function searchYouTube(title) {
    var encodedTitle = encodeURIComponent(title + " 영화 예고편");
    var url = "https://www.youtube.com/results?search_query=" + encodedTitle;
    window.open(url, '_blank');
}

function searchMovie(title) {
    var choice = prompt("검색할 사이트를 선택하세요:\n1. 네이버\n2. 다음\n3. 유튜브\n숫자를 입력해 주세요.");
    var encodedTitle = encodeURIComponent(title);
    var url;

    switch (choice) {
        case null:    // 취소 버튼을 눌렀을 때
        case "":      // ESC를 눌렀을 때
            return;   // 조용히 함수 종료
        case "1":
            url = "https://search.naver.com/search.naver?query=" + encodedTitle + "+영화";
            break;
        case "2":
            url = "https://search.daum.net/search?q=" + encodedTitle + "+영화";
            break;
        case "3":
            url = "https://www.youtube.com/results?search_query=" + encodedTitle + "+영화+예고편";
            break;
        default:
            alert("잘못된 선택입니다.");
            return;
    }

    window.open(url, '_blank');
}

// YouTube Data API 키 설정
async function recommendMusic(emotion) {
    try {
        // 데이터베이스에서 사용자의 선호 장르 가져오기
        const response = await fetch('get_user_genre.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `emotion=${encodeURIComponent(emotion)}`
        });
        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        const preferredGenre = data.genre;

        // YouTube Data API를 사용하여 음악 검색
        const API_KEY = ''; // YouTube API 키를 여기에 입력하세요

        const searchQuery = `${preferredGenre} 음악`;
        const youtubeResponse = await fetch(`https://www.googleapis.com/youtube/v3/search?part=snippet&q=${encodeURIComponent(searchQuery)}&type=video&videoCategoryId=10&key=${API_KEY}&maxResults=5`);
        const youtubeData = await youtubeResponse.json();

        // 검색 결과에서 음악 정보 추출
        const recommendations = youtubeData.items.map(item => ({
            title: item.snippet.title,
            videoId: item.id.videoId,
            thumbnail: item.snippet.thumbnails.default.url
        }));

        return recommendations;
    } catch (error) {
        console.error('음악 추천 중 오류 발생:', error);
        return [];
    }
}

// 음악 추천 결과 표시 함수
// function displayRecommendations(recommendations) {
//     const recommendationContainer = document.getElementById('music-recommendation-container');
//     recommendationContainer.innerHTML = '<h2>추천 음악</h2>';

//     recommendations.forEach(music => {
//         const musicElement = document.createElement('div');
//         musicElement.innerHTML = `
//         <img src="${music.thumbnail}" alt="${music.title}">
//         <p>${music.title}</p>
//         <button class="scroll_move" onclick="playMusicAndScroll('${music.videoId}')">재생</button>
//       `;
//         recommendationContainer.appendChild(musicElement);
//     });
// }

// JavaScript 함수 수정
function displayRecommendations(recommendations) {
    const scrollContainer = document.querySelector('.music-scroll-container');
    scrollContainer.innerHTML = ''; // 기존 내용 초기화

    recommendations.forEach(music => {
        const musicElement = document.createElement('div');
        musicElement.className = 'music-item';
        musicElement.innerHTML = `
            <img src="${music.thumbnail}" alt="${music.title}">
            <p>${music.title}</p>
            <button class="scroll_move" onclick="playMusicAndScroll('${music.videoId}')">재생</button>
        `;
        scrollContainer.appendChild(musicElement);
    });
}

// function displayRecommendations(recommendations) {
//     const recommendationContainer = document.getElementById('music-recommendation-container');
//     recommendationContainer.innerHTML = '<h2>추천 음악</h2>';

//     const musicGrid = document.createElement('div');
//     musicGrid.className = 'music-grid';

//     recommendations.forEach(music => {
//         const musicElement = document.createElement('div');
//         musicElement.className = 'music-item';
//         musicElement.innerHTML = `
//             <div class="music-thumbnail">
//                 <img src="${music.thumbnail}" alt="${music.title}">
//             </div>
//             <div class="music-info">
//                 <p class="music-title">${music.title}</p>
//                 <button class="scroll_move play-button" 
//                         onclick="playMusicAndScroll('${music.videoId}')">
//                     재생
//                 </button>
//             </div>
//         `;
//         musicGrid.appendChild(musicElement);
//     });

//     recommendationContainer.appendChild(musicGrid);
// }

// 음악 재생 및 페이지 최상단으로 스크롤하는 함수
function playMusicAndScroll(videoId) {
    // 음악 재생 로직 (기존 playMusic 함수의 내용을 여기에 포함)

    const container = document.querySelector(".video-container");
    if (container) {
        container.style.display = "block";
    }
    document.getElementById("player").style.display = "block";

    playMusic(videoId);

    const playerDiv = document.getElementById('player');
    if (playerDiv) {
        const offset = 100; // 위로 이동할 픽셀 수 (조절 가능)
        const rect = playerDiv.getBoundingClientRect();
        const scrollTop = document.documentElement.scrollTop;

        window.scrollTo({
            top: rect.top + scrollTop - offset,
            behavior: 'smooth'
        });
    }
}

// 음악 재생 함수
function playMusic(videoId) {
    // 기존의 player 객체를 사용하여 음악 재생
    player.loadVideoById(videoId);
}

// 감정 분석 결과에 따라 음악 추천
async function recommendMusicByEmotion(emotion) {
    const recommendations = await recommendMusic(emotion);
    displayRecommendations(recommendations);
}

// 기존 코드의 recommendMovies 함수를 수정
async function recommendMovies(emotion) {
    $.ajax({
        url: "recommend.php",
        method: "POST",
        data: { emotion: emotion },
        success: function (response) {
            $("#recommendation-container").html(response);
            // 영화 추천 후 음악 추천 실행
            recommendMusicByEmotion(emotion);
        }
    });
}

// YouTube IFrame API 로드
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;
function onYouTubeIframeAPIReady() {
    player = new YT.Player('player', {
        height: '100%',
        width: '100%',
        videoId: '',
        events: {
            'onReady': onPlayerReady
        }
    });
}

function onPlayerReady(event) {
    // 플레이어가 준비되면 실행할 코드
}

function playMusic(videoId) {
    if (player) {
        player.loadVideoById(videoId);
    }
}

// 스크롤 버튼 기능
window.onscroll = function () { scrollFunction() };

function scrollFunction() {
    var scrollToTopBtn = document.getElementById("scrollToTopBtn");
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollToTopBtn.style.display = "block";
    } else {
        scrollToTopBtn.style.display = "none";
    }
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// DOM이 로드된 후 이벤트 리스너 추가
document.addEventListener('DOMContentLoaded', function () {
    var scrollToTopBtn = document.getElementById("scrollToTopBtn");
    scrollToTopBtn.addEventListener("click", scrollToTop);
});

function redirectToLogin() {
    window.location.href = 'login.php';
    return false;
}

function checkLoginStatusMain(action) {
    if (!isLoggedIn) {
        alert("로그인이 필요한 기능입니다.");
        return false;
    }
    return true;
}

const loading = {
    start: () => {
        document.querySelector('#loading').style.display = 'flex';
    },
    end: () => {
        document.querySelector('#loading').style.display = 'none';
    }
};