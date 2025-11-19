<style>
.content-box {
  max-width: 95vw;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
  overflow-x: auto;
}

.content-box pre {
  max-width: 100%;
  overflow-x: auto;
  white-space: pre-wrap;
  word-wrap: break-word;
}
</style>
<?php

if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $raw = isset($_GET['raw']);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_ALL);
    curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_ALL);

    $content = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($content === false) {
        echo "<h2>Error fetching URL</h2>";
        echo "<a href='javascript:history.back()'>← Back</a>";
        echo "<p>URL: " . htmlspecialchars($url) . "</p>";
        echo "<p>Error: " . htmlspecialchars($error) . "</p>";
        exit;
    }
    
    echo "<div class='content-box'>";
    echo "<h2>Content from: " . htmlspecialchars($url) . "</h2>";
    echo "<a href='javascript:history.back()'>← Back</a>";
    echo "<hr>";

    if ($raw) {
        // Display raw content (vulnerable to XSS)
        echo $content;
    } else {
        // Display encoded (safe from XSS)
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
    }

    echo "</div>";
} else {
    echo "Usage: fetch.php?url=http://example.com&raw=1";
}
?>
