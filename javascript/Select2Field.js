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
						if (opts.free_order) {
							$(this).on("select2:select", function (evt) {
								var element = evt.params.data.element;
								var $element = $(element);
								$element.detach();
								$(this).append($element);
								$(this).trigger("change");
							});
						}
					}
				});
			});
		}
		else {
			// Init
			$('.field.select2 select').each(function () {
				opts = window['select2_' + $(this).attr('id')];
				$(this).select2(opts);
				if (opts.free_order) {
					$(this).on("select2:select", function (evt) {
						var element = evt.params.data.element;
						var $element = $(element);
						$element.detach();
						$(this).append($element);
						$(this).trigger("change");
					});
				}
			});
		}

	});
})(jQuery);