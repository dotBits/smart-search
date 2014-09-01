<?php
/**
 *
 * @package   SmartSearch
 * @author    Cristian Ronzio <cristian@contesio.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/smart-search
 * @copyright www.contesio.com
 *
 * @wordpress-plugin
 * Plugin Name: Smart Search
 * Plugin URI:  http://wordpress.org/plugins/smart-search
 * Description: Replaces Wordpress search engine by giving relevance to what matters
 * Version:     0.9.4
 * Author:      Cristian Ronzio
 * Author URI:  http://profiles.wordpress.org/contesio/
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


function get_post_id_from_url($url)
 {
     global $wp_rewrite, $wp_query;

     $url = apply_filters('url_to_postid', $url);

     // First, check to see if there is a 'p=N' or 'page_id=N' to match against
     if (preg_match('#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values)) {
         $id = absint($values[2]);
         if ($id)
             return $id;
     }

     // Check to see if we are using rewrite rules
     $rewrite = $wp_rewrite->wp_rewrite_rules();

     // Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
     if (empty($rewrite))
         return 0;

     // Get rid of the #anchor
     $url_split = explode('#', $url);
     $url = $url_split[0];

     // Get rid of URL ?query=string
     $url_split = explode('?', $url);
     $url = $url_split[0];

     // Add 'www.' if it is absent and should be there
     if (false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.'))
         $url = str_replace('://', '://www.', $url);

     // Strip 'www.' if it is present and shouldn't be
     if (false === strpos(home_url(), '://www.'))
         $url = str_replace('://www.', '://', $url);

     // Strip 'index.php/' if we're not using path info permalinks
     if (!$wp_rewrite->using_index_permalinks())
         $url = str_replace('index.php/', '', $url);

     if (false !== strpos($url, home_url())) {
         // Chop off http://domain.com
         $url = str_replace(home_url(), '', $url);
     }
     else {
         // Chop off /path/to/blog
         $home_path = parse_url(home_url());
         $home_path = isset($home_path['path']) ? $home_path['path'] : '';
         $url = str_replace($home_path, '', $url);
     }

     // Trim leading and lagging slashes
     $url = trim($url, '/');

     $request = $url;
     foreach (get_post_types(array(), 'objects') as $post_type => $t) {
         if (!empty($t->query_var)) {
             $post_type_query_vars[$t->query_var] = $post_type;
         }
     }

     // Look for matches.
     $request_match = $request;
     foreach ((array) $rewrite as $match => $query) {

         // If the requesting file is the anchor of the match, prepend it
         // to the path info.
         if (!empty($url) && ($url != $request) && (strpos($match, $url) === 0))
             $request_match = $url . '/' . $request;

         if (preg_match("!^$match!", $request_match, $matches)) {

             if ($wp_rewrite->use_verbose_page_rules && preg_match('/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch)) {
                 // this is a verbose page match, lets check to be sure about it
                 if (!get_page_by_path($matches[$varmatch[1]]))
                     continue;
             }

             // Got a match.
             // Trim the query of everything up to the '?'.
             $query = preg_replace("!^.+\?!", '', $query);

             // Substitute the substring matches into the query.
             $query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

             // Filter out non-public query vars
             global $wp;
             parse_str($query, $query_vars);
             $query = array();
             foreach ((array) $query_vars as $key => $value) {
                 if (in_array($key, $wp->public_query_vars)) {
                     $query[$key] = $value;
                     if (isset($post_type_query_vars[$key])) {
                         $query['post_type'] = $post_type_query_vars[$key];
                         $query['name'] = $value;
                     }
                 }
             }

             // Do the query
             $query = new WP_Query($query);
             if (!empty($query->posts) && $query->is_singular) {

                 if (empty($wp_query->tax_query->queries)) {
                     return $query->post->ID;
                 }
                 if (isset($wp_query->tax_query->relation) && strtoupper($wp_query->tax_query->relation) == 'OR') {
                     $relation = 'OR';
                 }
                 else {
                     $relation = 'AND';
                 }
                 // check for taxonomy matches @TODO tax_query operator should be checked as well
                 $got_terms = false;
                 foreach ($wp_query->tax_query->queries as $tax_query) {
                     $got_terms = has_term($tax_query['terms'], $tax_query['taxonomy'], $query->post->ID);
                     if ($relation == 'OR' && $got_terms) {
                         break;
                     }
                     elseif ($relation == 'AND' && !$got_terms) {
                         return 0;
                     }
                 }
                 if ($got_terms) {
                    return $query->post->ID;
                 } 
                 else {
                     return 0;
                 }
             }
             else
                 return 0;
         }
     }
     return 0;
 }