<?php
require 'lib/db.php';
require 'lib/util.php';
$searchTerm   = $argv[1];
$isSensitive = $argv[2] === 'true';
$isPartial = $argv[3] === 'true';
$out = fopen("error.log", "w");
if(!isset($searchTerm)) {
  return;
}
if($isSensitive && $isPartial) {
  $sql = "SELECT page.url, page.title, page.description, word.word_name
  FROM page JOIN page_word ON page.id = page_word.page_id
  JOIN word ON page_word.word_id = word.id
    WHERE word.word_name LIKE ? ORDER BY frequency DESC";
} else if($isSensitive && !$isPartial) {
  $sql = "SELECT page.url, page.title, page.description, word.word_name
  FROM page JOIN page_word ON page.id = page_word.page_id
  JOIN word ON page_word.word_id = word.id
    WHERE word.word_name = ? ORDER BY frequency DESC";
} else if(!$isSensitive && $isPartial) {
  $sql = "SELECT page.url, page.title, page.description, word.word_name
  FROM page JOIN page_word ON page.id = page_word.page_id
  JOIN word ON page_word.word_id = word.id
    WHERE LOWER(word.word_name) LIKE LOWER(?) ORDER BY frequency DESC";
} else {
  $sql = "SELECT page.url, page.title, page.description, word.word_name
  FROM page JOIN page_word ON page.id = page_word.page_id
  JOIN word ON page_word.word_id = word.id
    WHERE LOWER(word.word_name) = LOWER(?) ORDER BY frequency DESC";
}
if($isPartial) {
  $searchTerm = "%" . $searchTerm . "%";
}
$stmt = $dbconnect->prepare($sql) or die($dbconnect->error);
$stmt->bind_param('s', $searchTerm) or die($dbconnect->error);
$stmt->execute();
if($stmt->affected_rows === 0) die();
$results = $stmt->get_result();
$response = new stdClass();
$response->crawled_links = array();
$i = 0;
$numRows = $stmt->affected_rows;
foreach($results as $result) {
  $row = new stdClass();
  $row->url = $result["url"];
  $row->word_name =  mb_convert_encoding($result["word_name"], 'UTF-8', 'UTF-8');
  if(!empty($result["title"])) {
    $row->title = mb_convert_encoding($result["title"], 'UTF-8', 'UTF-8');
  } else {
    $row->title = null;
  }
  if(!empty($result["description"])) {
    $row->description = mb_convert_encoding($result["description"], 'UTF-8', 'UTF-8');
  } else {
    $row->description = null;
  }
  array_push($response->crawled_links, $row);
}
$stmt->close();
$response =  json_encode($response);
echo $response;
?>
