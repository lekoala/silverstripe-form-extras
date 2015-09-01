/**
 * AccountingField
 */
(function ($) {
	$(function () {
		$(document).on('blur', '.field.accounting input', function () {
			var $this = $(this);
			var precision = 2;
			if ($this.data('precision') !== undefined) {
				precision = $this.data('precision');
			}
			$this.val(accounting.formatNumber($this.val(), precision));
		});
	});
})(jQuery);

if (window.ParsleyValidator) {
	window.ParsleyValidator.addValidator('accountingmin', 
    function (value, requirement) {
        return accounting.unformat(value) >= requirement
    }, 32);
	window.ParsleyValidator.addValidator('accountingmax', 
    function (value, requirement) {
        return accounting.unformat(value) <= requirement
    }, 32);
}