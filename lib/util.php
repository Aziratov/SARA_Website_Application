<?php
function var_dump_pre($mixed = null) {
  echo '<pre>';
  var_dump($mixed);
  echo '</pre>';
  return null;
}

function convertToUTF8($text){

  $encoding = mb_detect_encoding($text, mb_detect_order(), false);

  if($encoding == "UTF-8")
  {
      $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');    
  }


  $out = iconv(mb_detect_encoding($text, mb_detect_order(), false), "UTF-8//IGNORE", $text);


  return $out;
}

function get_headers_from_curl_response($response) {
  $headers = array();
  
  $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
  
  foreach (explode("\r\n", $header_text) as $i => $line)
      if ($i === 0)
          $headers['http_code'] = $line;
      else {
          list($key, $value) = explode(': ', $line);
          
          $headers[$key] = $value;
      }
  
  return $headers;
}
?>
