/**
 * AppendGrid
 */
var appendGridSubPanelBuilder = function (cell, uniqueIndex) {
	if (!this.useSubPanel || !this.subColumns) {
		return;
	}
	var id = jQuery(cell).parents('table').attr('id');
	this.htmlID = id;
	jQuery.each(this.subColumns, function (index, value) {
		var tag = jQuery('<input type="text" />');
		var holder = jQuery('<div class="field appendgrid-sub-field"></div>');
		holder.appendTo(cell);

		jQuery('<span></span>').text(value.display + ': ').appendTo(holder);

		if (value.type === 'textarea') {
			tag = jQuery('<textarea></textarea').css('vertical-align', 'top');
		}

		tag.attr({
			id: id + '_' + value.name + '_' + uniqueIndex,
			name: id + '_' + value.name + '_' + uniqueIndex
		}).appendTo(holder);
	});
};
// Return the element value inside sub panel for `getAllValue` and `getRowValue` methods
var appendGridSubPanelGetter = function (uniqueIndex) {
	if (!this.useSubPanel || !this.subColumns) {
		return;
	}
	var obj = {};
	var id = this.htmlID;
	jQuery.each(this.subColumns, function (index, value) {
		obj[value.name] = jQuery('#' + id + '_' + value.name + '_' + uniqueIndex).val();
	});
	return obj;
};
var appendGridRowDataLoaded = function (caller, record, rowIndex, uniqueIndex) {
	if (!this.useSubPanel || !this.subColumns) {
		return;
	}
	var id = this.htmlID;
	jQuery.each(this.subColumns, function (index, value) {
		if (record[value.name]) {
			 var elem = document.getElementById(id + '_' + value.name + '_' + uniqueIndex);
			 elem.value = record[value.name];
		}
	});
};
var appendGridComputeTotalRow = function (column, ctrl) {
	if (column.ctrlProp && column.ctrlProp.TotalRowID) {
		totalValue = 0;
		var data = ctrl.parents('table').appendGrid('getAllValue');
		for (var i = 0; i < data.length; i++) {
			var row = data[i];
			if (row[column.name]) {
				totalValue = totalValue + accounting.unformat(row[column.name]);
			}
		}
		totalValue = accounting.formatNumber(totalValue, 2);
		$('#' + column.ctrlProp.TotalRowID).val(totalValue).trigger('change');
	}
};
var appendGridCurrencyBuilder = function (parent, idPrefix, name, uniqueIndex) {
	var ctrlId = idPrefix + '_' + name + '_' + uniqueIndex;
	var el = document.createElement('input');
	var ctrl = jQuery(el);

	// Format on blur
	ctrl.attr({id: ctrlId, name: ctrlId, type: 'text'}).blur(function () {
		ctrl.val(accounting.formatNumber(ctrl.val(), 2));
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
	return ctrl.val(accounting.formatNumber(value, 2));
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