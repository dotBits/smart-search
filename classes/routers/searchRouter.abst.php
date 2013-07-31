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
      * expected an md5 hash of 'routername_searchstring_offset'
      * @var string
      */
     protected $transient = null;

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
         // since it differs through different engines
         //$this->set_search_type();
         // build endpoint uri with params
         $this->set_search_uri();
         #cache_set (hashkey)
         $this->transient = 'SSearch' . md5( $this->set_transient() );
     }

     /**
      * Start the flow in one shot: concrete implement the relevant logic
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
         set_transient( $this->transient, $wp_query, $expiration );
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

     /**
      * Here's where the magic happens
      * WP_Query is altered as needed by concrete implementations
      */
     abstract protected function alter_main_query();
     
     /**
      * Since each router can define different query params
      * a concrete implementation is required to set cache key
      */
     abstract protected function set_transient();
 
 }