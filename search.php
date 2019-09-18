<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="./style.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500&display=swap" rel="stylesheet" />

    <title>Phase 1 Search</title>
</head>

<body>
    <header>
        <div class="logo-container">
            <img src="./img/logo.svg" alt="logo" srcset="" />
            <h4 class="logo">Internet & Web Technologies</h4>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a class="nav-link" href="index.html">Home</a></li>
                <li>
                    <div class="dropdown">
                        <a class="nav-link" href="#">Course</a>
                        <div class="dropdown-content">
                            <a href="https://learn.zybooks.com/zybook/CUNYCSCI355TeitelmanFall2019" target="_blank">Zybooks</a>
                            <a href="https://app.tophat.com/e/972963/lecture/" target="_blank">TopHat</a>
                            <a href="https://tinyurl.com/CSCI355-Summer2019" target=" _blank">Google Drive</a>
                            <a href="https://www.w3schools.com/" target=" _blank">W3Schools</a>
                        </div>
                    </div>
                </li>
                <li><a class="nav-link" href="browser.html">Browser</a></li>
                <li><a class="nav-link" href="about.html">About</a></li>
                <li><a class="nav-link active" href="search.php">Search</a></li>
                <li>
                    <div class="dropdown">
                        <a class="nav-link" href="#">Phase 2</a>
                        <div class="dropdown-content">
                            <a href="indexer.php">Indexer</a>
                            <a href="custom_search.php">Custom Search</a>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="search-title">
            <h4>Search Application</h4>
        </div>

        <div>
            <script async src="https://cse.google.com/cse.js?cx=000871576657004128095:smp5gv3t2dc"></script>
            <div class="gcse-searchbox"></div>
        </div>
        <div class="gcse-searchresults"></div>
        <form action="search.php" method="POST" enctype="multipart/form-data">
            <input id="upload-button" type="file" name="upload" />
            <input id="submit-button" type="submit" name="upload_button" value="Upload your file" />
        </form>
        <?php
          function parseJSON($fileContents) {
          $results = json_decode($fileContents)->Result;
          $jsonResults;
          for($i = 0; $i < count($results); ++$i) {
              $jsonResults .= "<li class='search_result_list_item' id='result" . $i . "'>";
              $jsonResults .= $results[$i]->title . "<br>";
              $jsonResults .= "<a href= " . "https://" . $results[$i]->url . " target = '_blank' class = 'link-given'>" . $results[$i]->url . "</a><br>";
              $jsonResults .=   $results[$i]->description . "<input class='results_checkbox' type='checkbox' id='checkbox" . $i . "'>";
              $jsonResults .= "</li>";
          }
          return $jsonResults;
      }

      function parseXML($fileContents) {
          $results = simplexml_load_string($fileContents);
          $xmlResults;
          for($i = 0; $i < count($results->result); ++$i) {
              $xmlResults .= "<li class='search_result_list_item' id='result" . $i . "'>";
              $xmlResults .= $results->result[$i]->title . "<br>";
              $xmlResults .=  "<a href= " . "https://" . $results->result[$i]->url . " target = '_blank' class = 'link-given'>" . $results->result[$i]->url . "</a><br>";
              $xmlResults .=  $results->result[$i]->description . "<input class='results_checkbox' type='checkbox' id='checkbox" . $i . "'>";
              $xmlResults .= "</li>";
          }
          return $xmlResults;
      }

      function parseCSV($fileContents) {
          $csvResults;
          $explodedCSVString = explode("\n", $fileContents);
          for($i = 0; $i < count($explodedCSVString); ++$i) {
              $currentCSV = str_getcsv($explodedCSVString[$i]);
              $csvResults .= "<li class='search_result_list_item' id='result" . $i . "'>";
              $csvResults .=  $currentCSV[0] . "<br>";
              $csvResults .=  "<a href= " . "https://" . $currentCSV[1]  . " target = '_blank' class = 'link-given'>" . $currentCSV[1] . "</a><br>";
              $csvResults .=  $currentCSV[2] . "<input class='results_checkbox'  type='checkbox' id='checkbox" . $i . "'>";
              $csvResults .= "</li>";
          }
          return $csvResults;
      }
        if(isset($_POST["upload_button"])) {
          if(!file_exists($_FILES["upload"]["tmp_name"])) {
            return;
          }
          echo "<input type='checkbox' id='select-all-checkbox'>
              <label for='select-all-checkbox'>Select All</label>
              <select id='select_file_format'> 
                  <option value = 'JSON'>JSON</option>
                  <option value = 'XML'>XML</option>
                  <option value = 'CSV'>CSV</option>
              </select>
              <button id='download'>Download File</button>
              <ol id='result_list'>";
             $fileContents = file_get_contents($_FILES["upload"]["tmp_name"]);
             $listItems = "";
             if($_FILES["upload"]["type"] == "text/xml") {
                 $listItems .= parseXML($fileContents);
             } else if($_FILES["upload"]["type"] == "application/json") {
                 $listItems .= parseJSON($fileContents);
             } else if($_FILES["upload"]["type"] == "text/csv") {
                 $listItems .= parseCSV($fileContents);
             } else {
                 echo $_FILES["upload"]["type"] . " is not a supported file type";
             }
             echo $listItems . "</ol>";
             echo "<script id='asifs_script'>
             document.getElementById('download').disabled = true;
             var checkboxes = document.getElementsByClassName('results_checkbox');
                 for(var i = 0; i < checkboxes.length; ++i) {
                     checkboxes[i].onclick = function() {
                         var numSelectedBoxes = 0;
                         for(var i = 0; i < checkboxes.length; ++i) {
                             if(checkboxes[i].checked) {
                                 numSelectedBoxes++;
                             }
                         }
                         if(numSelectedBoxes == checkboxes.length) {
                             document.getElementById('select-all-checkbox').checked = true;
                         } else {
                             document.getElementById('select-all-checkbox').checked = false;
                         }
                         for(var i = 0; i < checkboxes.length; ++i) {
                             if(checkboxes[i].checked) {
                                 document.getElementById('download').disabled = false;
                                 return;
                             }
                         }
                         document.getElementById('download').disabled = true;
                     }
             }
             var selectAllCheckbox = document.getElementById('select-all-checkbox');
             selectAllCheckbox.onclick = function() {
                 if(selectAllCheckbox.checked) {
                     document.getElementById('download').disabled = false;
                 } else {
                     document.getElementById('download').disabled = true;
                 }
                 var checkboxes = document.getElementsByClassName('results_checkbox');
                 for(var i = 0; i < checkboxes.length; ++i) {
                     checkboxes[i].checked = selectAllCheckbox.checked;
                 }
             };
             var downloadButton = document.getElementById('download');
             downloadButton.onclick = function() {
                 var previousDownloadTag = document.getElementById('download_link');
                 if(previousDownloadTag != null) {
                     previousDownloadTag.parentNode.removeChild(previousDownloadTag);
                 }
                 var checkedResults = [];
                 var checkboxes = document.getElementsByClassName('results_checkbox');
                 for(var i = 0; i < checkboxes.length; i++) {
                     if(checkboxes[i].checked) {
                         checkedResults.push(checkboxes[i]);
                     }
                 }
                 var selectedFormat = document.getElementById('select_file_format').value;
                 var fileText;
                 var fileType;
                 var fileName = prompt('Save as...', 'Enter file name') + '.';
                 if(checkedResults.length == 0) { 
                     return; 
                 }
                 if(selectedFormat == 'XML') {
                     fileType = 'text/xml';
                     fileName += 'xml';
                     fileText =  generateXMLFile(checkedResults);
                 } else if(selectedFormat == 'CSV') {
                     fileType = 'text/csv';
                     fileName += 'csv';
                     fileText = generateCSVFile(checkedResults);
                 } else if(selectedFormat == 'JSON'){
                     fileType = 'application/json';
                     fileName += 'json';
                     fileText = generateJSONFile(checkedResults);
                 } else {
                     console.log(selectedFormat);
                 }
                 var data = new Blob([fileText], {type: fileType});
                 var url = window.URL.createObjectURL(data);
                 var downloadAnchorTag = document.createElement('a');
     
                 var scriptNode = document.getElementById('asifs_script');
                 scriptNode.parentNode.insertBefore(downloadAnchorTag, scriptNode);
                 downloadAnchorTag.setAttribute('id', 'download_link');
                 downloadAnchorTag.setAttribute('href', url);
                 downloadAnchorTag.setAttribute('download', fileName);
                 downloadAnchorTag.textContent = 'Download File';
             };
             
             function generateXMLFile(checkboxes) {
                 var xmlFileText = '<' + '?' + 'xml version=\"1.0\" encoding=\"UTF-8\"?>';
        xmlFileText += '<results>';
            for(var i = 0; i < checkboxes.length; ++i) { var parentNode=checkboxes[i].parentNode; xmlFileText +='<result>' ; xmlFileText +='<title>' + parentNode.childNodes[0].nodeValue + '</title>' ; xmlFileText +='<url>' + parentNode.childNodes[2].textContent + '</url>' ; xmlFileText +='<description>' + parentNode.childNodes[4].nodeValue + '</description>' ; xmlFileText +='</result>' ; } xmlFileText +='</results>' ; return xmlFileText; } function generateCSVFile(checkboxes) { var csvFileText='' ; for(var i=0; i < checkboxes.length; ++i) { var parentNode=checkboxes[i].parentNode; csvFileText +=parentNode.childNodes[0].nodeValue + ',' ; csvFileText +=parentNode.childNodes[2].textContent + ',' ; csvFileText +=parentNode.childNodes[4].nodeValue; if(i !=checkboxes.length - 1) { csvFileText +='\\n' ; } } return csvFileText; } function generateJSONFile(checkboxes) { var jsonFileText='{' ; jsonFileText +='\"Result\": [' ; for(var i=0; i < checkboxes.length; ++i) { var parentNode=checkboxes[i].parentNode; console.log(parentNode.childNodes); jsonFileText +='{' ; jsonFileText +='\"title\":' + '\"' + parentNode.childNodes[0].nodeValue + '\"' + ',' ; jsonFileText +='\"url\":' + '\"' + parentNode.childNodes[2].textContent + '\"' + ',' ; jsonFileText +='\"description\":' + '\"' + parentNode.childNodes[4].nodeValue + '\"' ; jsonFileText +='}' ; if(i !=checkboxes.length - 1) { jsonFileText +=',' ; } } jsonFileText +=']}' return jsonFileText; } </script>"; } ?>
    </main>
</body>

</html>
