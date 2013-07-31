<?php
/**
 *
 * @package   SmartSearch
 * @author    Christian Ronzio <cristian.ronzio@gmail.com>
 * @license   GPL-2.0+
 * @link      TODO
 * @copyright TODO
 *
 * @wordpress-plugin
 * Plugin Name: BING Search LITE
 * Plugin URI:  TODO
 * Description: Equip Wordpress with BING search engine
 * Version:     1.0.0
 * Author:      Christian Ronzio
 * Author URI:  TODO
 * Text Domain: smart-search-textdomain
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// include constants & helpers
require_once( plugin_dir_path( __FILE__ ) . '/inc/const.php' );
require_once( plugin_dir_path( __FILE__ ) . '/inc/helpers.php' );

// main class instance
require_once( CLASS_PATH . '/smartSearch.class.php' );
register_activation_hook(__FILE__, array('SmartSearch', 'activate'));
register_deactivation_hook(__FILE__, array('SmartSearch', 'deactivate'));

SmartSearch::get_instance();
