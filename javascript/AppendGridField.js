/**
 * AppendGrid
 */
var appendGridSubPanelBuilder = function (cell, uniqueIndex) {
	if (!this.useSubPanel || !this.subColumns) {
		return;
	}
	var id = jQuery(cell).parents('table').attr('id');
	var table = jQuery('<table class="field appendgrid-sub-field"></table>');
	table.appendTo(cell);
	this.htmlID = id;
	jQuery.each(this.subColumns, function (index, value) {
		var tag = jQuery('<input type="text" />');
		var holder = jQuery('<tr></tr>');
		holder.appendTo(table);

		jQuery('<td class="appendgrid-sub-field-label"></td>').text(value.display + ': ').appendTo(holder);

		if (value.type === 'textarea') {
			tag = jQuery('<textarea rows="5"></textarea').css('vertical-align', 'top');
		}

		tag.attr({
			id: id + '_' + value.name + '_' + uniqueIndex,
			name: id + '_' + value.name + '_' + uniqueIndex
		});

                if(this.value && this.value.ctrlAttr) {
                    tag.attr(this.value.ctrlAttr);
                }

		jQuery('<td class="appendgrid-sub-field-input"></td>').append(tag).appendTo(holder);
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
	if (column.totalRow && column.totalRow.TotalRowID) {
		totalValue = 0;
		var data = ctrl.parents('table').appendGrid('getAllValue');
		for (var i = 0; i < data.length; i++) {
			var row = data[i];
			if (row[column.name]) {
				totalValue = totalValue + accounting.unformat(row[column.name]);
			}
		}
		totalValue = accounting.formatNumber(totalValue, 2);
		jQuery('#' + column.totalRow.TotalRowID).val(totalValue).trigger('change');
	}
};
var appendGridCurrencyBuilder = function (parent, idPrefix, name, uniqueIndex) {
	var ctrlId = idPrefix + '_' + name + '_' + uniqueIndex;
	var el = document.createElement('input');
	var ctrl = jQuery(el);

	var attrs = {id: ctrlId, name: ctrlId, type: 'text', class: this.ctrlClass};
	if (this.ctrlAttr) {
		attrs = jQuery.extend(attrs, this.ctrlAttr);
	}
	if (this.onChange) {
		ctrl.change(this.onChange);
	}

	ctrl.attr(attrs);

	// Format on focus
	ctrl.focus(function () {
		var options = ['0', '0.00', '0,00'];
		if (options.indexOf(ctrl.val()) > -1) {
			ctrl.val('');
		}
	}).appendTo(parent);
	// Format on blur
	ctrl.blur(function () {
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
	ctrl.blur(function (e) {
		appendGridComputeTotalRow(that, ctrl);
	});
	res = ctrl.val(accounting.formatNumber(value, 2));
	appendGridComputeTotalRow(this, ctrl);
	return res;
};
(function ($) {
	$(function () {
		if ($.entwine) {
			$.entwine('ss', function ($) {
				$('.field.appendgrid table.appendgrid input').entwine({
					onkeydown: function (e) {
						var code = e.keyCode || e.which;
						if (code == 13) { //Enter keycode
							e.preventDefault();
							e.stopPropagation();
							return false;
						}
					}
				});
				$('.field.appendgrid table.appendgrid').entwine({
					onmatch: function () {
						this._super();
						var configId = $(this).attr('id') + 'Config';
						var opts = $.parseJSON($('#' + configId).text());

						// replace functions name by references
						var fcts = [
							'customBuilder',
							'customGetter',
							'customSetter',
							'onChange',
							'onClick'
						];

						if (opts.useSubPanel) {
							opts.subPanelBuilder = appendGridSubPanelBuilder;
							opts.subPanelGetter = appendGridSubPanelGetter;
							opts.rowDataLoaded = appendGridRowDataLoaded;
						}
						for (var i = 0; i < opts.columns.length; i++) {
							var col = opts.columns[i];
							for (var j = 0; j < fcts.length; j++) {
								if (col[fcts[j]] && window[col[fcts[j]]] !== undefined) {
									col[fcts[j]] = window[col[fcts[j]]];
								}
							}
						}

						$(this).appendGrid(opts);
						$(this).parents('form').removeClass('changed');
					}
				});
			});
		}
		else {
			// Init
			$('.field.appendgrid table').each(function () {
				var opts = window['appendgrid_' + $(this).attr('id')];
				$(this).appendGrid(opts);
			});
		}

	});
})(jQuery);
