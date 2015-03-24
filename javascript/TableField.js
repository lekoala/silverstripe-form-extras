/**
 * TableField
 */
(function ($) {
	$(function () {

		function refreshTableRows(table, data) {
			var tbody = table.find('tbody');
			// Clear rows
			tbody.empty();

			// Append all rows
			$.each(data, function () {
				var row = '<tr class="ss-gridfield-item">';

				for (var key in this) {
					var val = this[key];
					row += '<td>' + val + '</td>';
				}

				row += '<td><button class="tablefield-btn-remove">-</button></tr>';
				tbody.append(row);
			});

		}

		if ($.entwine) {
			$.entwine('ss', function ($) {

				$('.field.table').entwine({
					onmatch: function () {
						this._super();
						var table = $(this);
						var input = table.find('input[type=hidden]');
						var val = [];
						if (input.val()) {
							val = JSON.parse(input.val());
						}

						refreshTableRows(table, val);
					}
				});
			});
		}
		else {
			// Init
			$('.field.table').each(function () {
				var table = $(this);
				var input = table.find('input[type=hidden]');
				var val = [];
				if (input.val()) {
					val = JSON.parse(input.val());
				}

				refreshTableRows(table, val);
			});
		}

		// Remove a row
		$('.tablefield-btn-remove').live('click', function () {
			var row = $(this).parents('tr');
			var tbody = $(this).parents('tbody');
			var index = tbody.children().index(row);
			var table = $(this).parents('.field.table');
			var input = table.find('input[type=hidden]');

			var val = [];
			if (input.val()) {
				val = JSON.parse(input.val());
			}

			// Remove value according to index position
			var removed = val.splice(index, 1);

			// Update value
			input.val(JSON.stringify(val));

			// Refresh table rows according to value
			refreshTableRows(table, val);
		});

		// Add a row
		$('.tablefield-btn-add').live('click', function () {
			var row = $(this).parents('tr');
			var table = $(this).parents('.field.table');
			var input = table.find('input[type=hidden]');
			var rowInputs = row.find('input,select');

			var val = [];
			if (input.val()) {
				val = JSON.parse(input.val());
			}

			var rowData = rowInputs.serializeArray();
			var result = {};
			$.each(rowData, function () {
				result[this.name] = this.value;
			});
			val.push(result);

			// Clear values
			rowInputs.val('');

			// Update value
			input.val(JSON.stringify(val));

			// Refresh table rows according to value
			refreshTableRows(table, val);
		});
	});
})(jQuery);