(function ($, Drupal) {
  Drupal.behaviors.simpleSearchBehavior = {
    attach: function (context, settings) {
      var $textInput = $('.view-search-content #edit-search-api-fulltext', context);

      // Tự động tìm kiếm khi người dùng nhập ít nhất 3 ký tự
      $textInput.on('keyup', function () {
        var searchValue = $(this).val();
        if (searchValue.length >= 3) {
          $('.view-search-content [id^="edit-submit-search-content"]').removeClass('d-none');
        } else {
          $('.view-search-content [id^="edit-submit-search-content"]').addClass('d-none');
        }
      });
    }
  };
})(jQuery, Drupal);
