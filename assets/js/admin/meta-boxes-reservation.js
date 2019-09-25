jQuery(function ($) {
	'use strict';
	/* global reservation_meta_params, jQuery */
	/* eslint-disable no-alert */

	var HTL_Reservation_Meta = {
		init: function () {
			this.charge_remain_deposit();
			this.reservation_dates_datepicker();
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
		},

		reservation_dates_datepicker: function () {
			var from_input = $('.edit-reservation-page__general-details').find('.htl-ui-input--start-date');
			var to_input = $('.edit-reservation-page__general-details').find('.htl-ui-input--end-date');

			from_input.datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: 0,
				changeMonth: true,
				onClose: function () {
					var date = $(this).datepicker('getDate');

					if (date) {
						date.setDate(date.getDate() + 1);
						to_input.datepicker('option', 'minDate', date);
					}
				},
				beforeShow: function () {
					$('#ui-datepicker-div').addClass('htl-ui-custom-datepicker');
				}
			});

			to_input.datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: 1,
				changeMonth: true,
				beforeShow: function () {
					$('#ui-datepicker-div').addClass('htl-ui-custom-datepicker');
				}
			});
		}
	};

	$(document).ready(function () {
		HTL_Reservation_Meta.init();
	});
});
