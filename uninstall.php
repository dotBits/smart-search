<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Your Name or Company Name
 */

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// TODO: remove old transients
// _transient_SSearch%
// _transient_timeout_SSearch%
// _transient_bing-search-options