/**
 * AppendGrid
 */
var appendGridComputeTotalRow = function (column, ctrl) {
	if (column.ctrlProp && column.ctrlProp.TotalRowID) {
		totalValue = 0;
		var data = ctrl.parents('table').appendGrid('getAllValue');
		for (var i = 0; i < data.length; i++) {
			var row = data[i];
			if(row[column.name]) {
				totalValue = totalValue + parseFloat(row[column.name]);
			}
		}
		totalValue = appendGridToFixed(totalValue);
		$('#' + column.ctrlProp.TotalRowID).val(totalValue);
	}
};
var appendGridCurrencyBuilder = function (parent, idPrefix, name, uniqueIndex) {
	var ctrlId = idPrefix + '_' + name + '_' + uniqueIndex;
	var el = document.createElement('input');
	var ctrl = jQuery(el);

	// Format on blur
	ctrl.attr({id: ctrlId, name: ctrlId, type: 'text'}).blur(function () {
		ctrl.val(appendGridToFixed(ctrl.val()));
	}).appendTo(parent);

	return el;
};
var appendGridCurrencyGetter = function (idPrefix, name, uniqueIndex) {
	var ctrlId = idPrefix + '_' + name + '_' + uniqueIndex;
	return jQuery('#' + ctrlId).val();
};
var appendGridCurrencySetter = function (idPrefix, name, uniqueIndex, value) {
	var ctrlId = idPrefix + '_' + name + '_' + uniqueIndex;
	var ctrl = jQuery('#' + ctrlId);
	var that = this;
	appendGridComputeTotalRow(this, ctrl);
	ctrl.blur(function (e) {
		appendGridComputeTotalRow(that, ctrl);
	});
	return ctrl.val(appendGridToFixed(value, 2));
};
var appendGridToFixed = function (value) {
	if(!value) {
		return '0.00';
	}
	var res = parseFloat(value.toString().replace(',', '.')).toFixed(2);
	if (res === 'NaN') {
		return '0.00';
	}
	return res;
};
(function ($) {
	$(function () {
		if ($.entwine) {
			$.entwine('ss', function ($) {
				$('.field.appendgrid table').entwine({
					onmatch: function () {
						this._super();
						opts = window['appendgrid_' + $(this).attr('id')];
						$(this).appendGrid(opts);
					}
				});
			});
		}
		else {
			// Init
			$('.field.appendgrid table').each(function () {
				opts = window['appendgrid_' + $(this).attr('id')];
				$(this).appendGrid(opts);
			});
		}

	});
})(jQuery);