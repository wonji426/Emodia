<?php
class ChatGPTRequest
{
    private string $api_key;
    private string $api_url = 'https://api.openai.com/v1/chat/completions';

    public function __construct(string $api_key)
    {
        if (empty($api_key)) {
            throw new Exception('API 키가 설정되지 않았습니다.');
        }
        $this->api_key = $api_key;
    }

    /**
     * OpenAI API에 메시지를 전송하고 응답을 받습니다.
     * 
     * @param string $message 사용자 메시지
     * @return array API 응답 데이터
     * @throws Exception cURL 오류나 API 응답 오류 발생시
     */

    public function formatResponse($content)
    {
        // 영화 추천 포맷팅
        if (strpos($content, '영화') !== false) {
            $content = str_replace(['1.', '2.', '3.', '4.', '5.'], ["1.", "2.", "3.", "4.", "5."], $content);
        }
        // 음악 추천 포맷팅
        else if (strpos($content, '음악') !== false || strpos($content, '곡') !== false) {
            $content = str_replace(['1.', '2.', '3.', '4.', '5.'], ["1.", "2.", "3.", "4.", "5."], $content);
        }

        return $content;
    }

    public function sendRequest(string $message, string $systemMessage = ''): array
    {
        if (empty($message)) {
            throw new Exception('메시지가 비어있습니다.');
        }

        $curl = curl_init();
        if (!$curl) {
            throw new Exception('cURL 초기화 실패');
        }

        $messages = [];
        if (!empty($systemMessage)) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemMessage
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 2048
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->api_key,
                'Content-Type: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new Exception('cURL Error: ' . $err);
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON 디코딩 오류: ' . json_last_error_msg());
        }

        $response = curl_exec($curl);
        $decoded = json_decode($response, true);

        if (isset($decoded['choices'][0]['message']['content'])) {
            $decoded['choices'][0]['message']['content'] = $this->formatResponse($decoded['choices'][0]['message']['content']);
        }

        return $decoded;
    }
}
