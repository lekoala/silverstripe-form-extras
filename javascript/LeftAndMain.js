/**
 */
(function($) {

    $.entwine('ss', function($) {
        $('.dropdown.chzn-container').entwine({
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
