jQuery(function ($) {
	'use strict';
	/* global room_params, jQuery, wp */
	/* eslint-disable no-multi-assign */

	var HTL_Room_Meta = {
		init: function () {
			this.show_meta();
			this.sortable();
			this.add_condition();
			this.remove_condition();
			this.show_errors();
		},

		show_meta: function () {
			$('.price-panel').hide();
			$('.room-deposit-amount').hide();

			$('.room-advanced-settings').on('change', '.room-price', function (e) {
				e.preventDefault();

				var val = $(this).val();
				var parent = $(this).closest('.room-price-panel');

				parent.find('.price-panel').hide();
				parent.find('.price-panel-' + val).show();
			});

			$('.room-price').each(function () {
				var val = $(this).val();
				var parent = $(this).closest('.room-price-panel');

				parent.find('.price-panel-' + val).show();
			});

			$('.require-deposit').each(function () {
				var _this = $(this);
				var checked = _this.is(':checked');
				var parent = _this.closest('.form-field');

				if (checked) {
					parent.next().show();
				}
			});

			$('.room-advanced-settings').on('click', '.require-deposit', function () {
				var _this = $(this);
				var parent = _this.closest('.form-field');

				if (_this.is(':checked')) {
					parent.next().show();
				} else {
					parent.next().hide();
				}
			});
		},

		sortable: function () {
			$('.room-conditions').sortable({
				items: 'tr',
				handle: '.sort-conditions',
				opacity: 0.65,
				axis: 'y',
				update: function () {
					HTL_Room_Meta.update_conditions_keys($(this));
				}
			});
		},

		clone_condition: function (condition) {
			var key = 1;
			var highest = 1;

			condition.parent().find('tr.room-condition').each(function () {
				var current = $(this).data('key');

				if (parseInt(current, 10) > highest) {
					highest = current;
				}
			});

			key = highest += 1;

			var clone = condition.clone();

			clone.attr('data-key', key);
			clone.find('input').val('');
			clone.find('input.condition-index').val(parseInt(key, 10));
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

		add_condition: function () {
			$('.room-advanced-settings').on('click', '.add-condition', function (e) {
				e.preventDefault();

				var button = $(this);
				var condition = button.closest('table').find('tr.room-condition').last();
				var clone = HTL_Room_Meta.clone_condition(condition);

				clone.insertAfter(condition);
			});
		},

		remove_condition: function () {
			$('.room-advanced-settings').on('click', '.remove-condition', function (e) {
				e.preventDefault();

				var button = $(this);
				var table = button.closest('table');
				var condition = button.parent().parent();
				var conditions = table.find('tr.room-condition');
				var count = conditions.length;

				if (count > 1) {
					$('input', condition).val('');
					condition.fadeOut('fast').remove();
				} else {
					$('input', condition).val('');
				}

				HTL_Room_Meta.update_conditions_keys(table);
			});
		},

		update_conditions_keys: function (container) {
			container.find('tr.room-condition').each(function (index) {
				var row = $(this);
				var i = index + 1;

				row.attr('data-key', i);

				row.find('input').each(function () {
					var input = $(this);
					var name = input.attr('name');

					name = name.replace(/\[(\d+)\](?!.*\[\d+\])/, '[' + i + ']');
					input.attr('name', name);

					if (input.hasClass('condition-index')) {
						input.val(i);
					}
				});
			});
		},

		show_errors: function () {
			$('.price-panel-global').on('keyup change', 'input[name^=_sale_price]', function () {
				var sale_price_field = $(this);
				var parent = sale_price_field.parent().parent();
				var regular_price_field = sale_price_field.closest('.price-panel').find('input[name^=_regular_price]');
				var sale_price = parseFloat(window.accounting.unformat(sale_price_field.val(), room_params.decimal_point));
				var regular_price = parseFloat(window.accounting.unformat(regular_price_field.val(), room_params.decimal_point));

				if (sale_price >= regular_price) {
					parent.css('position', 'relative').append('<div class="error-tooltip sale-error"></div>');
					parent.find('.sale-error').text(room_params.sale_less_than_regular_error);
				} else {
					parent.find('.sale-error').remove();
				}
			})
			.on('keyup change', 'input.htl-input-price', function () {
				var value = $(this).val();
				var parent = $(this).parent().parent();
				var regex = new RegExp('[^\-0-9\%\\' + room_params.decimal_point + ']+', 'gi');
				var newvalue = value.replace(regex, '');

				if (value !== newvalue) {
					$(this).val(newvalue);
					parent.css('position', 'relative').append('<div class="error-tooltip decimal-error"></div>');
					parent.find('.decimal-error').text(room_params.decimal_error);
				} else {
					parent.find('.decimal-error').remove();
				}
			});
		}
	};

	var HTL_Room_Variations = {
		init: function () {
			this.show_variations();
			this.sortable();
			this.toggle_variations();
			this.add_variation();
			this.remove_variation();
		},

		show_variations: function () {
			var room_type = $('#_room_type');
			var variation_panel = $('.variation-room-panel');
			var standard_panel = $('.standard-room-panel');

			variation_panel.hide();

			if (room_type.val() === 'variable_room') {
				variation_panel.show();
				standard_panel.hide();
			}

			room_type.change(function () {
				if ($(this).val() === 'variable_room') {
					variation_panel.show();
					standard_panel.hide();
				} else {
					variation_panel.hide();
					standard_panel.show();
				}
			});
		},

		sortable: function () {
			$('.room-variations').sortable({
				items: '.room-variation',
				handle: '.room-variation-header',
				axis: 'y',
				start: function (event, ui) {
					var styles = {
						backgroundColor: '#f6f6f6',
						border: 'none'
					};

					ui.item.css(styles);
				},
				stop: function (event, ui) {
					ui.item.removeAttr('style');
				},
				update: function () {
					var count = 1;

					$(this).find('.room-variation').each(function () {
						$(this).find('input.variation-index').each(function () {
							$(this).val(count);
						});
						count++;
					});
				}
			});
		},

		toggle_variations: function () {
			var expand_all = $('.expand-all');
			var close_all = $('.close-all');

			$('.room-variation.closed').each(function () {
				$(this).find('.room-variation-content').hide();
			});

			$('.room-variations').on('click', '.room-variation-header', function (event) {
				if ($(event.target).filter(':input, option').length > 0) {
					return;
				}

				$(this).next('.room-variation-content').stop().slideToggle();
			});

			expand_all.on('click', function (e) {
				e.preventDefault();
				$('.room-variation-content').show();
			});

			close_all.on('click', function (e) {
				e.preventDefault();
				$('.room-variation-content').hide();
			});
		},

		clone_variation: function (variation) {
			var key = 1;
			var highest = 1;

			variation.parent().find('.room-variation').each(function () {
				var current = $(this).data('key');

				if (parseInt(current, 10) > highest) {
					highest = current;
				}
			});

			key = highest += 1;

			var clone = variation.clone();

			clone.removeClass('closed');
			clone.find('.room-variation-content').removeAttr('style');
			clone.attr('data-key', key);
			clone.find('input').val('').removeAttr('checked');
			clone.find('select').each(function () {
				var input = $(this);

				input.find('option:selected').prop('selected', false);
				input.find('option:first').prop('selected', 'selected');
			});
			clone.find('input[type="checkbox"]').val(1);
			clone.find('.price-panel-global').show();
			clone.find('.price-panel-per_day').hide();
			clone.find('.price-panel-seasonal_price').hide();
			clone.find('.room-deposit-amount').hide();
			clone.find('input.variation-index').val(parseInt(key, 10));
			clone.find('tr.room-condition').not(':first').remove();
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

		add_variation: function () {
			var variation_button = $('.add-variation');

			variation_button.on('click', function (e) {
				e.preventDefault();

				var variation = $('.room-variations').find('.room-variation').last();
				var clone = HTL_Room_Variations.clone_variation(variation);

				clone.insertAfter(variation);
				HTL_Room_Meta.sortable();
			});
		},

		remove_variation: function () {
			$('.room-variations').on('click', '.remove-variation', function (e) {
				e.preventDefault();

				var button = $(this);
				var variation = button.parent().parent();
				var variations = $('.room-variations').find('.room-variation');
				var count = variations.length;

				if (count > 1) {
					$('input, select', variation).val('');
					variation.fadeOut('fast').remove();
				}

				variations.each(function (index) {
					var row = $(this);

					row.find('input, select').each(function () {
						var input = $(this);
						var name = input.attr('name');

						name = name.replace(/\[(\d+)\]/, '[' + index + ']');
						input.attr('name', name);

						if (input.hasClass('variation-index')) {
							input.val(index);
						}
					});
				});
			});
		}
	};

	var HTL_Room_Additional_Settings = {
		init: function () {
			this.show_additional_settings();
		},

		show_additional_settings: function () {
			var general_panel = $('.room-general-settings');
			var advanced_panel = $('.room-advanced-settings');
			var additional_panel = $('#room-additional-settings');

			additional_panel.hide();

			$('#view-room-additional-settings').on('click', function (e) {
				e.preventDefault();
				additional_panel.show();
				general_panel.hide();
				advanced_panel.hide();
			});

			$('#close-room-additional-settings').on('click', function (e) {
				e.preventDefault();
				additional_panel.hide();
				general_panel.show();
				advanced_panel.show();
			});
		}
	};

	var HTL_Room_Gallery = {
		init: function () {
			this.add_images();
			this.sort_images();
			this.delete_images();
		},

		add_images: function () {
			var add_images_button = $('#add-room-images');
			var room_gallery_frame;
			var room_gallery_ids = $('#room-image-gallery');
			var room_images = $('#room-images-container').find('ul.room-images');

			add_images_button.on('click', function (e) {
				e.preventDefault();

				var _this = $(this);

				// If the media frame already exists, reopen it.
				if (room_gallery_frame) {
					room_gallery_frame.open();
					return;
				}

				// Create the media frame.
				room_gallery_frame = wp.media.frames.room_gallery = wp.media({
					// Set the title of the modal.
					title: _this.data('choose'),
					button: {
						text: _this.data('update')
					},
					states: [
						new wp.media.controller.Library({
							title: _this.data('choose'),
							filterable: 'all',
							multiple: true
						})
					]
				});

				// When an image is selected, run a callback.
				room_gallery_frame.on('select', function () {
					var selection = room_gallery_frame.state().get('selection');
					var attachment_ids = room_gallery_ids.val();

					selection.map(function (attachment) {
						attachment = attachment.toJSON();

						if (attachment.id) {
							attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
							var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

							room_images.append('<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><a href="#" class="delete" title="' + _this.data('delete') + '">' + _this.data('text') + '</a></li>');
						}
					});

					room_gallery_ids.val(attachment_ids);
				});

				// Finally, open the modal.
				room_gallery_frame.open();
			});
		},

		sort_images: function () {
			var room_images = $('#room-images-container').find('ul.room-images');
			var room_gallery_ids = $('#room-image-gallery');

			room_images.sortable({
				items: 'li.image',
				cursor: 'move',
				forcePlaceholderSize: true,
				forceHelperSize: false,
				helper: 'clone',
				opacity: 0.65,
				placeholder: 'htl-image-sortable-placeholder',
				start: function (event, ui) {
					var styles = {
						backgroundColor: '#f6f6f6'
					};

					ui.item.css(styles);
				},
				stop: function (event, ui) {
					ui.item.removeAttr('style');
				},
				update: function () {
					var attachment_ids = '';

					$('#room-images-container').find('ul li.image').css('cursor', 'default').each(function () {
						var attachment_id = jQuery(this).attr('data-attachment_id');
						attachment_ids = attachment_ids + attachment_id + ',';
					});

					room_gallery_ids.val(attachment_ids);
				}
			});
		},

		delete_images: function () {
			var room_gallery_ids = $('#room-image-gallery');
			var room_images = $('#room-images-container');

			room_images.on('click', 'a.delete', function (e) {
				e.preventDefault();

				$(this).closest('li.image').remove();

				var attachment_ids = '';

				room_images.find('ul li.image').css('cursor', 'default').each(function () {
					var attachment_id = jQuery(this).attr('data-attachment_id');
					attachment_ids = attachment_ids + attachment_id + ',';
				});

				room_gallery_ids.val(attachment_ids);
			});
		}
	};

	$(document).ready(function () {
		HTL_Room_Meta.init();
		HTL_Room_Variations.init();
		HTL_Room_Additional_Settings.init();
		HTL_Room_Gallery.init();
	});
});
