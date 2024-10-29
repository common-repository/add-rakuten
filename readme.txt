=== Ad Rakuten ===
Author: FALEME Jonathan
Version: 0.0.11
Contributors: scion-lucifer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=U4HBAF7QGT9XN
Tags: rakuten, webservice, shortcode, 楽天, ウェブサービス, ショートコード
Requires at least: 4.0
Tested up to: 4.9.7
Requires PHP: 5.2.4
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With this plugin you can add Rakuten products in your website.

== Description ==

With this plugin you can add Rakuten products in your website.

Links: [Rakuten Developers](https://webservice.rakuten.co.jp/) | [Author's Profile](https://profiles.wordpress.org/scion-lucifer/) | [Author's Site](https://japanfigs.com/)

[youtube https://www.youtube.com/watch?v=2JysyZt6WRw]

== Installation ==

1. **Visit** Plugins > Add New
2. **Search** for "Ad Rakuten"
3. **Activate** Ad Rakuten from your Plugins page
4. **Click** on the new menu item "Rakuten" and enter your [Rakuten API key](https://webservice.rakuten.co.jp/).

== Screenshots ==

1. Click on the Rakuten Button

2. Search your item(s) (ex: あゆみ浜崎)

3. Select the items that you want to add in your post

4. A Shortcode will be create

5. After publish your post, you can see your items

== Changelog ==

= 0.0.9 =
* add additional search options: Keyword (default)
New: Genre ID, Item code, Shop code

= 0.0.8 =
* add the first customize options for the no developers peoples. 
=> The developers can use the wordpress filter, example: 
add_filter( "rakuten_item_price_color", "my_custom_price_color", 10, 1 );
=> or the developers can use the wordpress hook, example:
add_action('wp_head', 'my_custom_head'); 

= 0.0.7 =
* remove $rakuten_translation + rakuten_translation() and use __() function (@garyj, @tobifjellner and @peexy : thank you for your help)

= 0.0.6 =
* add the function : get_rakuten_item_tpl() + apply_filters 
* translate second test

= 0.0.3 =
* translate test: https://translate.wordpress.org/

= 0.0.2 =
* first commit

= 0.0.1 =
* first release