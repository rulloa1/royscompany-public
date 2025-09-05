<?php

// 🎮 Discord configuration
$discordWebhookUrl = getenv('DISCORD_WEBHOOK_URL') ?: '';

function sendDiscordAlert($webhookUrl, $data, $intel) {
    if (empty($webhookUrl)) return false;
    
    $embed = [
        'title' => '🚨 New Visitor Tracked!',
        'description' => 'Someone just visited the tracking page',
        'color' => 0xFF5733, // Orange color
        'timestamp' => date('c'),
        'fields' => [
            [
                'name' => '🆔 Fingerprint',
                'value' => $data['fingerprint'],
                'inline' => true
            ],
            [
                'name' => '🌍 IP Address',
                'value' => $data['ip'],
                'inline' => true
            ],
            [
                'name' => '🌎 Location',
                'value' => "{$intel['city']}, {$intel['country']}",
                'inline' => true
            ],
            [
                'name' => '🏢 ISP/Organization',
                'value' => $intel['org'],
                'inline' => true
            ],
            [
                'name' => '📱 Screen Resolution',
                'value' => $data['screen'],
                'inline' => true
            ],
            [
                'name' => '🗣️ Language',
                'value' => $data['lang'],
                'inline' => true
            ],
            [
                'name' => '🔗 Referrer',
                'value' => !empty($data['referrer']) ? $data['referrer'] : 'Direct visit',
                'inline' => false
            ],
            [
                'name' => '🕐 Timezone',
                'value' => $data['timezone'],
                'inline' => true
            ],
            [
                'name' => '⏰ Logged At',
                'value' => $data['logged_at'],
                'inline' => true
            ]
        ],
        'footer' => [
            'text' => 'TweakSquad Tracker',
            'icon_url' => 'https://cdn.discordapp.com/embed/avatars/0.png'
        ]
    ];
    
    $payload = [
        'username' => 'TweakSquad Tracker',
        'avatar_url' => 'https://cdn.discordapp.com/embed/avatars/0.png',
        'embeds' => [$embed]
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($payload)
        ]
    ];
    
    $context = stream_context_create($options);
    return @file_get_contents($webhookUrl, false, $context);
}

function fetchIntel($ip) {
    $res = @file_get_contents("https://ipapi.co/{$ip}/json/");
    if (!$res) return ['country' => 'Unknown', 'org' => 'N/A', 'city' => 'N/A'];
    $data = json_decode($res, true);
    return [
        'country' => $data['country_name'] ?? 'Unknown',
        'city'    => $data['city'] ?? 'N/A',
        'org'     => $data['org'] ?? 'N/A',
        'timezone'=> $data['timezone'] ?? 'N/A'
    ];
}

// 🧠 Collect & enrich data
$data = json_decode(file_get_contents('php://input'), true);
$data['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$data['agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$data['logged_at'] = date("Y-m-d H:i:s");
$redirected = $data['redirected'] ?? false;
$intel = fetchIntel($data['ip']);

// 💾 Log everything to log.csv
$f1 = fopen("log.csv", "a");
fputcsv($f1, [
    $data['fingerprint'],
    $data['ip'],
    $data['agent'],
    $data['screen'],
    $data['lang'],
    $data['timezone'],
    $data['referrer'],
    $intel['country'],
    $intel['city'],
    $intel['org'],
    $intel['timezone'],
    $data['logged_at']
]);
fclose($f1);

// 🎮 Send Discord notification for all visitors
sendDiscordAlert($discordWebhookUrl, $data, $intel);

// 🌀 If redirected, log to separate file
if ($redirected) {
    $f2 = fopen("redirect_log.csv", "a");
    fputcsv($f2, [
        $data['fingerprint'],
        $data['ip'],
        $data['screen'],
        $data['lang'],
        $data['referrer'],
        $data['timezone'],
        $data['logged_at']
    ]);
    fclose($f2);
    
    // 🎮 Send Discord alert for redirected users
    sendDiscordAlert($discordWebhookUrl, $data, $intel);
}

echo json_encode(['status' => 'logged']);
