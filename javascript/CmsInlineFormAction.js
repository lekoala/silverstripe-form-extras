(function ($) {
    $(function () {
        $.entwine('ss', function ($) {
            $('.cmsinlineaction').entwine({
                onmatch: function () {
                    this._super();
                },
                onclick: function () {
                    var t = this;

                    if (t.data('dialog')) {
                        var dialog = $('<div class="cmsinlineaction-dialog"/>');
                        dialog.ssdialog({iframeUrl: this.data('url'), height: 550});
                        dialog.ssdialog('open');
                        return;
                    }

                    if (t.data('ajax')) {
                        t.attr('disabled', 'disabled');

                        $.post(
                                t.data('url'),
                                t.parents('form').serialize(), function (r, status, xhr) {
                            if (r && r.length < 500) {
                                $.noticeAdd({text: r, type: status, stayTime: 5000, inEffect: {left: '0', opacity: 'show'}})
                            }
                            if (xhr.getResponseHeader('X-Reload')) {
                                location.reload();
                                return;
                            }

                        }).always(function () {
                            t.removeAttr('disabled');
                        });
                    } else {
                        window.location.href = t.data('url');
                    }
                }
            });
        });
    });
})(jQuery);