<?php

 /**
  * Define common properties, behaviors and a standard flow for any SearchRouterImpl
  */
 abstract class SearchRouter
 {
     /**
      * The router's name being used
      * @var string $router_name
      */
     protected $router_name = null;
     
     /**
      * The string being searched for
      * @var string $search_query
      */
     public $search_query = null;
     
     /**
      * Reference to SmartSearch instance
      * @var SmartSearch $plugin 
      */
     public $plugin = null;
     
     /**
      * API endpoint
      * @var string
      */
     protected $remote_search_url = null;
     
     /**
      * Domain context used in searches
      * @var string $context_domain
      */
     protected $context_domain = null;
     
     /**
      * Cache key
      * @var string
      */
     protected $transient = null;
     
     /**
      * Remote search engine matching items
      */
     public $results = array();
     
     /**
      * List of post ids that matches urls returned by the remote search engine
      * @var array $matched_post_ids
      */
     public $matched_post_ids = array();


     public function __construct($search_query = "")
     {
         global $wp_query;
         $this->router_name = $wp_query->get('search_router');
         $this->search_query = $search_query;
         $this->plugin = SmartSearch::get_instance();
         $this->remote_search_url = $this->plugin->config['search_providers'][$this->router_name]['base_uri'];
         $this->set_context_domain();
         $this->transient = 'SSearch' . md5( $this->set_transient() );
         
         $this->init();
         if(!empty($search_query)) {
             $this->search();
         }
     }
     
     protected function set_context_domain()
     {
         $this->context_domain = $this->plugin->config['search_providers'][$this->router_name]['context_domain'];
	 if(empty($this->context_domain)) {
	     $this->context_domain = site_url();
	 }
     }
     
     /**
      * Start flow, relevant logic implemented by concretes
      */
     final protected function search()
     {
         global $wp_query;
         
         if (false === ( $this->results = get_transient($this->transient) )) {             
             $this->results = $this->get_remote_results();
             if (empty($this->results)) {
                 return;
             }
             $this->set_matched_post_ids($this->results);
             
             // cache set
             
         }
         else {
             //$this->get_cached_results( $results );
         }
         //add_filter( 'found_posts', array($this, 'adjust_offset_pagination'), 1, 2 );	 
         do_action('smart_search_render');
     }
     
     protected function get_cached_results(WP_Query $cached_query)
     {
         
     }
     
     /**
      * Fills the two set of results $matched_post_ids and $unmatched_post_ids
      * Each result in $results array MUST be normalized to a SmartSearchResultItem object
      * @param array $results
      */
     final protected function set_matched_post_ids(array $results)
     {
         if (empty($results)) {
             return 0;
         }
         foreach ($results as $index => $result) {
             if (!$result instanceof SmartSearchResultItem) {
                 $e = new Exception("\$result at index $index must be an instance of SmartSearchResultItem");
                 echo $e->getMessage() . '<br>' . $e->getFile() . ' at line ' . $e->getLine();
                 die();
             }
             $post_id = get_post_id_from_url($result->url);
             if (!empty($post_id)) {
                 $this->matched_post_ids[] = $post_id;
             }
         }
         
         global $wp_query;
         if (!empty($this->matched_post_ids)) {
             $wp_query->set('post__in', $this->matched_post_ids);
         }
     }

     /**
      * Cache key setter
      */
     abstract protected function set_transient();
     
     /**
      * Init function that runs just before search
      */
     abstract protected function init();
     
     /**
      * API call to the search engine service
      * must return an array of SmartSearchResultItem objects
      */
     abstract protected function get_remote_results();

     public function get_router_name()
     {
         return $this->router_name;
     }
     
     public function get_remote_search_url()
     {
         return $this->remote_search_url;
     }
     
     public function get_context_domain()
     {
         return $this->context_domain;
     }
 
 }