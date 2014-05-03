<?php

 require_once ROUTERS_PATH . '/searchRouter.abst.php';

 class BingSearchRouterImpl extends SearchRouter
 {
     protected $domain = null;
     
     protected $max_result = 50;
     
     protected $skip = 0;
     
     private $apikey = null;
     
     // class visibility for result set cursor
     private $items_found = array();
     
     function __construct()
     {
         parent::__construct();         
         // prepare for search
         $search = SmartSearch::get_instance();
         $this->apikey = $search->config['search_providers'][$this->router_name]['API_KEY'];
         $this->init();
	 
	 add_action('smart_search_post_altering', array($this, 'apply_render_options'));
         
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
             $this->search_uri .= "&Query='" . urlencode(urldecode($this->search_string));
             $this->domain = 'site:' . $this->context_domain;
             $n_results = '&$top=' . $this->max_result;
             $skip = ($this->skip > 0) ? '&$skip=' . $this->skip : "";
             $this->search_uri .= urlencode(" $this->domain'") . $n_results . $skip;
	     // highlighting
	     $this->search_uri .= "&Options='EnableHighlighting'";
             
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
	     $search = SmartSearch::get_instance();
             // Encode the credentials and create the stream context.
             $auth = base64_encode( $this->apikey . ':' . $this->apikey );
             $data = array(
                     'http' => array(
                             'request_fulluri' => true,
                             // ignore_errors can help debug – remove for production. This option added in PHP 5.2.10
                             'ignore_errors' => false,
                             'header' => "Authorization: Basic $auth")
             );
             $context = stream_context_create( $data );
             // Get the response from Bing
             $this->response = json_decode( file_get_contents( $this->search_uri, 0, $context ) );
             $results = (!empty( $this->response )) ? $this->response->d->results : array();
	     
             $this->matched_post_ids = array();
	     // if context_domain overrides adjust match accordingly
	     $custom_domain = $search->config['search_providers'][$this->router_name]['context_domain'];
             foreach ($results as $result) {
		 
		 if (!empty($custom_domain)) {
		     $post_url = str_replace($custom_domain, str_replace(array("http://", "https://"), "", site_url()), $result->Url);
		 }
		 else {
		     $post_url = $result->Url;
		 }
                 $post_id = $this->search_post_id_from_url($post_url);
                 if ($post_id > 0)
                 {
                     array_push( $this->matched_post_ids, $post_id );
		     $this->items_found[$post_id] = $result;
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
              wp_enqueue_script( 'smart-search-no-apikey-script', PLUGIN_URL . '/js/no-apikey.js', array('jquery'), '1.0', true);
         }
         else
         {
	     $wp_query->is_search = (bool) 1;
	     if (!empty($this->matched_post_ids)) {		 
		 $wp_query->set('post__in', $this->matched_post_ids);
		 $wp_query->set('orderby', 'post__in');
		 
		 // Store next / prev if they are present
		 add_action('smart_search_post_altering', array($this, 'set_next_prev_skip'));		 
	     }
	     else {
		 // no results handler
		 $search = SmartSearch::get_instance();
		 $no_results_url = $search->config['search_providers'][$this->router_name]['no_results_url'];
		 if(!empty($no_results_url)) {
		     if(preg_match("/\?.*$/", $no_results_url)) {
			 wp_redirect($no_results_url . '&query=' . $this->get_search_string());
		     }
		     else {
			 wp_redirect($no_results_url . '?query=' . $this->get_search_string());
		     }		     
		     exit;
		 }
		 else {
		     $wp_query->set('post__in', array(0));
		 }
		 
	     }
             
         }
     }
     
     public function apply_render_options($response)
     {
	 // get render options and use conditionals to apply them
	 
	 // apply them by registering built-in filter
	 add_filter('the_title', array($this, 'highlight_title'), 10, 2);
     }
     
     public function highlight_title($title, $id)
     {
	 // @TODO wrap in a get_highlights_options()
	 $search = SmartSearch::get_instance();
	 $config = $search->get_config();
	 // Bing specific boundaries
	 $pattern_begin = "/\x{e000}/u";
	 $pattern_end = "/\x{e001}/u";
	 // get title specific render options
	 $option_begin = '<span style="background-color:yellow">';
	 $option_end = '</span>';
	 
	 $config['search_providers'][$this->router_name]['use_remote_title'] = true;
	 if($config['search_providers'][$this->router_name]['use_remote_title'] == true)
	 {
	     // crawled title
	     $title = preg_replace($pattern_begin, $option_begin, $this->items_found[$id]->Title);
	     $title = preg_replace($pattern_end, $option_end, $title);
	 }
	 else
	 {
	     // use WP post_title
	     
	 }
	 // highlight if needed
	 
	 
	 
	 //preg_match_all($pattern_full, $this->found_item->Title, $matches);
	 /*
	 $output = "";
	 $output = preg_replace($pattern_begin, "{{", $this->found_item->Title);
	 $output = preg_replace($pattern_end, "}}", $output);
	 
	 preg_match_all("/{{(.*)}}/", $output, $matches);
	 
	 if(!empty($matches[0])) {
	     $title = str_ireplace($matches[1][0], $option_begin . $matches[1][0] . $option_end, $title);
	     //$title = preg_replace("/".$matches[1][0]."/", $option_begin . $matches[1][0] . $option_end, $title);
	 }
	 else {
	     $title = $title;
	 }
	  * 
	  */
	 
	 return $title;
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

 