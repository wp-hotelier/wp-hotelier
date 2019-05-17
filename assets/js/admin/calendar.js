jQuery(function ($) {
	'use strict';
	/* global jQuery */

	var HTL_Calendar = {
		init: function () {
			this.datepicker();
		},

		datepicker: function () {
			$('.bc-datepicker').datepicker({
				dateFormat: 'yy-mm-dd'
			});
		}
	};

	$(document).ready(function () {
		HTL_Calendar.init();
	});
});
