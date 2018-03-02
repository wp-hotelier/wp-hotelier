jQuery(function ($) {
	'use strict';
	/* global jQuery, wp */
	/* eslint-disable no-multi-assign */

	var HTL_Settings = {
		init: function () {
			this.show_uploader();
			this.show_unforce_ssl();
			this.show_pets_message();
			this.show_book_now_quantity();
			this.add_seasonal_rule();
			this.remove_rule();
			this.sort_rules();
			this.datepicker();
		},

		show_uploader: function () {
			var uploader_button = $('.htl-uploader');
			var field = uploader_button.prev();
			var file_frame;

			uploader_button.on('click', function (e) {
				e.preventDefault();

				// If the media frame already exists, reopen it.
				if (file_frame) {
					file_frame.open();
					return;
				}

				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					states: [
						new wp.media.controller.Library({
							filterable: 'all',
							multiple: false
						})
					]
				});

				// When an image is selected, run a callback.
				file_frame.on('select', function () {
					var selection = file_frame.state().get('selection');
					var file_path = '';

					selection.map(function (attachment) {
						attachment = attachment.toJSON();

						if (attachment.url) {
							file_path = attachment.url;
						}
					});

					field.val(file_path);
				});

				// Finally, open the modal.
				file_frame.open();
			});
		},

		show_unforce_ssl: function () {
			var force_ssl_input = $('input[name="hotelier_settings[enforce_ssl_booking]"]');
			var unforce_ssl_field = $('#hotelier_settings\\[unforce_ssl_booking\\]').closest('tr');

			unforce_ssl_field.hide();

			if (force_ssl_input.is(':checked')) {
				unforce_ssl_field.show();
			}

			force_ssl_input.on('click', function () {
				var _this = $(this);

				if (_this.is(':checked')) {
					unforce_ssl_field.show();
				} else {
					unforce_ssl_field.hide();
				}
			});
		},

		show_pets_message: function () {
			var pets_input = $('input[name="hotelier_settings[hotel_pets]"]');
			var pets_message = $('#hotelier_settings\\[hotel_pets_message\\]').closest('tr');

			pets_message.hide();

			if (pets_input.is(':checked')) {
				pets_message.show();
			}

			pets_input.on('click', function () {
				var _this = $(this);

				if (_this.is(':checked')) {
					pets_message.show();
				} else {
					pets_message.hide();
				}
			});
		},

		show_book_now_quantity: function () {
			var book_now_behaviour = $('input[name="hotelier_settings[book_now_redirect_to_booking_page]"]');
			var book_now_quantity = $('#hotelier_settings\\[book_now_allow_quantity_selection\\]').closest('tr');

			book_now_quantity.hide();

			if (book_now_behaviour.is(':checked')) {
				book_now_quantity.show();
			}

			book_now_behaviour.on('click', function () {
				var _this = $(this);

				if (_this.is(':checked')) {
					book_now_quantity.show();
				} else {
					book_now_quantity.hide();
				}
			});
		},

		add_seasonal_rule: function () {
			$('#hotelier-seasonal-schema-table').on('click', '.add-rule', function (e) {
				e.preventDefault();

				var rule = $('#hotelier-seasonal-schema-table').find('tr.rule-row').last();
				var clone = HTL_Settings.clone_rule(rule);

				clone.insertAfter(rule);
				HTL_Settings.datepicker();
			});
		},

		clone_rule: function (rule) {
			var key = 1;
			var highest = 1;

			rule.parent().find('tr.rule-row').each(function () {
				var current = $(this).data('key');

				if (parseInt(current, 10) > highest) {
					highest = current;
				}
			});

			key = highest += 1;

			// Destroy datepicker
			rule.find('.date-from').datepicker('destroy').removeAttr('id');
			rule.find('.date-to').datepicker('destroy').removeAttr('id');

			var clone = rule.clone();

			clone.attr('data-key', key);
			clone.find('input').val('');
			clone.find('input.rule-index').val(parseInt(key, 10));
			clone.find('input').each(function () {
				var input = $(this);
				var name = input.attr('name');

				if (name) {
					name = name.replace(/\[(\d+)\](?!.*\[\d+\])/, '[' + parseInt(key, 10) + ']');
					input.attr('name', name);
				}
			});

			return clone;
		},

		remove_rule: function () {
			$('#hotelier-seasonal-schema-table').on('click', '.remove-rule', function (e) {
				e.preventDefault();

				var button = $(this);
				var rule = button.parent().parent();
				var rules = $('#hotelier-seasonal-schema-table').find('tr.rule-row');
				var count = rules.length;

				if (count > 1) {
					$('input', rule).val('');
					rule.fadeOut('fast').remove();
				} else {
					$('input', rule).val('');
				}

				HTL_Settings.update_rule_keys();
			});
		},

		update_rule_keys: function () {
			$('#hotelier-seasonal-schema-table').find('tr.rule-row').each(function (index) {
				var row = $(this);
				var i = index + 1;

				row.attr('data-key', i);

				row.find('input').each(function () {
					var input = $(this);
					var name = input.attr('name');

					name = name.replace(/\[(\d+)\]/, '[' + i + ']');
					input.attr('name', name);

					if (input.hasClass('rule-index')) {
						input.val(i);
					}
				});
			});
		},

		sort_rules: function () {
			$('#hotelier-seasonal-schema-table').sortable({
				items: 'tr',
				handle: '.sort-rules',
				opacity: 0.65,
				axis: 'y',
				update: function () {
					HTL_Settings.update_rule_keys();
				}
			});
		},

		datepicker: function () {
			var table = $('#hotelier-seasonal-schema-table');
			var from_inputs = table.find('.date-from');
			var to_inputs = table.find('.date-to');

			from_inputs.datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: 0,
				changeMonth: true,
				onClose: function () {
					var date = $(this).datepicker('getDate');

					if (date) {
						date.setDate(date.getDate() + 1);
						$(this).closest('tr').find('.date-to').datepicker('option', 'minDate', date);
					}
				}
			});

			to_inputs.datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: 1,
				changeMonth: true
			});
		}
	};

	$(document).ready(function () {
		HTL_Settings.init();
	});
});
