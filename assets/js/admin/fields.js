jQuery(function ($) {
	'use strict';
	/* global jQuery */
	/* eslint-disable no-multi-assign */

	var HTL_Field_Multi_Text = {
		init: function () {
			this.sortable();
			this.add_sortable_row();
			this.remove_sortable_row();
		},

		sortable: function () {
			$('table.htl-ui-table--sortable').sortable({
				items: 'tr',
				handle: 'td.htl-ui-table__cell--sort-row',
				opacity: 0.65,
				axis: 'y',
				update: function () {
					HTL_Field_Multi_Text.update_sortable_keys($(this));
				}
			});
		},

		clone_sortable_row: function (row) {
			var key = 1;
			var highest = 1;

			row.parent().find('tr.htl-ui-table__row--sortable').each(function () {
				var current = $(this).data('key');

				if (parseInt(current, 10) > highest) {
					highest = current;
				}
			});

			key = highest += 1;

			var table = row.closest('table');

			table.trigger({
				type: 'htl_multi_text_before_clone_row',
				sortable_type: table.attr('data-type'),
				row: row
			});

			var clone = row.clone();

			clone.attr('data-key', key);
			clone.find('input').val('');
			clone.find('input.htl-ui-input--row_index').val(parseInt(key, 10));
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

		add_sortable_row: function () {
			$('table.htl-ui-table--sortable').on('click', 'button.htl-ui-button--add-row', function (e) {
				e.preventDefault();

				var button = $(this);
				var table = button.closest('table');
				var row = table.find('tr.htl-ui-table__row--sortable').last();
				var clone = HTL_Field_Multi_Text.clone_sortable_row(row);

				clone.insertAfter(row);

				table.trigger({
					type: 'htl_multi_text_after_add_row',
					sortable_type: table.attr('data-type')
				});
			});
		},

		remove_sortable_row: function () {
			$('table.htl-ui-table--sortable').on('click', '.htl-ui-button--remove-row', function (e) {
				e.preventDefault();

				var button = $(this);
				var table = button.closest('table');
				var row = button.parent().parent();
				var rows = table.find('tr.htl-ui-table__row--sortable');
				var count = rows.length;

				if (count > 1) {
					$('input', row).val('');
					row.fadeOut('fast').remove();
				} else {
					$('input', row).val('');
				}

				HTL_Field_Multi_Text.update_sortable_keys(table);

				table.trigger({
					type: 'htl_multi_text_after_remove_row',
					sortable_type: table.attr('data-type'),
					row: row
				});
			});
		},

		update_sortable_keys: function (container) {
			container.find('tr.htl-ui-table__row--sortable').each(function (index) {
				var row = $(this);
				var i = index + 1;

				row.attr('data-key', i);

				row.find('input').each(function () {
					var input = $(this);
					var name = input.attr('name');

					name = name.replace(/\[(\d+)\](?!.*\[\d+\])/, '[' + i + ']');
					input.attr('name', name);

					if (input.hasClass('htl-ui-input--row_index')) {
						input.val(i);
					}
				});
			});
		}
	};

	var HTL_Conditional_Fields = {
		init: function () {
			this.show_if_switches();
			this.conditional_switches();
		},

		show_if_switches: function () {
			var switches = $('.show-if-switch');

			switches.each(function () {
				var _this = $(this);
				var parent = _this.closest('.htl-ui-settings-wrap');
				var show_val = _this.attr('data-show-if');
				var show_element = _this.attr('data-show-element');
				var dom_show_element = parent.find('.htl-ui-setting-conditional[data-type=' + show_element + ']');
				var selected_val = _this.find('input:checked').val();
				var inputs = _this.find('input');

				if (selected_val !== show_val) {
					dom_show_element.hide();
				}

				inputs.on('click', function () {
					if ($(this).val() === show_val) {
						dom_show_element.show();
					} else {
						dom_show_element.hide();
					}
				});
			});
		},

		conditional_switches: function () {
			var switches = $('.conditional-switch');

			switches.each(function () {
				var _this = $(this);
				var parent = _this.closest('.htl-ui-settings-wrap');
				var inputs = _this.find('input');
				var conditional_selector = _this.attr('data-conditional-selector');
				var conditional_elements = parent.find('.htl-ui-setting-conditional--' + conditional_selector);
				var selected_val = _this.find('input:checked').val();

				conditional_elements.hide();

				conditional_elements.each(function (i, el) {
					var element = $(el);

					if (element.data('type') === selected_val) {
						element.show();
					}
				});

				inputs.on('click', function () {
					var input_val = $(this).val();

					conditional_elements.each(function (i, el) {
						var element = $(el);

						if (element.data('type') === input_val) {
							element.show();
						} else {
							element.hide();
						}
					});
				});
			});
		}
	};

	$(document).ready(function () {
		HTL_Field_Multi_Text.init();
		HTL_Conditional_Fields.init();
	});
});
