<?php
// Basic SSRF - fetches any URL provided
// Uses cURL to support more protocols including gopher://

if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $raw = isset($_GET['raw']);

    // NO VALIDATION - this is the vulnerability!
    // Using cURL instead of file_get_contents to support gopher protocol

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    // Enable all protocols including gopher, file, dict, etc.
    curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_ALL);
    curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_ALL);

    $content = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($content === false) {
        echo "<h2>Error fetching URL</h2>";
        echo "<p>URL: " . htmlspecialchars($url) . "</p>";
        echo "<p>Error: " . htmlspecialchars($error) . "</p>";
        exit;
    }

    echo "<h2>Content from: " . htmlspecialchars($url) . "</h2>";
    echo "<hr>";

    if ($raw) {
        // Display raw content (could execute if HTML/JS)
        echo $content;
    } else {
        // Display encoded (safe)
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
    }
} else {
    echo "Usage: fetch.php?url=http://example.com&raw=1 (optional)";
}
?>