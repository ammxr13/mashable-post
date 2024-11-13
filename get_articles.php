<?php
header('Content-Type: application/json');

$articlesFile = 'crawler/articles.json';

if (file_exists($articlesFile)) {
    $jsonContent = file_get_contents($articlesFile);
    if ($jsonContent !== false) {
        echo $jsonContent;
    } else {
        echo json_encode([
            'error' => 'Could not read articles file',
            'path' => $articlesFile
        ]);
    }
} else {
    echo json_encode([
        'error' => 'Articles file not found',
        'path' => $articlesFile
    ]);
}
?>