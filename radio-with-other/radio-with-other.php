<?php
/*
Plugin Name: Radio With Other
Plugin URI: 
Description: 
Version: 1.0
Author: Fumito MIZUNO
Author URI: http://wp.php-web.net/
License: GPL
*/

function radiowithother_init(){
load_plugin_textdomain('radio-with-other', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}
add_action('plugins_loaded', 'radiowithother_init');

if (function_exists('register_field')){
register_field('RadioWithOther', WP_PLUGIN_DIR . '/radio-with-other/class.php');
}	
