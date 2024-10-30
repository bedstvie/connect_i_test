(function ($, Drupal) {
  Drupal.behaviors.backendAjaxBlockContent = {
    attach: function (context, settings) {
      $(window).on('load', function () {
        $.ajax({
          url: Drupal.url('ajax-content/backend-block'),
          type: 'GET',
          success: function (data) {
            $('#delayed-ajax-content-block', context).html(data.content);
          }
        });
      });
    }
  };
})(jQuery, Drupal);
