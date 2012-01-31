<?php defined('BASEPATH') OR exit('No direct script access allowed');

// plugin configuration variables
define('FB_ALBUM_TABLE', $table_prefix.'fb_albums');
define('FB_PHOTO_TABLE', $table_prefix.'fb_photos');
define('FB_POSTS_TABLE', $table_prefix.'posts');
define('FB_PLUGIN_PATH', WP_PLUGIN_DIR.'/fbgallery/');
define('FB_PLUGIN_URL', plugins_url().'/fbgallery/');
define('FB_PLUGIN_URL_JS', plugins_url().'/fbgallery/js/');
define('FB_STYLE_URL', FB_PLUGIN_URL.'styles/'.get_option('fb_style').'/');
define('FB_STYLE_PATH', FB_PLUGIN_PATH.'styles/'.get_option('fb_style').'/');
define('FB_MANAGE_URL', (get_bloginfo('version') >= 2.7 ? 'media-new.php' : 'edit.php') .'?page=smfbgallery/manage-smfbgallery.php');
define('FB_OPTIONS_URL', 'options-general.php?page=smfbgallery/options-smfbgallery.php');
define('AJAX_IMAGE_PATH', FB_PLUGIN_URL.'images/ajax_wait.gif');
define('FB_VERSION', 3.23);
define('FB_API_KEY','157508894357913');
define('FB_API_SECRET','7a8e0c7d1c6eb20a2bf17ff9cb42b934');

$config['THUMB_WIDTH']= 150;
$config['THUMB_HEIGHT']= 100;
$config['FB_API_KEY']= '157508894357913';
$config['FB_API_SECRET']= '7a8e0c7d1c6eb20a2bf17ff9cb42b934';
$config['APP_ID']= '157508894357913';
$config['SECRET_ID']= '7a8e0c7d1c6eb20a2bf17ff9cb42b934';
$config['addon_path']='addons/default/modules/';
$config['encryption_key'] = "abc1234";