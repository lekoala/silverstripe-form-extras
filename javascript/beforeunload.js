/**
 * Before unload message
 */
(function($) {
    $(function() {
        $(window).on('beforeunload', function() {
            return '$message';
        });
        $('form').on('submit', function() {
            $(window).off('beforeunload');
        });
    });
})(jQuery);