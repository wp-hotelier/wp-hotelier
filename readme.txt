=== Easy WP Hotelier ===
Contributors: benitolopez
Tags: booking, hotel, booking system, hostel, reservations, reservations, b&b, rooms, wphotelier
Requires at least: 4.1
Tested up to: 4.9
Stable tag: 1.7.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easy WP Hotelier is a powerful WordPress booking plugin allows you to manage hotel, hostel, b&b reservations with ease.

== Description ==

Easy WP Hotelier is a booking plugin for WordPress, built specifically for hotels, b&bs, etc. With Easy WP Hotelier you can manage hotel reservations inside your WordPress dashboard, accept payments online, choose between three different booking modes, receive email notifications, manage room’s amenities, set seasonal or fixed prices and much more. Easy WP Hotelier it’s an all-in-one booking system for WordPress.

You can see Easy WP Hotelier in action [here](http://manila.wphotelier.com/). Manila is a free WordPress hotel theme developed for Easy WP hotelier specifically. You can [download it for free here](https://github.com/easy-wp-hotelier/manila/releases).

= Features =

Easy WP Hotelier is a complete booking system for WordPress. Some of the features are:

* Three different booking modes: instant booking, manual booking (requires admin approval) and booking disabled.
* Accept payments: require a deposit at the time of booking or charge the entire stay.
* Advanced room settings: manage rooms, beds, and prices.
* Seasonal prices: increase reservations by offering discounts on off-seasons.
* Mark a room non cancellable and non refundable.
* List your rooms by using shortcodes.
* Email notifications.

= How it works? =

Easy WP Hotelier allows you to create two types of rooms: standard or with rates. A room with rates (variable room) lets you define variations of a room where each variation may have a different price, required deposit or conditions.

A room represents a type of room or accommodation available at your hotel. For example, if your hotel offers three "double rooms" with the same price, amenities, etc., just create only one room and set the stock quantity to 3.

Reservations are created when a guest completes the booking process or when the Administrator (or the Hotel Manager) adds a reservation manually. When a guest makes a reservation, the availability (stock) of the room is reduced automatically.

Three different pricing options are supported out of the box: global price, different price for each day of the week and seasonal prices. And a convenient booking calendar for hotel administrators it’s included in the core.

= Documentation =

* [Getting Started](http://docs.wphotelier.com/collection/1-getting-started)
* [FAQs](http://docs.wphotelier.com/collection/10-faqs)
* [Extensions & Themes](http://docs.wphotelier.com/collection/37-extensions-themes)
* [Advanced](http://docs.wphotelier.com/collection/13-advanced)

= Extensions =

Something missing? No problem, you can extend Easy WP Hotelier with a vast number of features and integrations (more to come). Visit our [extensions page](https://wphotelier.com/extensions/) to supercharge your hotel website.

* [Stripe Payment Gateway](https://wphotelier.com/extensions/stripe-payment-gateway/)
* [Disable Dates](https://wphotelier.com/extensions/disable-dates/)
* [iCalendar Importer/Exporter](https://wphotelier.com/extensions/icalendar-importer-exporter/)
* [Week Bookings](https://wphotelier.com/extensions/week-bookings/)
* [Enhanced Calendar](https://wphotelier.com/extensions/enhanced-calendar/)
* [Flat Deposit](https://wphotelier.com/extensions/flat-deposit/)
* [Minimum/Maximum Nights](https://wphotelier.com/extensions/minimummaximum-nights/)
* [Bank Transfer Payment Gateway](https://wphotelier.com/extensions/bank-transfer-payment-gateway/)
* [MailChimp](https://wphotelier.com/extensions/mailchimp/)

== Installation ==

= Minimum Requirements =

* WordPress 4.1 or greater
* PHP version 5.6.0 or greater
* Some payment gateways require fsockopen support (for IPN access)

= Automatic =

1. Visit 'Plugins > Add New'.
2. Search for 'Easy WP Hotelier'.
3. Activate Easy WP Hotelier from your 'Plugins' page.

You should see the 'Easy WP Hotelier' menu in your WordPress admin panel.

= Manually =

1. Upload the entire `wp-hotelier` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

You should see the 'Easy WP Hotelier' menu in your WordPress admin panel.

= Dummy Data =

Easy WP Hotelier comes with some sample data you can use to see how rooms look. You can find the sample_data.xml file in `wp-hotelier/sample-data/sample_data.xml`. To import the data, use the [WordPress importer](https://wordpress.org/plugins/wordpress-importer/) plugin.

= Extra =

Easy WP Hotelier requires two pages to work: the `listing` page and the `booking` page. Make sure that you have them and that they are set correctly in the [Easy WP Hotelier's settings](http://docs.wphotelier.com/article/9-general-settings#hotelier-pages)

== Frequently Asked Questions ==

= Will Easy WP Hotelier work with my theme? =

Yes; Easy WP Hotelier will work with any theme, but may require some styling to make it match nicely.

= Where can I find the Easy WP Hotelier documentation? =

Here: [Easy WP Hotelier documentation](http://docs.wphotelier.com/).

== Screenshots ==

1. Easy WP Hotelier - General settings panel.
2. Easy WP Hotelier - Room data panel.
3. Easy WP Hotelier - List of reservations.
4. Easy WP Hotelier - Admin calendar.

== Changelog ==

= 1.7.0 - 2018-06-17 =
* Added - Taxes module.
* Added - Added a remove button for the booking cart.
* Tweak - Filter for stock rooms.

= 1.6.0 - 2018-05-28 =
* Added - GDPR support.
* Tweak - Filter for hide pets message.

= 1.5.3 - 2018-05-19 =
* Added - New free WordPress theme for Easy WP Hotelier: Manila (http://manila.wphotelier.com/).
* Tweak - Minor markup fixes in the listing page and room archive page.
* Added - Input number type for booking fields.

= 1.5.2 - 2018-03-14 =
* Fix - Allow to cancel reservations for non logged-in users.
* Fix - Fix escapes on archive title.

= 1.5.1 - 2018-03-14 =
* Tweak - Move terms checkbox before booking button.

= 1.5.0 - 2018-03-06 =
* Added - General improvements to the listing page.
* Added - Option to redirect the user to the booking page after successful addition.
* Update - Hotel Datepicker library updated
* Localization - POT file updated.

= 1.4.0 - 2018-02-21 =
* Added - Show (and save) a dropdown where a guest can select the number of adults/children.
* Added - Refunds via PayPal and Stripe are now detected automatically (cancelling the reservation).
* Added - New refunded status for reservations.
* Added - View up to 4 weeks in admin calendar.
* Localization - POT file updated.

= 1.3.0 - 2018-02-09 =
* Added - Seasonal prices can now be repeated every year.
* Fixed - Fixed a minor bug on seasonal prices.
* Localization - POT file updated.

= 1.2.0 - 2018-01-05 =
* Changed - Save calculated deposit (and the percentage) in the reservation meta.
* Tweak - Allow to filter line to pay in cart.
* Tweak - Add filter for room deposit.
* Tweak - Minor tweaks.
* Localization - POT file updated.

= 1.1.9 - 2017-12-19 =
* Added - New action in reservation meta boxes.
* Tweak - Delete cron jobs when uninstalling.
* Added - Multi-checkbox setting helper.
* Localization - POT file updated.

= 1.1.8 - 2017-12-04 =
* Deleted - Removed unused file.
* Localization - POT file updated.

= 1.1.7 - 2017-11-21 =
* Add WordPress 4.9 support.

= 1.1.6 - 2017-10-24 =
* Fix - Fix months in advance setting (use correct value).
* Localization - POT file updated.

= 1.1.5 - 2017-09-27 =
* Fix - Fix admin scripts errors when the "Hotelier" string is translated.
* Localization - POT file updated.

= 1.1.4 - 2017-09-15 =
* Tweak - Show ETA (Estimated Arrival Time) in emails.
* Tweak - New action in guest emails so 3rd party plugins can add their details.
* Update - Hotel Datepicker library updated
* Localization - POT file updated.

= 1.1.3 - 2017-08-07 =
* Update - Hotel Datepicker library updated
* Fix - Fix timezone error on disabled dates (datepicker).
* Fix - Fix DST (Daylight Saving Time) issues (datepicker).
* Fix - Fix count comments error.

= 1.1.2 - 2017-07-31 =
* Fix - Fix JS console error in the hotel datepicker library.
* Localization - POT file updated.

= 1.1.1 - 2017-06-22 =
* Fix - Fix in room meta boxes (some settings were not saved correctly).

= 1.1.0 - 2017-06-21 =
* Change - Use 'wp-hotelier' for the i18n slug.
* Localization - POT file updated and renamed.

= 1.0.1 - 2017-06-19 =
* Fix - Use only Hotelier in menu name and fix screen IDs related issues.
* Fix - Use correct link in admin footer pages.
* Localization - POT file updated.

= 1.0.0 - 2017-06-18 =
* First public release.

== Credits ==

This program incorporates work covered by WooCommerce (https://woocommerce.com/). Thank you very much to all the WooThemes team for the permission.

And it includes some awesome JS libraries and plugins like:

- PhotoSwipe by Dmitry Semenov (http://photoswipe.com/)
- Fecha by Taylor Hakes (https://github.com/taylorhakes/fecha)

Thank you guys :)
