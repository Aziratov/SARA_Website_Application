<?php
$command = "php word_search_helper.php  " . $_POST["text_to_search"] . " " . $_POST["case_sensitive_search"] . " " . $_POST["partial_search"] ." &";
echo shell_exec($command); 
?>
