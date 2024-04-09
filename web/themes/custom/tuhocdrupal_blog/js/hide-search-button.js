(function ($, Drupal) {
  Drupal.behaviors.persistentHideSearchButton = {
    attach: function (context, settings) {
      var searchButtonHidden = false; // Cờ để kiểm tra xem nút tìm kiếm đã được ẩn hay không
      var initialSearchButtonClicked = false; // Cờ để kiểm tra xem đã click vào nút tìm kiếm khi trang được tải hay không

      // Hàm để ẩn nút tìm kiếm
      function hideSearchButton() {
        var $searchButton = $('.view-search-content [id^="edit-submit-search-content"]');
        if (!$searchButton.hasClass('d-none')) {
          $searchButton.addClass('d-none');
          searchButtonHidden = true; // Đặt cờ là true khi nút tìm kiếm đã được ẩn
        }
      }

      // Ẩn nút tìm kiếm lần đầu tiên khi behavior được áp dụng
      hideSearchButton();

      // Lắng nghe sự kiện AJAX hoàn tất và ẩn nút tìm kiếm mỗi lần như vậy
      $(document).ajaxComplete(function () {
        if (!searchButtonHidden) {
          hideSearchButton(); // Ẩn nút tìm kiếm chỉ khi nó chưa được ẩn
        }
      });

      var $textInput = $('.view-search-content #edit-search-api-fulltext', context);

      // Tự động bấm nút tìm kiếm khi trang được tải
      if (!initialSearchButtonClicked) {
        $(document).ready(function () {
          $('.view-search-content [id^="edit-submit-search-content"]').click();
          initialSearchButtonClicked = true; // Đặt cờ là true sau khi click vào nút tìm kiếm khi trang được tải
        });
      }

      // Tự động tìm kiếm khi người dùng nhập ít nhất 3 ký tự
      $textInput.on('keyup', function () {
        var searchValue = $(this).val();
        if (searchValue.length >= 3) {
          $('.view-search-content [id^="edit-submit-search-content"]').click();
        }
      });
    }
  };
})(jQuery, Drupal);
