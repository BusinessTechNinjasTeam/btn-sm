<?php
/*
Plugin Name: BTN Subscription Management
Plugin URI: https://businesstechninjas.com/
Description: Subscription Management
Version: 1.0.0
Author: Business Tech Ninjas
Author URI: https://businesstechninjas.com/
License: Copyright (c) Business Tech Ninjas
Text Domain: btn-sm
*/

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
	header('HTTP/1.0 403 Forbidden');
	die();
}

define('BTN_SM_VERSION', '1.14');
define('BTN_SM_PLUGIN', __FILE__);
define('BTN_SM_DIR', __DIR__ . '/');
define('BTN_SM_CLASS_DIR', BTN_SM_DIR . 'classes/');
define('BTN_SM_TMPL_DIR', BTN_SM_DIR . 'templates/');
$btn_default_url = plugins_url('', __FILE__);
define('BTN_SM_URL', $btn_default_url . '/');
define('BTN_SM_ASSETS_URL', BTN_SM_URL . 'assets/');


// Include Autoloader
include_once BTN_SM_CLASS_DIR . 'autoloader.php';
// Include the ACF plugin.



// Init Plugin
add_action('plugins_loaded',function(){
	btn_sm()->init();
}, 1 );

// Gets the instance of the `btn_default` class
function btn_sm(){
    return btn_sm::get_instance();
}