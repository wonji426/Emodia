<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';
require_once 'ChatGPTRequest.php';

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['message'])) {
        throw new Exception('메시지가 없습니다.');
    }

    $chatGPT = new ChatGPTRequest(OPENAI_API_KEY);
    
    // 시스템 메시지 설정
    $systemMessage = "당신은 영화와 음악 추천 전문가입니다. 영화와 음악에 대한 지식의 폭이 넓습니다. 영화나 음악을 추천하는 것에 있어서 재능이 있습니다. 영화나 음악 이외에 질문은 쿠션어를 이용하여 정중히 거절한다.";
    if (isset($data['category'])) {
        if ($data['category'] === 'movie') {
            $systemMessage .= "영화 추천시 제목, 개봉년도, 장르, 간단한 설명을 포함해 구조화된 형식으로 답변해주세요.";
        } else if ($data['category'] === 'music') {
            $systemMessage .= "음악 추천시 곡명, 아티스트, 발매년도를 포함해 구조화된 형식으로 답변해주세요.";
        }
    }

    $response = $chatGPT->sendRequest($data['message'], $systemMessage);
    
    // 응답 텍스트에 줄바꿈 추가
    $responseText = $response['choices'][0]['message']['content'];
    $responseText = nl2br($responseText);
    
    echo json_encode([
        'success' => true,
        'response' => $responseText
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>