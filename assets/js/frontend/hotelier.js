jQuery(function ($) {
	"use strict";
	/* global jQuery, hotelier_params */

	// datepicker_params is required to continue, ensure the object exists
	if (typeof hotelier_params === "undefined") {
		return false;
	}

	var HTL_Hotelier = {
		listing_form: $("form.form--listing"),

		init: function () {
			this.book_now_button();
			this.show_room_meta();
			if (hotelier_params.expand_rates !== "1") {
				this.show_room_rates();
			}
			this.show_price_breakdown();
			this.scroll_to_rates_button();
			this.scroll_to_datpicker_from_rates();
			this.apply_coupon();
		},

		// Show the quantity input and update the text button
		book_now_button: function () {
			var add_to_cart_button = this.listing_form.find(
				".button--add-to-cart"
			);
			var qty_input = this.listing_form.find(".room-quantity");

			qty_input.hide();

			if (hotelier_params.book_now_redirect_to_booking_page === "1") {
				$("#reserve-button").hide();
			}

			add_to_cart_button.on("click", function (e) {
				e.preventDefault();

				var _this = $(this);
				var parent = _this.closest(".add-to-cart-wrapper");
				var qty = parent.find(".room-quantity");
				var input = qty.find("input.room-quantity__input");

				var inputValue = parseInt(input.val());

				if (!inputValue || 1 > inputValue) {
					inputValue = 1;
				}

				// Redirect to booking page directly if the option is enabled
				if (
					hotelier_params.book_now_redirect_to_booking_page === "1" &&
					hotelier_params.book_now_allow_quantity_selection !== "1"
				) {
					input.val(inputValue);
					$("#reserve-button").click();
					return;
				}

				if (!_this.hasClass("button--selected")) {
					var parent_room = _this.closest("li.room");

					if (inputValue > 1) {
						var selected_txt = _this.data("selected-text-plural");
					} else {
						var selected_txt = _this.data("selected-text-singular");
					}

					var selected_html =
						'<span class="add-to-cart-selected"><span class="add-to-cart-selected__count">' +
						inputValue +
						'</span> <span class="add-to-cart-selected__text">' +
						selected_txt +
						"</span></span>";

					parent_room.addClass("room--selected");
					_this.addClass("button--selected");
					_this.append(selected_html);
					input.val(inputValue);
					qty.show();
				} else if (parseInt(input.val(), 10) > 0) {
					// Redirect to booking page directly if the option is enabled
					if (
						hotelier_params.book_now_redirect_to_booking_page ===
						"1"
					) {
						$("#reserve-button").click();
						return;
					}

					$("html, body").animate(
						{
							scrollTop: $("#reserve-button").offset().top - 300,
						},
						600
					);
				}
			});

			qty_input.on("change", function () {
				var _this = $(this);
				var input = _this.find("input.room-quantity__input");
				var parent_room = _this.closest("li.room");
				var value = parseInt(input.val(), 10);
				var button = _this
					.closest(".add-to-cart-wrapper")
					.find(".button--add-to-cart");
				var count = button.find(".add-to-cart-selected__count");
				var text = button.find(".add-to-cart-selected__text");

				if (value === 1) {
					text.html(button.data("selected-text-singular"));
				} else {
					text.html(button.data("selected-text-plural"));

					if (isNaN(value)) {
						value = 0;
					}
				}

				count.html(value);

				if (value === 0) {
					parent_room.removeClass("room--selected");
				} else {
					parent_room.addClass("room--selected");
				}
			});
		},

		// Show/hide room details in the available rooms form (listing page)
		show_room_meta: function () {
			var room_meta = this.listing_form.find(".room__details--listing");
			var room_meta_button = this.listing_form.find(".room__more-link");
			var open_text = room_meta_button.data("open");
			var closed_text = room_meta_button.data("closed");

			room_meta.hide();
			room_meta_button.text(closed_text);

			room_meta_button.on("click", function (e) {
				e.preventDefault();

				var _this = $(this);
				var meta_id = _this.attr("href");
				var txt = $(meta_id).is(":visible") ? closed_text : open_text;

				_this.text(txt);
				$(meta_id).toggle();
			});
		},

		// Show/hide room rates in the available rooms form (listing page)
		show_room_rates: function () {
			var room_variations = this.listing_form.find(
				".room__rates--listing"
			);
			var room_variations_button = this.listing_form.find(
				".button--toggle-rates"
			);
			var open_text = room_variations_button.data("open");
			var closed_text = room_variations_button.data("closed");

			room_variations.hide();
			room_variations_button.text(closed_text);

			room_variations_button.on("click", function (e) {
				e.preventDefault();

				var _this = $(this);
				var variations_id = _this.attr("href");
				var txt = $(variations_id).is(":visible")
					? closed_text
					: open_text;

				_this.text(txt);
				$(variations_id).toggle();
			});
		},

		// Show/hide the price breakdown table
		show_price_breakdown: function () {
			var breakdown_tables = $("table.table--price-breakdown");
			var breakdown_button = $("table.table--reservation-table").find(
				".view-price-breakdown"
			);
			var open_text = breakdown_button.data("open");
			var closed_text = breakdown_button.data("closed");

			breakdown_tables.hide();
			breakdown_button.text(closed_text);

			breakdown_button.on("click", function (e) {
				e.preventDefault();

				var _this = $(this);
				var table_id = _this.attr("href");
				var table = $(table_id);
				var txt = $(table_id).is(":visible") ? closed_text : open_text;

				_this.text(txt);
				if (table.hasClass("open")) {
					table.removeClass("open");
					table.hide();
				} else {
					table.addClass("open");
					table.show();
				}
			});
		},

		// Scroll to available rates in single page
		scroll_to_rates_button: function () {
			var rates_button = $(".room-available-rates__link");
			var rates_section = $(".room__rates--single");

			if (rates_button.length === 0 || rates_section.length === 0) {
				return;
			}

			rates_button.on("click", function (e) {
				e.preventDefault();

				var target = rates_section.offset().top - 300;

				$("html, body").stop().animate(
					{
						scrollTop: target,
					},
					600
				);
			});
		},

		// Scroll to datepicker from rate in single page
		scroll_to_datpicker_from_rates: function () {
			var button = $(".button--check-availability");

			if (button.length === 0) {
				return;
			}

			// Check if default room datepicker exists
			var datepicker = $("#hotelier-datepicker");

			if (datepicker.length === 0) {
				// Check if the ajax room booking widget exists
				var ajax_room_booking_widget = $(
					"#widget-ajax-room-booking-form"
				);
				if (ajax_room_booking_widget.length !== 0) {
					datepicker = ajax_room_booking_widget;
				}
			}

			if (datepicker.length === 0) {
				return;
			}

			button.on("click", function (e) {
				e.preventDefault();

				var target = datepicker.offset().top - 300;

				$("html, body").stop().animate(
					{
						scrollTop: target,
					},
					600
				);
			});
		},

		// Handle coupon form
		apply_coupon: function () {
			$(".table--reservation-table").on(
				"click",
				".coupon-form__apply, .coupon-form__remove",
				function (e) {
					e.preventDefault();

					var _this = $(this);
					var table = _this.closest("table");
					var main_form = _this.closest("form");
					var coupon_form = _this.closest(".coupon-form");
					var coupon_input = coupon_form.find(
						"input.coupon-form__input"
					);
					var isRemoving = Boolean(
						_this.hasClass("coupon-form__remove button")
					);
					var form_data = main_form.serialize();

					var coupon_data = {
						form_data: form_data,
						coupon_nonce: hotelier_params.apply_coupon_nonce,
						coupon_code: coupon_input.val(),
						is_removing: isRemoving,
						action: "hotelier_apply_coupon",
					};

					table.removeClass("loading");
					table.addClass("loading");
					coupon_form.find(".hotelier-notice").remove();

					// Check if field is empty
					if (!isRemoving && !coupon_input.val()) {
						coupon_form.append(
							'<div class="hotelier-notice hotelier-notice--error">' +
								hotelier_params.apply_coupon_i18n.empty_coupon +
								"</div>"
						);
						table.removeClass("loading");

						return;
					}

					$.ajax({
						method: "POST",
						url: hotelier_params.ajax_url.toString(),
						data: coupon_data,
					})
						.done(function (response) {
							if (response.success === true) {
								if (response.data.html) {
									var new_template = response.data.html;
									var new_table = $(new_template)
										.find("table")
										.html();

									table.html(new_table);
									HTL_Hotelier.show_price_breakdown();
									$(window).trigger(
										"htl_window_coupon_applied"
									);
								}
							} else {
								coupon_form.append(
									'<div class="hotelier-notice hotelier-notice--error">' +
										response.data.message +
										"</div>"
								);
							}
						})
						.fail(function (response) {
							if (hotelier_params.enable_debug) {
								console.log(response);
							}
						})
						.always(function () {
							table.removeClass("loading");
						});
				}
			);
		},
	};

	$(document).ready(function () {
		HTL_Hotelier.init();
	});
});
