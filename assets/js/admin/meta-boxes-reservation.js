jQuery(function ($) {
	'use strict';
	/* global reservation_meta_params, jQuery */
	/* eslint-disable no-alert */

	var HTL_Reservation_Meta = {
		init: function () {
			this.charge_remain_deposit();
		},

		charge_remain_deposit: function () {
			var form = $('#post');

			$('.htl-ui-button--charge-remain-deposit').on('click', function (e) {
				e.preventDefault();

				if (window.confirm(reservation_meta_params.i18n_do_remain_deposit_charge)) {
					// Create hidden input and append it to the form
					var input = document.createElement('input');
					input.setAttribute('type', 'hidden');
					input.setAttribute('name', 'hotelier_charge_remain_deposit');
					input.setAttribute('value', 1);
					form.append(input);

					// We can now submit the form
					form.submit();
				}
			});
		}
	};

	$(document).ready(function () {
		HTL_Reservation_Meta.init();
	});
});
