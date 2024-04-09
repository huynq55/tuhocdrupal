(function ($, Drupal) {
  Drupal.behaviors.autoSearchBasedOnClass = {
    attach: function (context, settings) {
      // Lấy ra nút tìm kiếm và ô nhập văn bản dựa vào class của view
      var $searchButton = $('.view-search-content #edit-submit-search-content', context);
      var $textInput = $('.view-search-content #edit-search-api-fulltext', context);

      // Ẩn nút tìm kiếm
      $searchButton.addClass('d-none');

      // Tự động bấm nút tìm kiếm khi trang được tải
      $(document).ready(function () {
        $searchButton.click();
      });

      // Tự động tìm kiếm khi người dùng nhập ít nhất 3 ký tự
      $textInput.on('keyup', function () {
        var searchValue = $(this).val();
        if (searchValue.length >= 3) {
          $searchButton.click();
        }
      });
    }
  };
})(jQuery, Drupal);
