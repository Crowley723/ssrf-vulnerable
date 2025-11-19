<?php
$redis_host = getenv('REDIS_HOST') ?: 'ssrf_redis';
$redis_port = getenv('REDIS_PORT') ?: 6379;

try{
    $redis = new Redis();
    $redis->connect($redis_host, $redis_port);
    $redis_connected = true;
} catch (Exception $e) {
    $redis_connected = false;
    $redis_error = $e->getMessage();
}


function start_session() {
    global $redis, $redis_connected;

    if (!$redis_connected) return false;

    if (isset($_COOKIE['sessionid'])) {
        $sessionid = $_COOKIE['sessionid'];
        $user = $redis->get("session:$sessionid");
        if ($user) {
            return $user;
        }
    }
    return false;
}

function create_session($username) {
    global $redis, $redis_connected;

    if (!$redis_connected) return false;

    $sessionid = bin2hex(random_bytes(16));
    $redis->setEx("session:$sessionid", 3600, $username); // 1 hour expiry
    setcookie('sessionid', $sessionid, time() + 3600, '/');
    return $sessionid;
}

function destroy_session() {
    global $redis, $redis_connected;

    if (!$redis_connected) return;

    if (isset($_COOKIE['sessionid'])) {
        $sessionid = $_COOKIE['sessionid'];
        $redis->del("session:$sessionid");
    }
    setcookie('sessionid', '', time() - 3600, '/');
}

$current_user = start_session();

$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simple hardcoded credentials for demo
    if ($username === 'admin' && $password === 'password') {
        create_session($username);
        $current_user = $username;
        header('Location: index.php');
        exit;
    } else {
        $login_error = 'Invalid credentials';
    }
}

if (isset($_GET['logout'])) {
    destroy_session();
    $current_user = false;
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 20px;
        }
        input[type="text"],
        input[type="password"] {
            padding: 8px;
            font-size: 14px;
        }
        button {
            padding: 8px;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($current_user): ?>
            <h2>Already logged in as <?php echo htmlspecialchars($current_user); ?></h2>
            <p><a href="index.php">Go to Home</a></p>
        <?php else: ?>
            <h2>Login</h2>
            <?php if ($login_error): ?>
                <p style="color: red;"><?php echo htmlspecialchars($login_error); ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Username" value="">
                <input type="password" name="password" placeholder="Password" value="">
                <input type="hidden" name="action" value="login">
                <button type="submit">Login</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>