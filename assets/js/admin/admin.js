jQuery(function ($) {
	'use strict';
	/* global jQuery */

	var HTL_Admin = {
		init: function () {
			this.toggle_advanced();
			this.see_pro_features();
		},

		toggle_advanced: function () {
			$(document).on('click', '.htl-ui-text-icon--show-advanced', function () {
				var _this = $(this);
				var show_label = _this.attr('data-show-text');
				var hide_label = _this.attr('data-hide-text');
				var wrapper = _this.parent().find('.htl-ui-advanced-settings-wrapper');

				if (_this.hasClass('open')) {
					_this.text(show_label).removeClass('open');
				} else {
					_this.text(hide_label).addClass('open');
				}

				wrapper.toggleClass('open');
			});
		},

		see_pro_features: function () {
			$('.see-pro-version-features').on('click', function (e) {
				e.preventDefault();

				$('html, body').animate({
					scrollTop: $('.hotelier-settings-pro-features').offset().top
				}, 1000);
			});
		}
	};

	var HTL_Modals = {
		init: function () {
			$('.htl-ui-button--open-modal').on('click', function () {
				var _this = $(this);
				var modal = $('#' + _this.attr('data-open-modal'));
				var obfuscator = document.createElement('div');

				obfuscator.id = 'htl-ui-modal-obfuscator';
				document.documentElement.appendChild(obfuscator);

				obfuscator.addEventListener('click', HTL_Modals.close_modal);
				obfuscator = $('#htl-ui-modal-obfuscator');

				obfuscator.show();
				modal.show();

				modal.find('.htl-ui-modal__cancel').on('click', HTL_Modals.close_modal);
			});
		},

		close_modal: function () {
			$('.htl-ui-modal').hide();
			$('#htl-ui-modal-obfuscator').remove();
		}
	};
	$(document).ready(function () {
		HTL_Admin.init();
	});
});
