=== Brightcove-Wordpress ===
Contributors: Anthony_MIlls
Tags: video, brightcove, embed 
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 0.2

== Description ==
The Brightcove wordpress plugin allows uploading and embedding Brightcove hosted media

== Installation ==
1. Extract the wp-brightcove-video-plugin/ folder file to /wp-content/plugins/
2. Activate the plugin at your blog's Admin -> Plugins screen
3. Change your PHP upload limits so you can upload large videos to Brightcove either by editing your servers php.ini file. On adding the following lines to the .htaccess file in the root of your 
wordpress install:

php_value upload_max_filesize 900M
php_value post_max_size 900M

== Changelog ==

= 0.1 = 
First release

= 0.2 =
Improved upload process
Chnged function naming convention to camel case

== Screenshots ==
None

== Upgrade Notice ==
None

== Past Contributors ==
None
