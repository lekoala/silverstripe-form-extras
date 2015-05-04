/**
 * AppendGrid
 */
(function ($) {
	$(function () {

		if ($.entwine) {
			$.entwine('ss', function ($) {
				$('.field.appendgrid table').entwine({
					onmatch: function () {
						this._super();
						opts = window['appendgrid_' + $(this).attr('id')];
						$(this).appendgrid(opts);
					}
				});
			});
		}
		else {
			// Init
			$('.field.appendgrid table').each(function () {
				opts = window['appendgrid_' + $(this).attr('id')];
				$(this).appendgrid(opts);
			});
		}

	});
})(jQuery);