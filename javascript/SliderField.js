/**
 * SliderField
 */
(function($) {
	$(function() {

		$('.field.slider .ui-slider').each(function() {
			var $this = $(this);
			var fieldHolder = $this.parents('.field');
			var input = fieldHolder.find('input');
			var span = fieldHolder.find('.readonly');
			
			var baseOptions = $this.data('options');
			var opts = {
				value : input.val(),
				slide:function(event,ui) {
					input.val(ui.value);
					if(span.length) {
						span.text(ui.value);
					}
				}
			};
			var options = $.extend(baseOptions,opts);
			
			$this.slider(options);			
		});
	});
})(jQuery);