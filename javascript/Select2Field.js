/**
 * select2Field
 */
(function ($) {
	$(function () {

		if ($.entwine) {
			$.entwine('ss', function ($) {
				$('.field.select2 select').entwine({
					onmatch: function () {
						this._super();
						$(this).parents('.field').find('.chzn-container').hide();
						opts = window['select2_' + $(this).attr('id')];
						$(this).select2(opts);
					}
				});
			});
		}
		else {
			// Init
			$('.field.select2 select').each(function () {
				opts = window['select2_' + $(this).attr('id')];
				$(this).select2(opts);
			});
		}

	});
})(jQuery);