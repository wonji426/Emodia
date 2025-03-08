<?php
// API 키가 올바르게 설정되어 있는지 확인
define('OPENAI_API_KEY', ''); // 실제 API 키로 교체

// 타임존 설정
date_default_timezone_set('Asia/Seoul');

// 에러 로깅 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'error.log');
?>