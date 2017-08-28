/**
 */
(function($) {

    $.entwine('ss', function($) {
        $('.chzn-container').entwine({
            onmatch: function() {
                var t = this;
                setTimeout(function() {
                    if (t.is(':hidden')) {
                        t.show();
                    }
                }, 200);
            }
        });
    });
}(jQuery));
