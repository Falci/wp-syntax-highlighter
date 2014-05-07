(function($) {

  var applyRestoreOptions = function() {
    $('#restoreDefaults').val('1');
  };

  var enableRestoreOptions = function() {
    $('#reset').click(function() {
      applyRestoreOptions();
    });
  };

  $(document).ready(function() {
    enableRestoreOptions();
  });

}(jQuery));
