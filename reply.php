<?php
header('Content-Type: application/json');

$apiKey = "YOUR_HF_TOKEN";

$message = $_POST['message'] ?? '';

$prompt = "Explain SQL Injection simply: " . $message;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,
"https://api-inference.huggingface.co/models/google/gemma-2b-it");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "inputs" => $prompt
]));

$response = curl_exec($ch);

if(curl_errno($ch)){
    echo json_encode([
        "reply" => "cURL error: " . curl_error($ch)
    ]);
    exit;
}

curl_close($ch);

echo json_encode([
    "reply" => $response
]);