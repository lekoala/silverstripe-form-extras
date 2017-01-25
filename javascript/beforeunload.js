/**
 * Before unload message
 */
(function($) {
    $(function() {
        $(window).on('beforeunload', function() {
            return '$message';
        });
    });
})(jQuery);