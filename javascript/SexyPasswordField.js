/**
 * SexyPasswordField
 */
(function ($) {
	$(function () {

		$('.field.sexy-password').each(function () {
			var $this = $(this);
			var input = $this.find('input.password');
			var cb = $this.find('input.sp-checkbox');
			var restrictions = $this.find('.sp-restrictions');

			restrictions.hide();
			input.focus(function (e) {
				restrictions.slideDown();
			});
			input.blur(function (e) {
				restrictions.slideUp();
			});
			if (cb.length) {
				cb.change(function (e) {
					if ($(this).is(":checked")) {
						input.prop("type", "text");
					}
					else {
						input.prop("type", "password");
					}
				});
			}
		});
	});
})(jQuery);