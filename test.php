<?php
header('Content-Type: application/json');

$response = [
    'status' => 'PHP is working!',
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => $_SERVER['SERVER_NAME'] ?? 'unknown',
    'discord_configured' => !empty(getenv('DISCORD_WEBHOOK_URL')) ? 'Yes' : 'No',
    'php_version' => phpversion()
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
