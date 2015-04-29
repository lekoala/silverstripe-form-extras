/**
 * select2Field
 */
(function ($) {
	$(function () {

		if ($.entwine) {
			$.entwine('ss', function ($) {
				console.log('init');
				$('.field.select2 select').entwine({
					onmatch: function () {
						this._super();
						console.log('match');
						opts = window['select2_' + $(this).attr('id')];
						console.log(opts);
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