<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'lib/phpuri.php';
require 'lib/simple_html_dom.php';
require 'lib/db.php';
require 'lib/util.php';
$crawledSites = array();
$depth        = 2;
$urlToCrawl   = $argv[1];
$mainDomain   = parse_url($urlToCrawl, PHP_URL_HOST);
$linkChecked  = 0;
extractLinks($urlToCrawl, $crawledSites, $depth, $urlToCrawl);
        echo implode(' ', $crawledSites);

function extractLinks($linkToCrawl, &$crawledSites, $depth) {
    if ($depth <= 0 || urlIndexedRecently($linkToCrawl)) {
        return;
    }
    global $mainDomain;
    $ch = curl_init($linkToCrawl);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $startTime   = microtime(true);
    $response    = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $html        = substr($response, $header_size);
    if ($html === false) {
        return;
    }
    $urlToAdd = phpUri::parse($linkToCrawl);
    if (!isset($urlToAdd->authority) || $urlToAdd->authority !== $mainDomain) {
        return;
    }
    if (in_array($urlToAdd->scheme . '://' . $urlToAdd->authority . $urlToAdd->path, $crawledSites)) {
        return;
    }
    $headers      = get_headers_from_curl_response($response);
    $lastModified = null;
    if (array_key_exists("Last-Modified", $headers)) {
        $lastModified = $headers["Last-Modified"];
    }
    @$doc = str_get_html(convertToUTF8($html));
    if ($doc === false) {
        return;
    }
    array_push($crawledSites, $urlToAdd->scheme . '://' .  $urlToAdd->authority . $urlToAdd->path);
    $title = $doc->find('title', 0);
    if (isset($title)) {
        $title = $title->innertext;
    }
    $description = $doc->find('meta[description]', 0);
    $body = $doc->find('body', 0);
    if(isset($body)) {
        $body = preg_split("/\s+/", strip_tags($body->innertext));
    } else {
        $body = preg_split("/\s+/", strip_tags($doc->plaintext));
    }
    $wordCount = array();
    foreach($body as $word) {
      $wordCount[$word] = 0;
    }
    foreach($body as $word) {
      $wordCount[$word]++;
    }
    $duration    = microtime(true) - $startTime;
    saveIndexedInfo($linkToCrawl, $wordCount, $lastModified, $title, $description, $duration);
    $anchorList = $doc->find('a');
    foreach ($anchorList as $anchor) {
        $href = $anchor->getAttribute('href');
        if (0 !== strpos($href, 'http')) {
            $href = phpUri::parse($linkToCrawl)->join($href);
        }
        extractLinks($href, $crawledSites, $depth - 1);
    }
}

function urlIndexedRecently($url) {
    global $dbconnect;
    $stmt = $dbconnect->prepare('SELECT last_indexed FROM page WHERE url = ?');
    $stmt->bind_param('s', $url);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows !== 0) {
        $row           = $result->fetch_assoc();
        $lastIndexDate = $row['last_indexed'];
        if (strtotime($lastIndexDate) == strtotime(date('Y-m-d'))) {
            return true;
        }
    }
    return false;
}

function saveIndexedInfo($url, $wordsOnPage, $lastModified, $title, $description, $timeToIndex) {
    global $dbconnect;
    global $dbconnect2;
    $sql    = "INSERT INTO page(url, last_indexed";
    $sqlEnd = "VALUES(?, ?";
    if (isset($title)) {
        $sql .= ", title";
        $sqlEnd .= ", ?";
    }
    if (isset($description)) {
        $sql .= ", description";
        $sqlEnd .= ", ?";
    }
    if (isset($lastModified)) {
        $lastModified = strtotime($lastModified);
        $lastModified = date("Y-m-d", $lastModified);
        $sql .= ", last_modified";
        $sqlEnd .= ", ?";
    }
    $sql .= ") ";
    $sql .= $sqlEnd . ")";
    $sql .= " ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), last_modified = VALUES(last_modified)";
    $insertPageStmt = $dbconnect->prepare($sql) or die($dbconnect->error);
    $lastIndexDate  = date("Y-m-d");
    if (isset($description) && isset($title) && isset($lastModified)) {
        $insertPageStmt->bind_param('sssss', $url, $lastIndexDate, $title, $description, $lastModified);
    } else if (isset($description) && isset($title) && !isset($lastModified)) {
        $insertPageStmt->bind_param('ssss', $url, $lastIndexDate, $title, $description);
    } else if (isset($description) && !isset($title) && isset($lastModified)) {
        $insertPageStmt->bind_param('ssss', $url, $lastIndexDate, $description, $lastModified);
    } else if (isset($description) && !isset($title) && !isset($lastModified)) {
        $insertPageStmt->bind_param('sss', $url, $lastIndexDate, $description);
    } else if (!isset($description) && isset($title) && isset($lastModified)) {
        $insertPageStmt->bind_param('ssss', $url, $lastIndexDate, $title, $lastModified);
    } else if (!isset($description) && isset($title) && !isset($lastModified)) {
        $insertPageStmt->bind_param('sss', $url, $lastIndexDate, $title);
    } else if (!isset($description) && !isset($title) && isset($lastModified)) {
        $insertPageStmt->bind_param('sss', $url, $lastIndexDate, $lastModified);
    } else {
        $insertPageStmt->bind_param('ss', $url, $lastIndexDate);
    }
    $insertPageStmt->execute() or die(mysqli_error($dbconnect));
    $insertPageStmt->close();
    
    $sql        = "INSERT IGNORE INTO word(word_name) VALUES ";
    $param_type = '';
    $n          = count($wordsOnPage);
    for ($i = 0; $i < $n; $i++) {
        $param_type .= 's';
        $sql .= "(?)";
        if ($i < $n - 1) {
            $sql .= ", ";
        }
    }
    $params = array();
    foreach($wordsOnPage as $word => $value) {
        array_push($params, html_entity_decode($word));
    } 
    $dbconnect2->query($sql, $params);

    $pageWordSQL = "INSERT IGNORE INTO page_word(page_id, word_id, frequency) VALUES ";
    $param_type = '';
    $i = 0;
    $params = array();
    foreach($wordsOnPage as $word => $frequency) {
      $pageWordSQL .= "((SELECT id FROM page WHERE url = ?), (SELECT id FROM word WHERE word_name = ?), ?)";
      $param_type .= 'sss';
      if ($i < $n - 1) {
          $pageWordSQL .= ", ";
      }
      array_push($params, $url, $word, $frequency);
      $i++;
    }
    $dbconnect2->query($pageWordSQL, $params);
}


?>
