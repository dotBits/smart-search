<?php

 require_once ROUTERS_PATH . '/searchRouter.abst.php';

 class BingSearchRouterImpl extends SearchRouter
 {
     protected $domain = null;
     
     protected $max_result = 50;
     
     protected $skip = 0;
     
     private $apikey = null;
     
     function __construct()
     {
         parent::__construct();         
         // prepare for search
         $search = SmartSearch::get_instance();
         $this->apikey = $search->config['search_providers'][$this->router_name]['API_KEY'];
         $this->init();
         
         // straight search
         $this->search();
     }

     protected function parse_custom_query()
     {
         global $wp_query;
         $this->set_search_string( $wp_query->get( 'search_query' ) );
     }

     protected function set_search_uri()
     {
         if (!empty( $this->search_string ))
         {                          
             $this->search_uri.= "&Query='" . urlencode(urldecode($this->search_string));
             //$domain = 'site:' . get_bloginfo( 'wpurl' );
             $this->domain = 'site:http://bio.tuttogreen.it ';
             $n_results = '&$top='.$this->max_result;
             $skip = ($this->skip > 0) ? '&$skip='.$this->skip : "";
             $this->search_uri.= urlencode( " $this->domain'" ) . $n_results . $skip;
             
             return true;
         }
         else
         {
             return false;
         }
     }     

     /**
      * 
      * @global type $wp_query
      * @return array
      */
     protected function set_matched_post_ids()
     {
         global $wp_query;
         
         // apikey is required to make a search
         if (!empty( $this->apikey ))
         {
             // Encode the credentials and create the stream context.         
             $auth = base64_encode( $this->apikey . ':' . $this->apikey );
             $data = array(
                     'http' => array(
                             'request_fulluri' => true,
                             // ignore_errors can help debug – remove for production. This option added in PHP 5.2.10
                             'ignore_errors' => true,
                             'header' => "Authorization: Basic $auth")
             );
             $context = stream_context_create( $data );
             // Get the response from Bing
             $this->response = json_decode( file_get_contents( $this->search_uri, 0, $context ) );
             $results = (!empty( $this->response )) ? $this->response->d->results : array();

             $this->matched_post_ids = array();
             foreach ($results as $result) {
                 $post_id = url_to_postid( str_replace( 'bio.tuttogreen.it', 'localhost/wp-env', $result->Url ) );
                 if ($post_id > 0)
                 {
                     array_push( $this->matched_post_ids, $post_id );
                 }
             }
             return array_unique( $this->matched_post_ids );
         }
         
     }
     
     protected function alter_main_query()
     {
         global $wp_query;
         if (empty( $this->apikey ))
         {
              $wp_query->is_404 = (bool) 1;
              wp_enqueue_script( 'smart-search-no-apikey-script', PLUGIN_URL . '/js/no-apikey.js', array('jquery'), '1.0',true);              
         }
         else
         {
             $wp_query->is_search = (bool) 1;
             $wp_query->set( 'post__in', $this->matched_post_ids );
             $wp_query->set( 'orderby', 'post__in' );

             // Store next / prev if they are present
             add_action( 'smart_search_post_altering', array($this, 'set_next_prev_skip') );
         }
     }
     
     /**
      * 
      * @param type $found_posts
      * @param type $query
      * @return type
      */
     public function adjust_offset_pagination($found_posts, $query)
     {         
         $query->set('real_found_posts', $found_posts);
         if ($query->is_main_query() && true === is_penultimate_page( $query ))
         {             
             remove_filter('found_posts', array($this, 'adjust_offset_pagination'));
         }
         return $found_posts;
     }
     
     public function hook_posts_results($posts, $query)
     {
         if($query->is_main_query() && true === is_penultimate_page( $query ))
         {
             $this->handle_skip();
             remove_filter('posts_results', array($this, 'hook_posts_results'));
         }
         return $posts;
     }
     
     protected function handle_skip()
     {
         global $wp_query;
         $search = SmartSearch::get_instance();
         $expiration = $search->config['search_providers'][$this->router_name]['cache_expire'];
         $this->search_uri = $search->config['search_providers'][$this->router_name]['base_uri'];
         
         // if is the first or last page and can skip forward or backward
         $post_in = $wp_query->get( 'post__in' );
         $next = $wp_query->get( 'skip_next_url' );
         $current_page = $wp_query->get('paged');
         $skipped_on_page = $wp_query->get('skipped_on_page');
         
         if (!empty( $next ) && $current_page != $skipped_on_page)
         {
             $url = parse_url( $next );
             parse_str( $url['query'], $args );

             $this->skip = intval( $args['$skip'] );
             $this->set_search_uri();
             $next_results = $this->set_matched_post_ids();
             if (!empty( $next_results ))
             {
                 $wp_query->set( 'post__in', array_merge( $post_in, $next_results ) );
                 $wp_query->set('skipped_on_page', $current_page);
                 // #cache_set to update with appended skip results
                 set_transient( $this->transient, $wp_query, $expiration );
             }
         }
     }
     
     public function set_next_prev_skip($response)
     {
         if(!empty($response->d->results) && isset($response->d->__next))
         {
             global $wp_query;
             
             if (!empty( $response->d->__next ))
                 $wp_query->set( 'skip_next_url', $response->d->__next );
         }
     }
     
     /**
      * routerName+searchString+domain+maxResultReturned+apiCallOffset
      * @return string cache_key used for transient
      */
     protected function set_transient()
     {
         $string = $this->get_router_name()
             . $this->search_string
             . $this->domain
             . $this->max_result
             . $this->skip;

         return $string;
     }
     
 }

 