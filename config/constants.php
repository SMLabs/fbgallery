<?php
define('FB_ALBUM_TABLE', $table_prefix.'fb_albums');
define('FB_PHOTO_TABLE', $table_prefix.'fb_photos');
define('FB_POSTS_TABLE', $table_prefix.'posts');
define('FB_PLUGIN_PATH', WP_PLUGIN_DIR.'/fbgallery/');
define('FB_PLUGIN_URL', plugins_url().'/fbgallery/');
define('FB_PLUGIN_URL_JS', plugins_url().'/fbgallery/js/');
define('FB_STYLE_URL', FB_PLUGIN_URL.'styles/'.get_option('fb_style').'/');
define('FB_STYLE_PATH', FB_PLUGIN_PATH.'styles/'.get_option('fb_style').'/');
define('FB_MANAGE_URL', (get_bloginfo('version') >= 2.7 ? 'media-new.php' : 'edit.php') .'?page=fbgallery/manage-smfbgallery.php');
define('FB_OPTIONS_URL', 'options-general.php?page=fbgallery/options-smfbgallery.php');
define('AJAX_IMAGE_PATH', FB_PLUGIN_URL.'images/ajax_wait.gif');
define('FB_VERSION', 3.23);
define('FB_API_KEY',      '157508894357913');
define('FB_API_SECRET',   '7a8e0c7d1c6eb20a2bf17ff9cb42b934');
define('APP_ID', '157508894357913');				# UPDATE
define('SECRET_ID', '7a8e0c7d1c6eb20a2bf17ff9cb42b934');			# UPDATE

define('THUMB_WIDTH', 150);
define('THUMB_HEIGHT', 100);