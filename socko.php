<?php
/**
 * Plugin Name: Socko
 * Plugin URI: https://www.n-finity.org
 * Description: Trick Facebook and Skype by sharing an image that's basically a redirect to your specified page.
 * Version: 1.0.0
 * Author: n-finity
 * Author URI: http://www.n-finity.org
 */

/** Define global plugin variables */
define( 'SOCKO_PATH'               ,  dirname(__FILE__));
define( 'SOCKO_SLUG'               , 'socko/socko.php');
define( 'SOCKO_URL'                ,  plugin_dir_url( __FILE__ ));

/** Include the main class */
require_once ( SOCKO_PATH.'/init.php' );

/** Start the plugin */
SOCKO_Init::init();