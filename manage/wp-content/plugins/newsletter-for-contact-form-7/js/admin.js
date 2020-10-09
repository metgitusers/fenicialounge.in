(function ($) {
    "use strict";
    $(function () {
        if (azm_cf7) {
            $('body.toplevel_page_wpcf7 .wp-list-table tbody tr').each(function () {
                var id = $(this).find('.check-column input[type="checkbox"]').val();
                if (id) {
                    var $actions = $(this).find('.column-title .row-actions');
                    $('<span class="submissions-list"> | <a href="' + azm_cf7.submissions_list.replace('{id}', id) + '">' + azm_cf7.i18n.submissions_list + '</a></span>').appendTo($actions);
                    $('<span class="send-email"> | <a href="' + azm_cf7.send_email.replace('{id}', id) + '">' + azm_cf7.i18n.send_email + '</a></span>').appendTo($actions);
                }
            });
        }
    });
})(window.jQuery);


