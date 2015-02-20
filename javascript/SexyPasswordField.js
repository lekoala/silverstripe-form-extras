/**
 * SexyPasswordField
 */
(function () {
	window.ParsleyConfig = window.ParsleyConfig || {};
	window.ParsleyConfig.validators = window.ParsleyConfig.validators || {};

	window.ParsleyConfig.validators.sexypassword = {
		fn: function (value, requirement) {
			return $(requirement).data('sp-valid');
		},
		priority: 256
	};
})();
(function ($) {
	$(function () {

		var regexes = {
			lowercase: /[a-z]/,
			uppercase: /[A-Z]/,
			digits: /[0-9]/,
			punctuation: /[^A-Za-z0-9]/
		};

		function validatePassword(input, rules, restrictions) {
			var val = input.val();
			var restrictionsItems = restrictions.find('li');
			var valid = true;

			if (rules.minLength) {
				if (val.length >= rules.minLength) {
					restrictions.find('li.sp-restriction-minLength').removeClass('invalid').addClass('valid');
				}
				else {
					restrictions.find('li.sp-restriction-minLength').removeClass('valid').addClass('invalid');
					valid = false;
				}
			}
			if (rules.minScore) {
				for (prop in rules.testNames) {
					var testName = rules.testNames[prop];
					var regex = regexes[testName];

					if (regex.test(val)) {
						restrictions.find('li.sp-restriction-' + testName).removeClass('invalid').addClass('valid');
					}
					else {
						restrictions.find('li.sp-restriction-' + testName).removeClass('valid').addClass('invalid');
						valid = false;
					}
				}
			}

			input.data('sp-valid', valid);
		}

		$('.field.sexy-password').each(function () {
			var $this = $(this);
			var input = $this.find('input.password');
			var cb = $this.find('input.sp-checkbox');
			var restrictions = $this.find('.sp-restrictions');
			var rules = input.data('rules');

			// Parsley integration
			var parsley = input.data('parsley-required');
			if (parsley) {
				input.attr('data-parsley-sexypassword', '#' + input.attr('id'));
			}

			restrictions.hide();
			input.focus(function (e) {
				restrictions.slideDown();
			});
			if (restrictions.length) {
				input.keyup(function (e) {
					validatePassword(input, rules, restrictions);
				});
				validatePassword(input, rules, restrictions);
			}
			input.blur(function (e) {
				restrictions.slideUp();
			});
			if (cb.length) {
				cb.change(function (e) {
					if ($(this).is(":checked")) {
						input.prop("type", "text");
					}
					else {
						input.prop("type", "password");
					}
				});
			}
		});
	});
})(jQuery);