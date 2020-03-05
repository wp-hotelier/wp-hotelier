jQuery(function ($) {
	'use strict';
	/* global jQuery */
	/* eslint-disable no-multi-assign */

	var HTL_New_Reservation = {
		table: $('table.htl-ui-table--add-new-room-to-reservation'),
		form: $('form.add-new-reservation-form'),

		init: function () {
			this.add_room();
			this.remove_room();
			this.datepicker();
			this.notices();
		},

		clone_room_row: function (row) {
			var key = 1;
			var highest = 1;

			HTL_New_Reservation.table.find('tr.htl-ui-table__row--body').each(function () {
				var current = $(this).data('key');

				if (parseInt(current, 10) > highest) {
					highest = current;
				}
			});

			key = highest += 1;

			var clone = row.clone();

			clone.attr('data-key', key);
			clone.find('input').not('.htl-ui-input--fixed-value').val(1);
			clone.find('input, select').each(function () {
				var input = $(this);
				var name = input.attr('name');

				if (name) {
					name = name.replace(/\[(\d+)\]/, '[' + parseInt(key, 10) + ']');
					input.attr('name', name);
				}
			});

			return clone;
		},

		add_room: function () {
			HTL_New_Reservation.table.on('click', 'button.htl-ui-button--add-row', function (e) {
				e.preventDefault();

				var row = HTL_New_Reservation.table.find('tr.htl-ui-table__row--body').last();
				var clone = HTL_New_Reservation.clone_room_row(row);

				clone.insertAfter(row);

				$(window).trigger('htl_window_add_manual_reservation_after_add_room');
			});
		},

		remove_room: function () {
			HTL_New_Reservation.table.on('click', 'button.htl-ui-button--remove-row', function (e) {
				e.preventDefault();

				var button = $(this);
				var row = button.closest('tr');
				var rows = HTL_New_Reservation.table.find('tr.htl-ui-table__row--body');
				var count = rows.length;

				if (count > 1) {
					$('input', row).val('');
					row.fadeOut('fast').remove();
				}
			});
		},

		datepicker: function () {
			var from_input = HTL_New_Reservation.form.find('.htl-ui-input--start-date');
			var to_input = HTL_New_Reservation.form.find('.htl-ui-input--end-date');

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
		},

		notices: function () {
			if ($('.htl-ui-notice--new-reservation-message').length > 0) {
				$('.htl-ui-notice--new-reservation-message').insertBefore('form.add-new-reservation-form').show();
			}
		}
	};

	$(document).ready(function () {
		HTL_New_Reservation.init();
	});
});
