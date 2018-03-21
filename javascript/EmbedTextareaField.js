/**
 * EmbedTextAreaField
 */
(function ($) {
    $(function () {
        function attach(item) {}

        function validateURL(val) {
            var urlregex = new RegExp(
                "^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)");
            return urlregex.test(val);
        }

        function onPaste(event) {
            var target = $(event.target);

            setTimeout(function () {
                var val = target.val();

                if (validateURL(val)) {
                    target.val('...');
                    target.attr('readonly', 'readonly');
                    $.getJSON('/__upload/oembed/?embed_url=' + encodeURI(val), function (res) {
                        if (res.html) {
                            target.val(res.html);
                        }
                        target.removeAttr('readonly');
                    }).error(function () {
                        target.val(val);
                        target.removeAttr('readonly');
                    });
                }
            }, 100);
        }
        if ($.entwine) {
            $.entwine('ss', function ($) {
                $('.field.embedtextarea textarea').entwine({
                    onmatch: function () {
                        this._super();
                    },
                    onpaste: onPaste,
                    onblur: onPaste
                });
            });
        } else {
            // Init
            $('.field.embedtextarea textarea').each(function () {
                $(this).on('paste blur', onPaste);
            });
        }
    });
})(jQuery);
