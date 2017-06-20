=== Easy WP Hotelier ===
Contributors: benitolopez
Tags: booking, hotel, booking system, hostel, reservations, reservations, b&b, rooms, wphotelier
Requires at least: 4.1
Tested up to: 4.8
Stable tag: 1.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easy WP Hotelier is a powerful WordPress booking plugin allows you to manage hotel, hostel, b&b reservations with ease.

== Description ==

Easy WP Hotelier is a booking plugin for WordPress, built specifically for hotels, b&bs, etc. With Easy WP Hotelier you can manage hotel reservations inside your WordPress dashboard, accept payments online, choose between three different booking modes, receive email notifications, manage room’s amenities, set seasonal or fixed prices and much more. Easy WP Hotelier it’s an all-in-one booking system for WordPress.

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
* [Minimum/Maximum Nights](https://wphotelier.com/extensions/minimummaximum-nights/)

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

= 1.0.0 - 2017-06-19 =
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
