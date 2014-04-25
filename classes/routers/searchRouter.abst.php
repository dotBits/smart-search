<?php

 /**
  * Define common properties, methods and a standard flow for any SearchRouterImpl
  */
 abstract class SearchRouter
 {
     
     protected $router_name = null;

     /**
      * Matched WP => Search Engine results
      * @var array
      */
     protected $matched_post_ids = array();

     /**
      * API Endpoint URI
      * @var string
      */
     protected $search_uri = null;
     
     /**
      * The search string being processed
      */
     protected $search_string = null;
     
     /**
      * Search Engine response
      * @var mixed
      */
     protected $response = null;
     
     /**
      * Formally the cache key for a specific query
      * @var string
      */
     protected $transient = null;
     
     protected $context_domain = null;

     /**
      * Constructor
      * @param WP_Query $wp_query
      */
     function __construct()
     {
         global $wp_query;
         $this->router_name = $wp_query->get('search_router');
     }

     // STANDARD GETTERS AND SETTERS //

     public function get_router_name()
     {
         return $this->router_name;
     }

     // CORE METHODS //
     
     protected function init()
     {
         $search = SmartSearch::get_instance();
         $this->search_uri = $search->config['search_providers'][$this->router_name]['base_uri'];
         
         // what's being requested by WP
         $this->parse_custom_query();
	 // define context, can be overriden by config
	 $this->context_domain = $search->config['search_providers'][$this->router_name]['context_domain'];
	 if(empty($this->context_domain)) {
	     $this->context_domain = site_url();
	 }
         // build endpoint uri with params
         $this->set_search_uri();
         #cache_set (hashkey)
         $this->transient = 'SSearch' . md5( $this->set_transient() );
     }

     /**
      * Start the flow in one shot: relevant logic implemented by concretes
      */
     protected function search()
     {
         //delete_transient( $this->transient );
         if (false === ( $query_results = get_transient( $this->transient ) ))
         {
             $this->get_remote_results();
         }
         else
         {
             $this->get_cached_results( $query_results );
         }
         add_filter( 'found_posts', array($this, 'adjust_offset_pagination'), 1, 2 );
         add_filter( 'posts_results', array($this, 'hook_posts_results'), 1, 2);
     }     
     
     abstract protected function adjust_offset_pagination($found_posts, $query);
     abstract protected function hook_posts_results($posts, $query);
     abstract protected function handle_skip();


     private function get_remote_results()
     {   
         $search = SmartSearch::get_instance();
         $expiration = $search->config['search_providers'][$this->router_name]['cache_expire'];
         
         $this->matched_post_ids = $this->set_matched_post_ids();
         $this->alter_main_query(); // inject
         // $this->response MUST be set by set_matched_post_ids()
         do_action( 'smart_search_post_altering', $this->response );

         global $wp_query;
         // #cache_set
         if($expiration > 0) {
             set_transient( $this->transient, $wp_query, $expiration );
         }
     }
     
     private function get_cached_results(WP_Query $cached_query)
     {
         global $wp_query;
         
         $wp_query->is_search = (bool) 1;
         $wp_query->set( 'post__in', $cached_query->get( 'post__in' ) );
         $wp_query->set( 'orderby', $cached_query->get( 'orderby' ) );
         $wp_query->set( 'skipped_on_page', $cached_query->get( 'skipped_on_page' ) );

         $next = $cached_query->get( 'skip_next_url' );
         if (!empty( $next ))
             $wp_query->set( 'skip_next_url', $next );
     }

     /**
      * Read query args using a standard for each Router 
      */
     abstract protected function parse_custom_query();
     
     protected function set_search_string($string)
     {
         global $wp_query;
         $wp_query->set('s', $string);
         $this->search_string = $string;
     }
     
     public function get_search_string()
     {
         return $this->search_string;
     }

     /**
      * Where to build API Endopoint with full set of params
      */
     abstract protected function set_search_uri();

     public function get_search_uri()
     {
         return $this->search_uri;
     }

     /**
      * Post ids that match with search engine results
      */
     abstract protected function set_matched_post_ids();

     public function get_matched_post_ids()
     {
         return $this->matched_post_ids;
     }
     
     protected function search_post_id_from_url($url)
     {
         global $wp_rewrite;

         $url = apply_filters( 'url_to_postid', $url );

         // First, check to see if there is a 'p=N' or 'page_id=N' to match against
         if (preg_match( '#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values ))
         {
             $id = absint( $values[2] );
             if ($id)
                 return $id;
         }

         // Check to see if we are using rewrite rules
         $rewrite = $wp_rewrite->wp_rewrite_rules();

         // Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
         if (empty( $rewrite ))
             return 0;

         // Get rid of the #anchor
         $url_split = explode( '#', $url );
         $url = $url_split[0];

         // Get rid of URL ?query=string
         $url_split = explode( '?', $url );
         $url = $url_split[0];

         // Add 'www.' if it is absent and should be there
         if (false !== strpos( home_url(), '://www.' ) && false === strpos( $url, '://www.' ))
             $url = str_replace( '://', '://www.', $url );

         // Strip 'www.' if it is present and shouldn't be
         if (false === strpos( home_url(), '://www.' ))
             $url = str_replace( '://www.', '://', $url );

         // Strip 'index.php/' if we're not using path info permalinks
         if (!$wp_rewrite->using_index_permalinks())
             $url = str_replace( 'index.php/', '', $url );

         if (false !== strpos( $url, home_url() ))
         {
             // Chop off http://domain.com
             $url = str_replace( home_url(), '', $url );
         }
         else
         {
             // Chop off /path/to/blog
             $home_path = parse_url( home_url() );
             $home_path = isset( $home_path['path'] ) ? $home_path['path'] : '';
             $url = str_replace( $home_path, '', $url );
         }

         // Trim leading and lagging slashes
         $url = trim( $url, '/' );

         $request = $url;
         foreach (get_post_types( array(), 'objects' ) as $post_type => $t) {
             if (!empty( $t->query_var ))
             {
                 $post_type_query_vars[$t->query_var] = $post_type;
             }
         }

         // Look for matches.
         $request_match = $request;
         foreach ((array) $rewrite as $match => $query) {

             // If the requesting file is the anchor of the match, prepend it
             // to the path info.
             if (!empty( $url ) && ($url != $request) && (strpos( $match, $url ) === 0))
                 $request_match = $url . '/' . $request;

             if (preg_match( "!^$match!", $request_match, $matches ))
             {

                 if ($wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ))
                 {
                     // this is a verbose page match, lets check to be sure about it
                     if (!get_page_by_path( $matches[$varmatch[1]] ))
                         continue;
                 }

                 // Got a match.
                 // Trim the query of everything up to the '?'.
                 $query = preg_replace( "!^.+\?!", '', $query );

                 // Substitute the substring matches into the query.
                 $query = addslashes( WP_MatchesMapRegex::apply( $query, $matches ) );

                 // Filter out non-public query vars
                 global $wp;
                 parse_str( $query, $query_vars );
                 $query = array();
                 foreach ((array) $query_vars as $key => $value) {
                     if (in_array( $key, $wp->public_query_vars ))
                     {
                         $query[$key] = $value;
                         if (isset( $post_type_query_vars[$key] ))
                         {
                             $query['post_type'] = $post_type_query_vars[$key];
                             $query['name'] = $value;
                         }
                     }
                 }

                 // Do the query
                 $query = new WP_Query( $query );
                 if (!empty( $query->posts ) && $query->is_singular)
                     return $query->post->ID;
                 else
                     return 0;
             }
         }
         return 0;
     }

     /**
      * Here's where the magic happens
      * WP_Query is altered as needed by concrete implementations
      */
     abstract protected function alter_main_query();
     
     /**
      * Since each router can define different query params
      * a concrete implementation is required to define cache key
      */
     abstract protected function set_transient();
 
 }