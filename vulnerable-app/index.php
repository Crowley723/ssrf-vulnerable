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
?>

<!DOCTYPE html>
<html>
<head>
    <title>URL Fetcher</title>
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
        .feature {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="user-info">
            <?php if ($current_user): ?>
                <span>Welcome, <?php echo htmlspecialchars($current_user); ?></span>
                <a href="internal_api.php">Internal API</a>
                <a href="login.php?logout=1">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="feature">
        <h2>URL Content Fetcher</h2>
        <p>Fetch and display content from any URL</p>
        <form action="fetch.php" method="GET">
            <input type="text" name="url" placeholder="http://example.com" value="http://example.com">
            <label>
                <input type="checkbox" name="raw" value="1"> Show unencoded response
            </label>
            <button type="submit">Fetch URL</button>
        </form>
    </div>
</body>
</html>
