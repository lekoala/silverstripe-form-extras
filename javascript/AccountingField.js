/**
 * AccountingField
 */
(function ($) {
	$(function () {
		$(document).on('focus', '.field.accounting input', function () {
			var $this = $(this);
			var options = ['0','0.00','0,00'];
			if(options.indexOf($this.val()) > -1) {
				$this.val('');
			}
		});
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

if (window.Parsley) {
    window.Parsley.addValidator('accountingmin',
    function (value, requirement) {
        return accounting.unformat(value) >= requirement
    }, 32);
    window.Parsley.addValidator('accountingmax',
    function (value, requirement) {
        return accounting.unformat(value) <= requirement
    }, 32);
}