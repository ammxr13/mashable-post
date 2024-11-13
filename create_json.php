<?php
// create_json.php
header('Content-Type: text/plain');

$directory = 'crawler';
$file = $directory . '/articles.json';

echo "Initializing articles.json...\n";

try {
    // Create crawler directory if it doesn't exist
    if (!is_dir($directory)) {
        if (!mkdir($directory, 0755, true)) {
            throw new Exception("Failed to create crawler directory");
        }
        echo "Created crawler directory\n";
    }

    // Create empty articles array
    $initial_data = [];
    
    // Convert to JSON
    $json = json_encode($initial_data, JSON_PRETTY_PRINT);
    
    // Write to file
    if (file_put_contents($file, $json) === false) {
        throw new Exception("Failed to write to articles.json");
    }
    
    echo "Successfully created articles.json\n";
    echo "File permissions: " . substr(sprintf('%o', fileperms($file)), -4) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>