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
      * An associative array with a unique identifier as key
      */
     public $results_map = array();

     /**
      * List of post ids that matches urls returned by the remote search engine
      * @var array $matched_post_ids
      */
     public $matched_post_ids = array();
     
     /**
      *
      * @var string $skip_next_url
      */
     public $skip_next_url = null;
     
     /**
      *
      * @var string $skip_prev_url
      */
     public $skip_prev_url = null;

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
		 $wp_query->is_404 = (bool) 1;
                 return;
             }
             $this->set_matched_post_ids($this->results);
             // cache set
             $expire = $this->plugin->config['search_providers'][$this->router_name]['cache_expire'];
             if ($expire > 0) {
                 set_transient($this->transient, $this->results, $expire);
             }
             
             add_filter('the_posts', array($this, 'filter_smart_search_result_items'), 10);
             add_filter('the_posts', array($this, 'check_skip_next_url'), 11);
             add_filter('the_permalink', array($this, 'filter_smart_search_result_permalink'));
         }
         else {
             
         }
         do_action('smart_search_render');
     }
     
     /**
      * Fills $matched_post_ids to know which results are posts or pages
      * @param array $results MUST be an array of SmartSearchResultItem object
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
             // convert url with context_domain if needed
             if (!empty($this->context_domain)) {
                 $result->post_permalink = str_replace($this->context_domain, str_replace(array("http://", "https://"), "", site_url()), $result->post_permalink);
             }
             
             $post_id = get_post_id_from_url($result->post_permalink);
             if (!empty($post_id)) {
                 $this->matched_post_ids[$index] = $post_id;
             }
             
             // build an hashmap for later use
             $this->results_map[$result->hash] = $result;
             
             
         }
         
         global $wp_query;
         if (!empty($this->matched_post_ids)) {
             $wp_query->set('nopaging', true);
             $wp_query->set('post__in', $this->matched_post_ids);
         }
         
     }
     
     /**
      * Keep only those posts that match in the original query
      * Honor tax_qery and meta_query
      * @return array of WP_Post objects
      */
     public function filter_smart_search_result_items($posts)
     {
         global $wp_query;
         if (!$wp_query->is_main_query()) {
             return $posts;
         }
         
         $posts = wp_list_pluck( $posts, 'ID' );
         foreach ($this->matched_post_ids as $index => $post_id) {
             $key = array_search($post_id, $posts);
             if ($key === false) {
                 unset($this->matched_post_ids[$index]);
                 unset($this->results_map[$this->results[$index]->hash]);
                 unset($this->results[$index]);
             }
         }
         $wp_query->set('post__in', $this->matched_post_ids);
         
         $new_post_list = array();
         foreach ($this->results as $post) {	     
             $new_post_list[] = apply_filters('smart_search_add_result_item', $post, $this);	     
         }         
         $new_post_list = apply_filters('smart_search_return_new_post_list', $new_post_list, $this);
         $wp_query->found_posts = count($new_post_list);
         
         // pagination
         $new_post_list = $this->paginate_results($new_post_list);
         
         remove_filter('the_posts', array($this, 'filter_smart_search_result_items'));
         return $new_post_list;
     }

     /**
      * Return the permalink for resources coming from the remote search engine
      * which are not recorded with a valid $post->ID
      * @return string
      */
     public function filter_smart_search_result_permalink($url)
     {
         global $post, $wp_query;
         if(in_the_loop() && $wp_query->is_main_query() && !$post->ID) {
            return apply_filters('smart_search_result_item_permalink', $this->results_map[$post->hash]->post_permalink, $this);
         }
         else {
             remove_filter('the_permalink', array($this, 'filter_smart_search_result_permalink'), 10);
             return $url;
         }
     }
     
     /**
      * Since we've altered $wp_query->posts, pagination must be recalculated
      * @param array $new_post_list
      * @return array
      */
     protected function paginate_results($new_post_list)
     {
         global $wp_query;
         
         $page = $wp_query->get('paged');
         $per_page = $wp_query->get('posts_per_page');
         $wp_query->max_num_pages = round($wp_query->found_posts / $per_page, 0, PHP_ROUND_HALF_UP);
         $page = empty($page) ? 1 : $page;
         
         $paged_results = array_chunk($new_post_list, $per_page);         
         $paged_results = apply_filters('smart_search_paginate_results', $paged_results, $this);
         
         return $paged_results[$page-1];
     }
     
     public function check_skip_next_url($posts)
     {
         global $wp_query;
         $current_page = $wp_query->get('paged');
         
         if (!$wp_query->is_main_query() || !$current_page) {
             return $posts;
         }
         
         $should_check_next = $current_page == ($wp_query->max_num_pages - 1);
         if ($should_check_next && !empty($this->skip_next_url)) {
             $this->remote_search_url = $this->skip_next_url;
             $this->set_transient();
             
             if (false === ( $next_results = get_transient($this->transient) )) {
                 $next_results = $this->get_remote_results();
                 // prepare the queue to appended it to current result set retaining the order
                 $start = count($this->results);
                 $queue = array();
                 foreach ($next_results as $index => $result) {
                     $queue[$start+$index] = $result;
                 }
                 /*
                 //remove_filter('the_posts', array($this, 'filter_smart_search_result_items'), 10);
                 remove_filter('the_posts', array($this, 'check_skip_next_url'), 11);
                 //remove_filter('the_permalink', array($this, 'filter_smart_search_result_permalink'));
                 
                 
                 $this->set_matched_post_ids($queue);
                 /* cache set
                 $expire = $this->plugin->config['search_providers'][$this->router_name]['cache_expire'];
                 if ($expire > 0) {
                    set_transient($this->transient, $this->results, $expire);
                 }
                  
                 remove_action('pre_get_posts', array($this->plugin, 'route_request'));
                 $wp_query->set('nopaging', true);
                 $posts = $wp_query->get_posts();
                  * 
                  */
             }
             
         }
         
         //remove_filter('the_posts', array($this, 'check_skip_next_url'));
         return $posts;
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