/**
 * select2Field
 */
(function ($) {
    $(function () {
        // http://stackoverflow.com/questions/31431197/select2-how-to-prevent-tags-sorting
        function freeOrder(el) {
            $(el).on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);
                $element.detach();
                $(el).append($element);
                $(el).trigger("change");
            });
        }

        if ($.entwine) {
            $.entwine('ss', function ($) {
                $('.field.select2 select').entwine({
                    onmatch: function () {
                        this._super();
                        $(this).parents('.field').find('.chzn-container').hide();
                        opts = window['select2_' + $(this).attr('id')];
                        $(this).select2(opts);
                        if (opts.free_order) {
                            freeOrder(this);
                        }
                    }
                });
            });
        } else {
            // Init
            $('.field.select2 select').each(function () {
                opts = window['select2_' + $(this).attr('id')];
                $(this).select2(opts);

                if (opts.free_order) {
                    freeOrder(this);
                }
            });
        }

    });
})(jQuery);