/**
 * ComboBox functionnality
 */
(function($) {
	$(function() {

		$('.field.combo select').live('change', function() {

			var $this = $(this);

			if ($this.val() === '_') {
				$this.hide();

				var input = $('<input type="text" name="' + $this.attr('name') + '" />');

				input.blur(function() {
					var val = $(this).val();
					if (val.length === 0) {
						input.remove();
						$this.show();
						$this.val('');
						return;
					}
					$this.find('option:last').before('<option value="' + val + '">' + val + '</option>');
					input.remove();
					$this.show();
					$this.val(val);
				});

				input.insertAfter($this);
			}
		});
	});
})(jQuery);