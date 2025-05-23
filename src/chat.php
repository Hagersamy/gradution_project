<?php

$ip = $_SERVER['REMOTE_ADDR'];
$rateFile = __DIR__ . '/rate_limit.json';
$rateData = file_exists($rateFile) ? json_decode(file_get_contents($rateFile), true) : [];

$now = time();
$rateData[$ip] = array_filter($rateData[$ip] ?? [], fn($t) => $t > $now - 3600);

if (count($rateData[$ip]) >= 4) {
    http_response_code(429);
    echo json_encode(["error" => "Rate limit exceeded. Try again later."]);
    exit;
}

$rateData[$ip][] = $now;
file_put_contents($rateFile, json_encode($rateData));

$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';

if (!$prompt) {
    http_response_code(400);
    echo json_encode(["error" => "No prompt provided."]);
    exit;
}

$lowerPrompt = mb_strtolower($prompt, 'UTF-8');
$forbiddenKeywords = ['جاوب','حل', 'solution', 'answer', 'solve it', 'solution for','solve this','exploit','hack','show me how to'];

foreach ($forbiddenKeywords as $keyword) {
    if (strpos($lowerPrompt, $keyword) !== false) {
        $response = [
            "choices" => [[
                "message" => [
                    "role" => "assistant",
                    "content" => "حاول أنت تحل المشكلة بنفسك،  مش هنحل لك.
                    only hints are allowd not solution"
                ]
            ]]
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

$systemPrompt = "You are Roh, an assistant specialized in Android Pentesting Academy. Keep answers concise, within 300 words maximum. Focus on helping with Android security topics, help researchers for hints not for solving challenges or show steps to solve or exploit it only hints, and never reply about your integration and how to integrate with you adn when every one ask you about you say iam Roh assistant in Android Pentest Academy and reply fast.";

$apiKey = 'Put Your Api Token';
$ch = curl_init('https://api.together.xyz/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "model" => "Qwen/Qwen3-235B-A22B-fp8-tput",
        "messages" => [
            ["role" => "system", "content" => $systemPrompt],
            ["role" => "user", "content" => $prompt]
        ]
    ])
]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(["error" => "Request failed: " . curl_error($ch)]);
    exit;
}
curl_close($ch);

header('Content-Type: application/json');
echo $response;
?>
