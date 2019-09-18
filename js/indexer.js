// magic.js
$(document).ready(function() {
  $('#indexer-loading').hide();
  $('form').submit(function(event) {
    $(document).ajaxStart(function() {
      $('#indexer-loading').show();
    });
    var urlToIndex = $('#input_index_url').val();
    var formData = {
      url_to_index: urlToIndex,
    };
    $('#results').html(urlToIndex + ' is now being crawled and indexed.');
    $.ajax({
      type: 'POST',
      url: 'crawler.php',
      data: formData,
      encode: true,
    }).done(function(data) {
      $('#indexer-loading').hide();
      var html = "<div class='box'>";
      if (!data || 0 === data.length) {
        html +=
          "<div class='info-box item'><i class='fa fa-info-circle'></i>No websites were crawled.</div></div>";
        $('#results').html(html);
        return;
      }
      var websites = data.split(' ');
      html += "<div class='divTable paleBlueRows '>";
      html += "<div class='divTableHeading'>";
      html += "<div class='divTableRow'>";
      html += "<div class='divTableHead'>";
      html += 'Crawled URLS';
      html += '</div>';
      html += '</div>';
      html += '</div>';
      html += "<div class='divTableBody'>";
      for (var i = 0; i < websites.length; ++i) {
        html += "<div class='divTableRow'>";
        html += "<div class='divTableCell'>";
        html += websites[i];
        html += '</div>';
        html += '</div>';
      }
      html += '</div>';
      html += '</div>';
      html += '</div>';
      $('#results').html(html);
    });
    event.preventDefault();
  });
});
