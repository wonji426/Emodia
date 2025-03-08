// 모달 관련 요소
const modal = document.getElementById("chatbotModal");
const btn = document.getElementById("chatbotBtn");
const span = document.getElementsByClassName("close")[0];

// 초기 메시지와 미리 정의된 응답들
const initialMessage = `안녕하세요! AI 미디어 추천도우미입니다.

다음 중 원하시는 서비스를 선택해주세요:

1. 영화 추천
2. 음악 추천

번호를 입력하시거나 직접 질문을 입력해주세요.`;

let currentCategory = ''; // 현재 선택된 카테고리 저장

const predefinedResponses = {
    '1': `영화 추천을 선택하셨습니다.

원하는 장르를 적어주세요:
- 액션
- 로맨스
- 코미디
- SF
- 호러 등`,

    '2': `음악 추천을 선택하셨습니다.

원하는 장르를 적어주세요:
- 팝
- 록
- 클래식
- K-pop
- 재즈 등`
};

// 모달 열기
btn.onclick = function () {
    modal.style.display = "block";
    // 초기 메시지 표시
    if (document.getElementById('chat-messages').children.length === 0) {
        appendMessage('bot', initialMessage);
    }
    // 모달이 열릴 때 입력칸에 포커스
    document.getElementById('user-input').focus();
}

// 모달 닫기
span.onclick = function () {
    modal.style.display = "none";
}

// 모달 외부 클릭시 닫기
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// ESC 키로 모달 닫기
document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && modal.style.display === "block") {
        modal.style.display = "none";
    }
});

// typing indicator 상태 관리를 위한 변수
let isTyping = false;

async function sendMessage() {

    if (!checkLoginStatus()) {
        return; // 로그인되지 않은 경우 모달 열기 중단
    }

    const userInput = document.getElementById('user-input');
    const message = userInput.value.trim();

    if (!message) return;

    // 사용자 메시지 표시
    appendMessage('user', message);
    userInput.value = '';
    userInput.disabled = true;

    // 입력 중 상태 표시
    setTypingIndicator(true);

    try {
        // 번호 입력 확인
        if (['1', '2'].includes(message)) {
            currentCategory = message === '1' ? 'movie' : 'music';
            appendMessage('bot', predefinedResponses[message]);
            userInput.disabled = false;
            userInput.focus();
            return;
        }

        // API 요청 준비
        let promptMessage = message;
        if (currentCategory === 'movie') {
            promptMessage = `다음 장르의 영화를 5개 추천해주세요: ${message}. 각 영화에 대해 제목, 개봉년도, 간단한 설명을 포함해주세요.`;
        } else if (currentCategory === 'music') {
            promptMessage = `다음 장르의 음악을 5곡 추천해주세요: ${message}. 각 곡에 대해 제목, 아티스트, 발매년도를 포함해주세요.`;
        }

        // API 요청
        const response = await fetch('chat_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: promptMessage,
                category: currentCategory
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const text = await response.text();
        const data = JSON.parse(text);

        if (message.toLowerCase() === '처음으로') {
            currentCategory = '';
            appendMessage('bot', initialMessage);
            userInput.disabled = false;
            userInput.focus();
            return;
        }

        if (data.success) {
            appendMessage('bot', data.response);
            // 추천 후 카테고리 초기화
            if (currentCategory) {
                appendMessage('bot', '다른 장르의 추천이 필요하시다면 장르를 입력해주세요.\n처음으로 돌아가시려면 "처음으로"를 입력해주세요.');
            }
        } else {
            throw new Error(data.error || '알 수 없는 오류가 발생했습니다.');
        }

    } catch (error) {
        console.error('Error:', error);
        appendMessage('bot', '죄송합니다. 오류가 발생했습니다: ' + error.message);
    } finally {
        userInput.disabled = false;
        // typing indicator 제거
        setTypingIndicator(false);
        // 메시지 전송 후 입력칸에 포커스
        userInput.focus();
    }
}

// typing indicator 설정 함수
function setTypingIndicator(isTyping) {
    const userInput = document.getElementById('user-input');
    if (isTyping) {
        userInput.placeholder = '도우미가 입력중입니다...';
        userInput.disabled = true;
    } else {
        userInput.placeholder = '메시지를 입력하세요...';
        userInput.disabled = false;
    }
}

// 모달 열기 함수에 typing indicator 초기화 추가
// 모달 열기 함수 수정
btn.onclick = function() {
    modal.style.display = "block";
    if (document.getElementById('chat-messages').children.length === 0) {
        appendMessage('bot', initialMessage);
    }
    document.getElementById('user-input').focus();
}

function appendMessage(sender, message) {
    const chatMessages = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender}-message`;

    // 줄바꿈을 HTML <br> 태그로 변환
    const formattedMessage = message.replace(/\n/g, '<br>');
    messageDiv.innerHTML = formattedMessage;

    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Enter 키 이벤트 처리
document.getElementById('user-input').addEventListener('keypress', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// 로그인 상태 체크 함수
function checkLoginStatus() {
    if (!isLoggedIn) {
        alert("로그인 후 챗봇을 사용해주세요.");
        return false;
    }
    return true;
}