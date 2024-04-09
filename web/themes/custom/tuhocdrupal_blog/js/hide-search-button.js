(function ($, Drupal) {
  Drupal.behaviors.persistentHideSearchButton = {
    attach: function (context, settings) {
      // Hàm để ẩn nút tìm kiếm
      function hideSearchButton() {
        var $searchButton = $('.view-search-content #edit-submit-search-content');
        if (!$searchButton.hasClass('d-none')) {
          $searchButton.addClass('d-none');
        }
      }

      // Ẩn nút tìm kiếm lần đầu tiên khi behavior được áp dụng
      hideSearchButton();

      // Lắng nghe sự kiện AJAX hoàn tất và ẩn nút tìm kiếm mỗi lần như vậy
      $(document).ajaxComplete(function () {
        hideSearchButton();
      });

      var $textInput = $('.view-search-content #edit-search-api-fulltext', context);

      // Tự động bấm nút tìm kiếm khi trang được tải
      $(document).ready(function () {
        $('.view-search-content #edit-submit-search-content').click();
      });

      // Tự động tìm kiếm khi người dùng nhập ít nhất 3 ký tự
      $textInput.on('keyup', function () {
        var searchValue = $(this).val();
        if (searchValue.length >= 3) {
          $('.view-search-content #edit-submit-search-content').click();
        }
      });
    }
  };
})(jQuery, Drupal);
