(function ($) {
	$.entwine('ss', function ($) {
		$('input.minicolorsfield').entwine({
			onmatch: function (e) {
				var $self = $(this);
				var picker = $(this).minicolors({
					theme: $self.data('theme')
				});
			},
			onkeyup: function () {
			}
		});
	});
})(jQuery);