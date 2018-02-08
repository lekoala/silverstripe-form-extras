(function ($) {
    var selector = "div.flatpickr input.flatpickr";

    $(selector).entwine({
        onadd: function () {
            if (this.hasClass("flatpickr-init")) {
                return;
            }
            opts = this.data("flatpickr");
            if (!opts) {
                opts = {};
            }

            rangeInput = this.data("rangeinput");
            if (rangeInput) {
                opts.plugins = [new rangePlugin({
                    input: rangeInput
                })];
            }

            disabledFunc = this.data("disabledfunc");
            if (disabledFunc) {
                opts.disable = [
                    window[disabledFunc]
                ];
            }

            flatpickr("#" + this.attr("id"), opts);
            this.addClass("flatpickr-init");
        }
    });

    // We need to rely on this pattern otherwise on first load "onadd" is never called
    $(function () {
        var list = $(selector);
        if (list.length) {
            list.each(function () {
                $(this).onadd();
            });
        } else {
            console.log("Selector " + selector + " did not match anything");
        }
    });
})(jQuery);
