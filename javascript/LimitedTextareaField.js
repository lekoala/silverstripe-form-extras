/**
 * LimitedTextareaField
 */
(function ($) {
	$(function () {
		function limitChars(textid, limit, infodiv) {
			var text = $('#' + textid).val();
			var textlength = text.length;
			if (textlength > limit) {
				$('#' + textid).val(text.substr(0, limit));
				return false;
			}
			else {
				return true;
			}
		}

		if ($.entwine) {
			$.entwine('ss', function ($) {
				$('textarea.limitedtextarea').entwine({
					onkeyup: function () {
						limitChars($(this).attr('id'), $(this).data('limit'));
					}
				});
			});
		}
		else {
			// Init
			$('textarea.limitedtextarea').each(function () {
				$(this).keyup(function () {
					limitChars($(this).attr('id'), $(this).data('limit'), 'charlimitinfo');
				});
			});
		}
	});
})(jQuery);