<?php
/*
Plugin Name: MDKN Donation
Plugin URI: http://tecbraces.com    
Description: Basic donation for the mosque 
Version: 1.0
Author: MD KHAN
Author URI: http://www.monirkhan.net
License: GPL2

 * 
 */


// Restricted direct acces to the plugin files 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// define plugin root directory 
define( 'PLUGIN_DIR_PATH', plugin_dir_path( __FILE__) );

// define plugine uri
define('PLUGIN_URL',  plugin_dir_url(__FILE__) );

// Render Add members
require_once PLUGIN_DIR_PATH . 'mdkkh-admin-page-menu.php';

require_once PLUGIN_DIR_PATH . 'mdkh-create-donation-form-shortcode.php';

require_once PLUGIN_DIR_PATH . 'mdkn-enquue-scripts.php';

