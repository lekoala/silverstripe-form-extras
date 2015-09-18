/**
 * ExpandableTextareaField
 */
(function ($) {
	$(function () {
		var opts = {
			init:true,
			by:1,
			within:0,
			interval:500,
			duration:0
		};
		if ($.entwine) {
			$.entwine('ss', function ($) {
				$('textarea.expandabletextarea').entwine({
					onmatch: function () {
						this._super();
						$(this).expandable(opts);
					}
				});
			});
		}
		else {
			// Init
			$('textarea.expandabletextarea').each(function () {
				$(this).expandable(opts);
			});
		}
	});
})(jQuery);