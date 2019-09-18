// magic.js
$(document).ready(function() {
  $('#indexer-loading').hide();
  var button = $('#button-submit').on('click', function() {
    $(document).ajaxStart(function() {
      $('#indexer-loading').show();
    });
    var textToSearch = $('#search-bar').val();
    var formData = {
      text_to_search: textToSearch,
      case_sensitive_search: $('#checkbox-case-sensitive').prop('checked'),
      partial_search: $('#checkbox-partial').prop('checked'),
    };
    $('#results').html('Searching...');
    $.ajax({
      type: 'POST',
      url: 'word_search.php',
      data: formData,
      encode: true,
    }).done(function(data) {
      try {
        var results = JSON.parse(data);
        var html =
          "<input type='checkbox' id='select-all-checkbox'> <label for='select-all-checkbox'>Select All</label><select id='select_file_format'> <option value = 'JSON'>JSON</option><option value = 'XML'>XML</option><option value = 'CSV'>CSV</option></select><button id='download'>Download File</button>";
        html += "<div id='result-table' class='divTable paleBlueRows'>";
        html += "<div class='divTableHeading'>";
        html += "<div class='divTableRow'>";
        html += "<div class='divTableHead'>";
        html += 'Url';
        html += '</div>';
        html += "<div class='divTableHead'>";
        html += 'Word';
        html += '</div>';
        html += "<div class='divTableHead'>";
        html += 'Title';
        html += '</div>';
        html += "<div class='divTableHead'>";
        html += 'Description';
        html += '</div>';
        html += "<div class='divTableHead'>";
        html += 'Download';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += "<div class='divTableBody'>";
        for (var i = 0; i < results.crawled_links.length; ++i) {
          html += "<div class='divTableRow'>";
          html += "<div class='divTableCell' id='result-url" + i + "'>";
          html += results.crawled_links[i]['url'];
          html += '</div>';
          html += "<div class='divTableCell' id='result-word-name" + i + "'>";
          html += results.crawled_links[i]['word_name'];
          html += '</div>';
          html += "<div class='divTableCell' id='result-title" + i + "'>";
          if (results.crawled_links[i]['title'] != null) {
            html += results.crawled_links[i]['title'];
          }
          html += '</div>';
          html += "<div class='divTableCell' id='result-description" + i + "'>";
          if (results.crawled_links[i]['description'] != null) {
            html += results.crawled_links[i]['description'];
          }
          html += '</div>';
          html += "<div class='divTableCell'>";
          html +=
            "<input class='results_checkbox' type='checkbox' id='checkbox" +
            i +
            "'>";
          html += '</div>';

          html += '</div>';
        }
        html += '</div>';
        html += '</div>';
        $('#indexer-loading').hide();
        $('#results').html(html);
        setupDownloadButtons();
      } catch (e) {
        console.log(data);
      }
    });
  });
});

function setupDownloadButtons() {
  document.getElementById('download').disabled = true;
  var checkboxes = document.getElementsByClassName('results_checkbox');
  for (var i = 0; i < checkboxes.length; ++i) {
    checkboxes[i].onclick = function() {
      var numSelectedBoxes = 0;
      for (var i = 0; i < checkboxes.length; ++i) {
        if (checkboxes[i].checked) {
          numSelectedBoxes++;
        }
      }
      if (numSelectedBoxes == checkboxes.length) {
        document.getElementById('select-all-checkbox').checked = true;
      } else {
        document.getElementById('select-all-checkbox').checked = false;
      }
      for (var i = 0; i < checkboxes.length; ++i) {
        if (checkboxes[i].checked) {
          document.getElementById('download').disabled = false;
          return;
        }
      }
      document.getElementById('download').disabled = true;
    };
  }
  var selectAllCheckbox = document.getElementById('select-all-checkbox');
  selectAllCheckbox.onclick = function() {
    if (selectAllCheckbox.checked) {
      document.getElementById('download').disabled = false;
    } else {
      document.getElementById('download').disabled = true;
    }
    var checkboxes = document.getElementsByClassName('results_checkbox');
    for (var i = 0; i < checkboxes.length; ++i) {
      checkboxes[i].checked = selectAllCheckbox.checked;
    }
  };
  var downloadButton = document.getElementById('download');
  downloadButton.onclick = function() {
    var previousDownloadTag = document.getElementById(
      'download_link_custom_search',
    );
    if (previousDownloadTag != null) {
      previousDownloadTag.parentNode.removeChild(previousDownloadTag);
    }
    var resultsToDownload = [];
    var checkboxes = document.getElementsByClassName('results_checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
      if (checkboxes[i].checked) {
        var resultID = checkboxes[i].id.substring(checkboxes[i].id.length - 1);
        var title = document.getElementById('result-title' + resultID)
          .innerText;
        var description = document.getElementById(
          'result-description' + resultID,
        ).innerText;
        var word = document.getElementById('result-word-name' + resultID)
          .innerText;
        var url = document.getElementById('result-url' + resultID).innerText;
        var result = {
          title: title,
          description: description,
          word: word,
          url: url,
        };
        resultsToDownload.push(result);
      }
    }
    var selectedFormat = document.getElementById('select_file_format').value;
    var fileText;
    var fileType;
    var fileName = prompt('Save as...', 'Enter file name');
    if (resultsToDownload.length == 0) {
      return;
    }
    if (selectedFormat == 'XML') {
      fileType = 'text/xml';
      if (!fileName.endsWith('.xml')) {
        fileName += '.xml';
      }
      fileText = generateXMLFile(resultsToDownload);
    } else if (selectedFormat == 'CSV') {
      fileType = 'text/csv';
      if (!fileName.endsWith('.csv')) {
        fileName += '.csv';
      }
      fileText = generateCSVFile(resultsToDownload);
    } else if (selectedFormat == 'JSON') {
      fileType = 'application/json';
      if (!fileName.endsWith('.json')) {
        fileName += '.json';
      }
      fileText = generateJSONFile(resultsToDownload);
    } else {
      console.log(selectedFormat);
    }
    var data = new Blob([fileText], {
      type: fileType,
    });
    var url = window.URL.createObjectURL(data);
    var downloadAnchorTag = document.createElement('a');

    var nodeToInsertBefore = document.getElementById('result-table');
    nodeToInsertBefore.parentNode.insertBefore(
      downloadAnchorTag,
      nodeToInsertBefore,
    );
    downloadAnchorTag.setAttribute('id', 'download_link_custom_search');
    downloadAnchorTag.setAttribute('href', url);
    downloadAnchorTag.setAttribute('download', fileName);
    downloadAnchorTag.textContent = 'Download: ' + fileName;
  };
}
function generateXMLFile(resultsToDownload) {
  var xmlFileText = '<?xml version="1.0" encoding="UTF-8"?>';
  xmlFileText +=
    '<results xmlns="https://www.w3schools.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
  for (var i = 0; i < resultsToDownload.length; ++i) {
    xmlFileText += '<result>';
    if (resultsToDownload[i]['title']) {
      xmlFileText += '<title>' + resultsToDownload[i]['title'] + '</title>';
    } else {
      xmlFileText += '<title xsi:nil="true" />';
    }
    if (resultsToDownload[i]['url']) {
      xmlFileText += '<url>' + resultsToDownload[i]['url'] + '</url>';
    } else {
      xmlFileText += '<url xsi:nil="true" />';
    }
    if (resultsToDownload[i]['description']) {
      xmlFileText +=
        '<description>' +
        resultsToDownload[i]['description'] +
        '</description>';
    } else {
      xmlFileText += '<description xsi:nil="true" />';
    }
    if (resultsToDownload[i]['word']) {
      xmlFileText += '<word>' + resultsToDownload[i]['word'] + '</word>';
    } else {
      xmlFileText += '<word xsi:nil="true" />';
    }
    xmlFileText += '</result>';
  }
  xmlFileText += '</results>';
  return xmlFileText;
}

function generateCSVFile(resultsToDownload) {
  var csvFileText = '';
  for (var i = 0; i < resultsToDownload.length; ++i) {
    if (resultsToDownload[i]['title']) {
      csvFileText += resultsToDownload[i]['title'];
      csvFileText += ',';
    }
    if (resultsToDownload[i]['url']) {
      csvFileText += resultsToDownload[i]['url'];
    }
    csvFileText += ',';
    if (resultsToDownload[i]['description']) {
      csvFileText += resultsToDownload[i]['description'];
    }
    csvFileText += ',';
    if (resultsToDownload[i]['word']) {
      csvFileText += resultsToDownload[i]['word'];
    }
    if (i != resultsToDownload.length - 1) {
      csvFileText += '\n';
    }
  }
  return csvFileText;
}

function generateJSONFile(resultsToDownload) {
  var jsonFileText = '{';
  jsonFileText += '"Result": [';
  for (var i = 0; i < resultsToDownload.length; ++i) {
    jsonFileText += '{';
    jsonFileText += '"title":';
    if (resultsToDownload[i]['title']) {
      jsonFileText += '"' + resultsToDownload[i]['title'] + '"';
    } else {
      jsonFileText += 'null';
    }
    jsonFileText += ', "url":';
    if (resultsToDownload[i]['url']) {
      jsonFileText += '"' + resultsToDownload[i]['url'] + '"';
    } else {
      jsonFileText += 'null';
    }
    jsonFileText += ', "description":';
    if (resultsToDownload[i]['description']) {
      jsonFileText += '"' + resultsToDownload[i]['description'] + '"';
    } else {
      jsonFileText += 'null';
    }
    jsonFileText += ', "word":';
    if (resultsToDownload[i]['word']) {
      jsonFileText += '"' + resultsToDownload[i]['word'] + '"';
    } else {
      jsonFileText += 'null';
    }
    jsonFileText += '}';
    if (i != resultsToDownload.length - 1) {
      jsonFileText += ',';
    }
  }
  jsonFileText += ']}';
  return jsonFileText;
}
