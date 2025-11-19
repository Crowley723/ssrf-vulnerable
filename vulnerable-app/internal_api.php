<?php
$redis_host = getenv('REDIS_HOST') ?: 'ssrf_redis';
$redis_port = getenv('REDIS_PORT') ?: 6379;

try{
    $redis = new Redis();
    $redis->connect($redis_host, $redis_port);
    $redis_connected = true;
} catch (Exception $e) {
    $redis_connected = false;
}

$current_user = false;
if ($redis_connected && isset($_COOKIE['sessionid'])) {
    $sessionid = $_COOKIE['sessionid'];
    $current_user = $redis->get("session:$sessionid");
}

if (!$current_user) {
    header('Location: login.php');
    exit;
}

$internal_api_url = getenv('INTERNAL_API_URL') ?: 'http://internal-api';
$api_content = '';
$api_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $internal_api_url . '/admin.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $api_content = curl_exec($ch);
    $api_error = curl_error($ch);
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Internal API Access</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: flex-end;
            padding: 10px 20px;
            background-color: #f0f0f0;
        }
        .user-info {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        pre {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        button {
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($current_user); ?></span>
            <a href="index.php">Home</a>
            <a href="login.php?logout=1">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Internal API Access</h2>
        <p>Access the internal admin API (requires authentication)</p>

        <form method="POST">
            <button type="submit">Fetch Internal API Data</button>
        </form>

        <?php if ($api_content): ?>
            <h3>API Response:</h3>
            <pre><?php echo htmlspecialchars($api_content); ?></pre>
        <?php endif; ?>

        <?php if ($api_error): ?>
            <p style="color: red;">Error: <?php echo htmlspecialchars($api_error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
