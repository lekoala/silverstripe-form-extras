(function ($) {
	$(document).ready(function () {
		$('.sexyoptionset li').click(function (e) {
			e.stopPropagation();
			e.preventDefault();
			var checked = $(this).hasClass('checked');
			$(this).parents('.sexyoptionset').find('li').removeClass('checked').find('input').removeAttr('checked');
			if (!checked) {
				$(this).find('input').attr('checked', 'checked');
				$(this).addClass('checked');
			}
		});
		$('.sexyoptionset li input:checked').parents('li').addClass('checked');

	});
}(jQuery));