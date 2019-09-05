jQuery(function ($) {
	'use strict';
	/* global HTL_Field_Multi_Text, HTL_Conditional_Fields, jQuery, wp */
	/* eslint-disable no-multi-assign */

	var HTL_Room_Meta = {
		init: function () {
			this.show_room_type_panel();
		},

		show_room_type_panel: function () {
			var room_type_switch = $('.htl-ui-switch--room-type');
			var room_type_standard_input = room_type_switch.find('input.htl-ui-switch__input--standard_room');
			var room_type_variations_input = room_type_switch.find('input.htl-ui-switch__input--variable_room');
			var standard_room_settings = $('.room-settings__standard');
			var variations_room_settings = $('.room-settings__variations');

			if (room_type_standard_input.prop('checked')) {
				standard_room_settings.show();
				variations_room_settings.hide();
			} else {
				standard_room_settings.hide();
				variations_room_settings.show();
			}

			room_type_standard_input.add(room_type_variations_input).on('change', function () {
				if (room_type_standard_input.prop('checked')) {
					standard_room_settings.show();
					variations_room_settings.hide();
				} else {
					standard_room_settings.hide();
					variations_room_settings.show();
				}
			});
		}
	};

	var HTL_Room_Variations = {
		init: function () {
			this.toggle_variations();
			this.toggle_variation();
			this.new_variation();
			this.new_clone_variation();
			this.remove_variation();
			this.sort_variations();
		},

		toggle_variations: function () {
			var expand_all = $('.htl-ui-text-icon--expand-variation');
			var close_all = $('.htl-ui-text-icon--collapse-variation');

			expand_all.on('click', function (e) {
				e.preventDefault();
				$('.room-variation__content').stop().slideDown();
			});

			close_all.on('click', function (e) {
				e.preventDefault();
				$('.room-variation__content').stop().slideUp();
			});
		},

		toggle_variation: function () {
			$('.room-settings__variations').on('click', '.room-variation__header', function (e) {
				var dom = $(e.target);

				if (!dom.hasClass('htl-ui-input--select') && !dom.hasClass('htl-ui-icon--clone-variation') && !dom.hasClass('htl-ui-icon--delete-variation') && !dom.hasClass('htl-ui-icon--drag-variation')) {
					$(this).closest('.room-variation').find('.room-variation__content').stop().slideToggle();
				}
			});
		},

		new_variation: function () {
			$('.htl-ui-button--add-room-rate').on('click', function () {
				var variation_placeholder = $('.room-variation--placeholder');
				var clone = HTL_Room_Variations.clone_variation(variation_placeholder);

				$('.room-variations__list').append(clone);
				HTL_Field_Multi_Text.init();
				HTL_Conditional_Fields.init();
			});
		},

		new_clone_variation: function () {
			$('.room-settings__variations').on('click', '.htl-ui-icon--clone-variation', function () {
				var variation = $(this).closest('.room-variation');
				var clone = HTL_Room_Variations.clone_variation(variation);

				$('.room-variations__list').append(clone);
				HTL_Field_Multi_Text.init();
				HTL_Conditional_Fields.init();
			});
		},

		clone_variation: function (variation) {
			var key = 1;
			var highest = 1;

			variation.parent().find('.room-variation--in-use').each(function () {
				var current = $(this).data('key');

				if (parseInt(current, 10) > highest) {
					highest = current;
				}
			});

			key = highest += 1;

			var clone = variation.clone();

			clone.removeClass('room-variation--placeholder').addClass('room-variation--in-use');
			clone = HTL_Room_Variations.update_keys(clone, key);

			return clone;
		},

		update_keys: function (clone, key) {
			clone.attr('data-key', key);
			clone.find('input, select, label').each(function () {
				var input = $(this);
				var name = input.attr('name');
				var for_attr = input.attr('for');

				if (name) {
					name = name.replace(/\[(\d+)\]/, '[' + parseInt(key, 10) + ']');
					input.attr('name', name);
				}

				if (for_attr) {
					for_attr = for_attr.replace(/\[(\d+)\]/, '[' + parseInt(key, 10) + ']');
					input.attr('for', for_attr);
				}

				if (input.hasClass('htl-ui-input--room-variation-index')) {
					input.val(parseInt(key, 10));
				}
			});

			// Fix switch IDs
			var switch_inputs = clone.find('.htl-ui-switch__input');

			switch_inputs.each(function () {
				var _this = $(this);
				var id_attr = _this.attr('id');

				if (id_attr) {
					id_attr = id_attr.replace(/\[(\d+)\]/, '[' + parseInt(key, 10) + ']');
					_this.attr('id', id_attr);
				}
			});

			// Fix toggle IDs
			var toggle_inputs = clone.find('.htl-ui-toggle__input');

			toggle_inputs.each(function () {
				var _this = $(this);
				var id_attr = _this.attr('id');

				if (id_attr) {
					id_attr = id_attr.replace(/\[(\d+)\]/, '[' + parseInt(key, 10) + ']');
					_this.attr('id', id_attr);
				}
			});

			return clone;
		},

		remove_variation: function () {
			$('.room-settings__variations').on('click', '.htl-ui-icon--delete-variation', function () {
				var variation = $(this).closest('.room-variation');
				var variations = $('.room-variation--in-use');
				var count = variations.length;

				if (count > 1) {
					$('input, select', variation).val('');
					variation.fadeOut('fast').remove();
				}

				$('.room-variation--in-use').each(function (index) {
					var key = index + 1; // Start from 1
					HTL_Room_Variations.update_keys($(this), key);
				});
			});
		},

		sort_variations: function () {
			$('.room-variations__list').sortable({
				items: '.room-variation--in-use',
				handle: '.htl-ui-icon--drag-variation',
				opacity: 0.65,
				axis: 'y',
				update: function () {
					var count = 1;

					$('.room-variation--in-use').each(function () {
						$(this).find('input.htl-ui-input--room-variation-index').each(function () {
							$(this).val(count);
						});

						count++;
					});
				}
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
		HTL_Room_Gallery.init();
	});
});
