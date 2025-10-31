<div class="feature">
    <h2>Feature 1: URL Content Fetcher</h2>
    <p>Fetch and display content from any URL</p>
    <form action="fetch.php" method="GET">
        <input type="text" name="url" placeholder="http://example.com" value="http://example.com">
        <label>
            <input type="checkbox" name="raw" value="1"> Show raw (unencoded)
</label>
        <button type="submit">Fetch URL</button>
    </form>
    <p><small>Try: file:///etc/passwd, http://internal-api/admin.php</small></p>
</div>