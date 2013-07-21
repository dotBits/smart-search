<?php

 /**
  * Define common properties, methods and a standard flow for any SearchRouterImpl
  */
 abstract class SearchRouter
 {
     // WORDPRESS FILTER PARAMS //

     /**
      * Reference to main_query passed to constructor (in loop)
      * @var WP_Query $query
      * @link http://codex.wordpress.org/Class_Reference/WP_Query
      */
     protected $query;

     /**
      * Authors filter
      * @var mixed 
      * @link http://codex.wordpress.org/Class_Reference/WP_Query#Author_Parameters
      */
     private $authors = null;

     /**
      * Categories filter
      * @var mixed 
      * @link http://codex.wordpress.org/Class_Reference/WP_Query#Category_Parameters
      */
     private $categories = null;

     /**
      * Tags filter
      * @var mixed 
      * @link http://codex.wordpress.org/Class_Reference/WP_Query#Tag_Parameters
      */
     private $tags = null;

     /**
      * Taxonomy filter
      * @var array 
      * @link http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters
      */
     private $taxonomy = array();

     /**
      * Post type filter
      * @var mixed 
      * @link http://codex.wordpress.org/Class_Reference/WP_Query#Type_Parameters
      */
     private $post_type = null;

     /**
      * DateTime parameter
      * @var mixed 
      * @link http://codex.wordpress.org/Class_Reference/WP_Query#Time_Parameters
      */
     private $date_time = null;

     /**
      * Order parameter
      * @var array 
      * @link http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters
      */
     private $meta_query = array();

     /**
      * Order parameter
      * @var string 
      * @ignore
      */
     private $order = null;

     /**
      * Orderby parameter
      * @var string 
      * @ignore
      */
     private $orderby = null;

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
      * Type of contents to query
      * @var string
      */
     private $search_type = null;
     
     /**
      * The search string being processed
      */
     protected $search_string = null;

     /**
      * Constructor
      * @param WP_Query $wp_query
      */
     function __construct(WP_Query $wp_query)
     {
         $this->set_query($wp_query); // main_query reference
     }

     // STANDARD GETTERS AND SETTERS //

     public function get_query()
     {
         return $this->query;
     }

     private function set_query(WP_Query $query)
     {
         $this->query = $query;
     }

     public function get_authors()
     {
         return $this->authors;
     }

     public function set_authors($authors)
     {
         $this->authors = $authors;
     }

     public function get_categories()
     {
         return $this->categories;
     }

     public function set_categories($categories)
     {
         $this->categories = $categories;
     }

     public function get_tags()
     {
         return $this->tags;
     }

     public function set_tags($tags)
     {
         $this->tags = $tags;
     }

     public function get_taxonomy()
     {
         return $this->taxonomy;
     }

     public function set_taxonomy(array $taxonomy)
     {
         $this->taxonomy = $taxonomy;
     }

     public function get_post_type()
     {
         return $this->post_type;
     }

     public function set_post_type($post_type)
     {
         $this->post_type = $post_type;
     }

     public function get_date_time()
     {
         return $this->date_time;
     }

     public function set_date_time($date_time)
     {
         $this->date_time = $date_time;
     }

     public function get_meta_query()
     {
         return $this->meta_query;
     }

     public function set_meta_query(array $meta_query)
     {
         $this->meta_query = $meta_query;
     }

     public function get_order()
     {
         return $this->order;
     }

     public function set_order($order)
     {
         $this->order = $order;
     }

     public function get_orderby()
     {
         return $this->orderby;
     }

     public function set_orderby($orderby)
     {
         $this->orderby = $orderby;
     }

     // CORE METHODS //
     
     protected function init()
     {
         $search = SmartSearch::get_instance();
         $this->search_uri = $search->config['search_providers'][$this->query->get('search_router')]['base_uri'];
         
         // what's being requested by WP
         $this->parse_custom_query();
         // since it differs through different engines
         //$this->set_search_type();
         // build endpoint uri with params
         $this->set_search_uri();
     }

     /**
      * Start the flow in one shot: concrete implement the relevant logic
      */
     protected function search()
     {
         // WP post_ids that match search engine results 
         $this->set_matched_post_ids();
         // internal filters and WP_Query modifiers
         $this->alter_main_query(); // inject
     }

     /**
      * Read query args using a standard for each Router 
      */
     abstract protected function parse_custom_query();

     /**
      * Search for Web, Images, Videos etc.
      */
     protected function set_search_type()
     {
         
     }

     public function get_search_type()
     {
         return $this->search_type;
     }
     
     protected function set_search_string($string)
     {
         $this->query->set('s', $string);
         $this->search_string = $string;
     }
     
     public function get_search_string()
     {
         return $this->search_string;
     }

     /**
      * Where to build API Endopoint with params
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
     
 }