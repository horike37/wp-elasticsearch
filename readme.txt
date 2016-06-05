=== WP Simple Elasticsearch ===
Contributors: horike,amimotoami
Donate link: https://github.com/horike37/wp-elasticsearch
Tags: search, elasticsearch
Requires at least: 4.5
Tested up to: 4.5.2
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is that WordPress of standard search using `?s=xxx` to replace WordPress DB with Elasticsearch.

== Description ==

We adopted the Elasticsearch to search engine. You can also search, such as post title, post content, custom fields by replacing the WordPress of standard search function. The accuracy of the search feature dramatically improves in WordPress.

= Setting =
* In the [screenshoot section](https://wordpress.org/plugins/wp-simple-elasticsearch/screenshots/) you can look on the first screenshot. 
* Require setting to `Elasticsearch Endpoint`,`Port`,`index`,`type` and push `Save Changes`. It is default search target `post title`, `post content`.
* Please push `Post Data sync to Elasticsearch`. So Posts data are sent to Elasticsearch.

== Installation ==
1. Upload elasticommerce-search-form to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Please set up on Settings > WP Elasticsearch.

== Screenshots ==
1. Setting Screen

== Changelog ==
= 0.1 =
* first Release.
