<?php
/**
 *
 * @package   SmartSearch
 * @author    Cristian Ronzio <cristian@contesio.com>
 * @license   GPL-2.0+
 * @link      http://www.contesio.com
 * @copyright www.contesio.com
 *
 * @wordpress-plugin
 * Plugin Name: Smart Search
 * Plugin URI:  http://www.contesio.com/wordpress-plugins/smart-search
 * Description: Replaces Wordpress search engine by giving relevance to what matters
 * Version:     0.9.3
 * Author:      Cristian Ronzio
 * Author URI:  http://www.contesio.com/about
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
