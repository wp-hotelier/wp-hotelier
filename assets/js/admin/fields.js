jQuery(function ($) {
	'use strict';
	/* global jQuery, HTL_Field_Multi_Text, HTL_Conditional_Fields */
	/* eslint-disable no-multi-assign */

	window.HTL_Field_Multi_Text = {
		init: function () {
			this.sortable();
			this.add_sortable_row();
			this.remove_sortable_row();
		},

		sortable: function () {
			$('table.htl-ui-table--sortable').sortable({
				items: 'tr.htl-ui-table__row--body',
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
			clone.find('input').prop('checked', false);
			clone.find('input.htl-ui-input--row_index').val(parseInt(key, 10));
			clone.find('input').each(function () {
				var input = $(this);
				var name = input.attr('name');
				var id = input.attr('id');

				if (name) {
					name = name.replace(/\[(\d+)\](?!.*\[\d+\])/, '[' + parseInt(key, 10) + ']');
					input.attr('name', name);
				}

				if (id) {
					id = id.replace(/\[(\d+)\](?!.*\[\d+\])/, '[' + parseInt(key, 10) + ']');
					input.attr('id', id);
				}
			});

			return clone;
		},

		add_sortable_row: function () {
			$('table.htl-ui-table--sortable').off('click', 'button.htl-ui-button--add-row').on('click', 'button.htl-ui-button--add-row', function (e) {
				e.preventDefault();

				var button = $(this);
				var table = button.closest('table');
				var row = table.find('tr.htl-ui-table__row--sortable').last();
				var clone = HTL_Field_Multi_Text.clone_sortable_row(row);

				clone.insertAfter(row);

				table.trigger({
					type: 'htl_multi_text_after_add_row',
					sortable_type: table.attr('data-type'),
					row: clone
				});

				$(window).trigger('htl_window_multi_text_after_add_row');
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

	window.HTL_Conditional_Fields = {
		init: function () {
			this.show_if_setting_switches(); // Switches on settings
			this.show_if_switches(); // Switches on meta boxes
			this.conditional_switches(); // Conditional switches on meta boxes

			this.show_if_setting_toggles(); // Toggles on settings
			this.show_if_toggles(); // Toggles on meta boxes
		},

		show_if_setting_switches: function () {
			var switches = $('.show-if-setting-switch');

			switches.each(function () {
				var _this = $(this);
				var show_val = _this.attr('data-show-if');
				var show_elements = _this.attr('data-show-element').split(',');
				var dom_show_elements = [];

				var i = 0;
				for (i = 0; i < show_elements.length; i++) {
					var element = $('.htl-ui-setting--' + show_elements[i]).closest('tr');
					dom_show_elements.push(element);
				}

				var selected_val = _this.find('input:checked').val();
				var inputs = _this.find('input');

				var j = 0;
				if (selected_val !== show_val) {
					for (j = 0; j < dom_show_elements.length; j++) {
						dom_show_elements[j].hide();
					}
				}

				inputs.on('click', function () {
					var i = 0;
					if ($(this).val() === show_val) {
						for (i = 0; i < dom_show_elements.length; i++) {
							dom_show_elements[i].show();
						}
					} else {
						for (i = 0; i < dom_show_elements.length; i++) {
							dom_show_elements[i].hide();
						}
					}
				});
			});
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
		},

		show_if_setting_toggles: function () {
			var toggles = $('.show-if-setting-toggle');

			toggles.each(function () {
				var _this = $(this);
				var show_val = _this.attr('data-show-if');
				var show_elements = _this.attr('data-show-element').split(',');
				var dom_show_elements = [];

				// Skip this toggle it doesn't have show-if attr
				if (!show_val) {
					return;
				}

				var i = 0;
				for (i = 0; i < show_elements.length; i++) {
					var element = $('.htl-ui-setting--' + show_elements[i]).closest('tr');
					dom_show_elements.push(element);
				}

				var checkbox = _this.find('input');
				var is_checked = checkbox.prop('checked');

				var j = 0;
				if (!is_checked) {
					for (j = 0; j < dom_show_elements.length; j++) {
						dom_show_elements[j].hide();
					}
				}

				checkbox.on('change', function () {
					var i = 0;
					if ($(this).prop('checked')) {
						for (i = 0; i < dom_show_elements.length; i++) {
							dom_show_elements[i].show();
						}
					} else {
						for (i = 0; i < dom_show_elements.length; i++) {
							dom_show_elements[i].hide();
						}
					}
				});
			});
		},

		show_if_toggles: function () {
			var toggles = $('.show-if-toggle');

			toggles.each(function () {
				var _this = $(this);
				var parent = _this.closest('.htl-ui-settings-wrap');
				var show_val = _this.attr('data-show-if');
				var show_element = _this.attr('data-show-element');
				var dom_show_element = parent.find('.htl-ui-setting-conditional[data-type=' + show_element + ']');
				var checkbox = _this.find('input');
				var is_checked = checkbox.prop('checked');

				// Skip this toggle it doesn't have show-if attr
				if (!show_val) {
					return;
				}

				if (!is_checked) {
					dom_show_element.hide();
				}

				checkbox.on('change', function () {
					if ($(this).prop('checked')) {
						dom_show_element.show();
					} else {
						dom_show_element.hide();
					}
				});
			});
		}
	};

	$(document).ready(function () {
		HTL_Field_Multi_Text.init();
		HTL_Conditional_Fields.init();
	});
});
