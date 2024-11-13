<?php
// crawler.php
require_once('simple_html_dom.php');

// Function to safely create JSON
function crawlMashable() {
    $articles = array();
    
    try {
        // Create curl instance
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://mashable.com');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        // Get the HTML content
        $html = curl_exec($ch);
        curl_close($ch);

        if ($html === false) {
            throw new Exception('Failed to fetch content');
        }

        // Create DOM from HTML
        $dom = str_get_html($html);
        
        if (!$dom) {
            throw new Exception('Failed to parse HTML');
        }

        // Find all articles
        foreach($dom->find('article') as $article) {
            $title_elem = $article->find('h2', 0);
            $link_elem = $article->find('a', 0);
            $date_elem = $article->find('time', 0);
            
            if ($title_elem && $link_elem) {
                $title = trim($title_elem->plaintext);
                $link = $link_elem->href;
                $date = date('Y-m-d H:i:s'); // Current date if no date found
                
                if ($date_elem && isset($date_elem->datetime)) {
                    $date = $date_elem->datetime;
                }
                
                // Ensure link is absolute
                if (!preg_match("~^(?:f|ht)tps?://~i", $link)) {
                    $link = "https://mashable.com" . $link;
                }
                
                $articles[] = array(
                    'title' => $title,
                    'link' => $link,
                    'date' => $date
                );
            }
        }

        // Clear up memory
        $dom->clear();
        unset($dom);

        // Sort articles by date (newest first)
        usort($articles, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $articles;

    } catch (Exception $e) {
        error_log("Crawler error: " . $e->getMessage());
        return array();
    }
}

// Execute the crawler and save results
try {
    $articles = crawlMashable();
    
    if (!empty($articles)) {
        // Ensure proper JSON encoding
        $json = json_encode($articles, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        if ($json === false) {
            throw new Exception('JSON encoding failed: ' . json_last_error_msg());
        }
        
        // Write to file
        $result = file_put_contents('articles.json', $json);
        
        if ($result === false) {
            throw new Exception('Failed to write to articles.json');
        }
        
        echo "Crawler completed successfully. Articles saved: " . count($articles) . "\n";
    } else {
        echo "No articles found.\n";
    }
    
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}
?>