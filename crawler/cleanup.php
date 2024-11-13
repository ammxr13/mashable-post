<?php
// cleanup.php
header('Content-Type: text/plain');

$file = 'crawler/articles.json';
echo "Cleaning up articles.json...\n";

try {
    // Check if file exists
    if (!file_exists($file)) {
        throw new Exception("articles.json not found");
    }

    // Read the file
    $content = file_get_contents($file);
    if ($content === false) {
        throw new Exception("Could not read articles.json");
    }

    // Try to decode current content
    $data = json_decode($content, true);
    
    if ($data === null) {
        // File contains invalid JSON, create empty array
        $data = array();
        echo "Invalid JSON found, resetting to empty array\n";
    }

    // Ensure it's an array
    if (!is_array($data)) {
        $data = array();
        echo "Content was not an array, resetting\n";
    }

    // Re-encode with proper formatting
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    if ($json === false) {
        throw new Exception("JSON encoding failed: " . json_last_error_msg());
    }

    // Write back to file
    $result = file_put_contents($file, $json);
    
    if ($result === false) {
        throw new Exception("Could not write to articles.json");
    }

    echo "Cleanup completed successfully\n";
    echo "Current article count: " . count($data) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>