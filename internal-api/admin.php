<?php
// Internal admin API - should only be accessible from internal network
header('Content-Type: application/json');

// Connect to SQLite database with CORRECT path
$db_path = '/var/www/data/users.db';

if (!file_exists($db_path)) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database not found',
        'expected_path' => $db_path,
        'message' => 'Database may not be initialized yet'
    ]);
    exit;
}

try {
    $db = new SQLite3($db_path);

    // Get all users
    $result = $db->query('SELECT * FROM users');

    $users = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $users[] = $row;
    }

    // Get sensitive data
    $sensitive_result = $db->query('SELECT * FROM sensitive_data');
    $sensitive_data = [];
    while ($row = $sensitive_result->fetchArray(SQLITE3_ASSOC)) {
        $sensitive_data[] = $row;
    }

    $db->close();

    echo json_encode([
        'status' => 'success',
        'message' => '⚠️ SENSITIVE INTERNAL DATA - Should not be externally accessible!',
        'warning' => 'This endpoint contains credentials and secrets',
        'users' => $users,
        'sensitive_data' => $sensitive_data,
        'server_info' => [
            'hostname' => gethostname(),
            'php_version' => phpversion(),
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
}
?>