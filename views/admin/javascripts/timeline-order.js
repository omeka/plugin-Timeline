(function($) {
  $(document).ready(function() {
    var sortableList = $('#sortable');
    sortableList.sortable({
      update: function(event, ui) {
        $.post(
          'timeline/timelines/update-order',
          $('#sortable').sortable('serialize'), 
          function(data) {}
        );
      }
    });
    $('#sortable').disableSelection();
    resetButton = document.getElementById("reset-button");
    resetButton.addEventListener("click", function(e) {
        $.post(
          'timeline/timelines/reset-order',
          $('#sortable').sortable('serialize'), 
          function(data) {
              // Reload the page after resetting order
              location.reload();
          }
        );
    });
  });
})(jQuery); 
