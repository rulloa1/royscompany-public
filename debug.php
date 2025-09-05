<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Deployment Debug - TweakSquad Tracker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 Deployment Debug - TweakSquad Tracker</h1>
    
    <div class="status info">
        <h3>📊 System Information</h3>
        <ul>
            <li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
            <li><strong>Server:</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'Unknown'; ?></li>
            <li><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></li>
            <li><strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s T'); ?></li>
        </ul>
    </div>

    <div class="status <?php echo !empty(getenv('DISCORD_WEBHOOK_URL')) ? 'success' : 'error'; ?>">
        <h3>🎮 Discord Configuration</h3>
        <?php if (!empty(getenv('DISCORD_WEBHOOK_URL'))): ?>
            <p>✅ Discord webhook is configured!</p>
            <p>Webhook: <?php echo substr(getenv('DISCORD_WEBHOOK_URL'), 0, 50) . '...'; ?></p>
        <?php else: ?>
            <p>❌ Discord webhook is NOT configured</p>
            <p>Please add DISCORD_WEBHOOK_URL environment variable</p>
        <?php endif; ?>
    </div>

    <div class="status info">
        <h3>📁 File Structure</h3>
        <pre><?php
            $files = [
                'index.html' => file_exists('index.html'),
                'tracker.html' => file_exists('tracker.html'), 
                'logger.php' => file_exists('logger.php'),
                'admin.html' => file_exists('admin.html'),
                'test.php' => file_exists('test.php'),
                'log.csv' => file_exists('log.csv'),
            ];
            foreach($files as $file => $exists) {
                echo $exists ? "✅ $file\n" : "❌ $file\n";
            }
        ?></pre>
    </div>

    <div class="status info">
        <h3>🔗 Test Links</h3>
        <ul>
            <li><a href="/test.php">PHP Test</a></li>
            <li><a href="/tracker.html">Tracker Page (Google Style)</a></li>
            <li><a href="/admin.html">Admin Panel</a></li>
            <li><a href="/logger.php" target="_blank">Logger Endpoint</a></li>
        </ul>
    </div>

    <div class="status info">
        <h3>🌐 Environment Variables</h3>
        <pre><?php
            $env_vars = ['DISCORD_WEBHOOK_URL', 'PHP_VERSION', 'RENDER_SERVICE_NAME'];
            foreach($env_vars as $var) {
                $value = getenv($var);
                if ($value) {
                    if ($var === 'DISCORD_WEBHOOK_URL') {
                        echo "$var: " . substr($value, 0, 30) . "...\n";
                    } else {
                        echo "$var: $value\n";
                    }
                } else {
                    echo "$var: Not set\n";
                }
            }
        ?></pre>
    </div>
</body>
</html>
