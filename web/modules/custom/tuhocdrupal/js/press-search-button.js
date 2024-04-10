(function ($, Drupal) {
  Drupal.behaviors.pressSearchButton = {
    attach: function (context, settings) {
      $(document).ready(function () {
        $('.view-search-content #edit-submit-search-content').click();
      })
    }
  }
})(jQuery, Drupal);
