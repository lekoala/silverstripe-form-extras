/**
 * AccountingField
 */
(function ($) {
	$(function () {
		applyAccountingSettings();
		
		$(document).on('blur', '.field.accounting input', function () {
			var $this = $(this);
			var precision = 2;
			if($this.data('precision') !== undefined) {
				precision = $this.data('precision');
			}
			$this.val(accounting.formatNumber($this.val(), precision));
		});
	});
})(jQuery);