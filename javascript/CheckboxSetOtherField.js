/**
 * CheckboxSetOtherField
 */
(function ($) {
	$(function () {

		$(document).on('change', '.field.checkboxset .val_ input', function () {
			var $this = $(this);
			
			var val = $(this).parents('.field').data('other-value');
			if(val === undefined) {
				val = '';
			}
			
			if ($this.is(':checked')) {
				
				var input = $('<input type="text" class="text" name="' + $this.attr('name') + '" value="'+val+'" />');

				input.appendTo($this.parents('ul'));
			}
			else {
				$this.parents('.field').find('input[type="text"]').remove();
			}
		});
		$('.field.checkboxset .val_ input').trigger('change');
	});
})(jQuery);