/**
 * ComboField
 */
(function ($) {
	$(function () {

		$(document).on('change', '.field.combo select', function () {
			var $this = $(this);
			var chosenField = null;
			if ($this.hasClass('has-chzn')) {
				chosenField = $this.parents('.field').find('.chzn-container');
			}

			if ($this.val() === '_') {
				if (chosenField) {
					chosenField.hide();
				}
				else {
					$this.hide();
				}


				var input = $('<input type="text" class="text" name="' + $this.attr('name') + '" />');

				input.blur(function () {
					var val = $(this).val();

					// If no value is entered, back to select
					if (val.length === 0) {
						input.remove();
						$this.val('');
						if (chosenField) {
							chosenField.show();
							$this.trigger('liszt:updated').trigger("chosen:updated");
						}
						else {
							$this.show();
						}
						return;
					}
					$this.find('option:last').before('<option value="' + val + '">' + val + '</option>');
					input.remove();
					$this.val(val);
					if (chosenField) {
						chosenField.show();
						$this.trigger('liszt:updated').trigger("chosen:updated");
					}
					else {
						$this.show();
					}
				});

				input.insertAfter($this);
			}
		});
	});
})(jQuery);