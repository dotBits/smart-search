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
	 global $wp_query;
	 
	 $this->max_result = get_option('posts_per_page');
	 
	 if (!empty( $this->search_string ))
         {
             $this->search_uri .= "&Query='" . urlencode(urldecode($this->search_string));
             $this->domain = 'site:' . $this->context_domain;
	     
             
             $skip = ($this->skip > 0) ? '&$skip=' . $this->skip : "";
             $this->search_uri .= urlencode(" $this->domain'") . $skip;
	     // highlighting @TODO check if needed
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
	     
	     $ch = curl_init();
	     curl_setopt($ch, CURLOPT_URL, $this->search_uri);
	     curl_setopt($ch, CURLOPT_HEADER, false);
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	     curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	     //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
	     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	     curl_setopt($ch, CURLOPT_USERPWD, $this->apikey . ":" . $this->apikey);

	     $json = curl_exec($ch);
	     curl_close($ch);

	     $this->response = json_decode($json);
	     
	     $results = array();
	     if(!empty($this->response)) {
		 $results = $this->response->d->results;
	     }     
	     
             $this->matched_post_ids = array();
	     // creates a reference for post-processing operations
	     $wp_query->set('smart_search_found_items', array());
	     // if context_domain overrides adjust match accordingly
	     $custom_domain = $search->config['search_providers'][$this->router_name]['context_domain'];
             foreach ($results as $index => $result) {
		 
		 if (!empty($custom_domain)) {
		     $post_url = str_replace($custom_domain, str_replace(array("http://", "https://"), "", site_url()), $result->Url);
		 }
		 else {
		     $post_url = $result->Url;
		 }
		 $post_url = urldecode($post_url);
                 $post_id = $this->search_post_id_from_url($post_url);
                 if ($post_id)
                 {
                     array_push( $this->matched_post_ids, $post_id );
		     // store matched result set as reference
		     $shared_results = $wp_query->get('smart_search_found_items');
		     $shared_results[$post_id] = $result;
		     $wp_query->set('smart_search_found_items', $shared_results);
                 }
		 else {
		     // no post_id that matches ... but nothing trashed
		     
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
		 
		 add_action('smart_search_post_altering', array($this, 'set_next_prev_skip'));	
		 add_action('smart_search_render', array($this, 'apply_render_options'));
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
		     // return an empty result set and let WP handle this
		     $wp_query->set('post__in', array(0));
		 }
		 
	     }
             
         }
     }
     
     public function apply_render_options()
     {	 
	 // get render options and use conditionals to apply them
	 $search = SmartSearch::get_instance();
	 $config = $search->get_config();
	 $ruoter_config = $config['search_providers'][$this->router_name];
	 
	 
	 // apply them by registering built-in filter
	 if($ruoter_config['highlight_title']) {
	    add_filter('the_title', array($this, 'highlight_title'), 10, 2);
	 }
	 
	 if($ruoter_config['highlight_excerpt']) {
	    add_filter('the_excerpt', array($this, 'highlight_excerpt'));
	 }
     }

     public function highlight_title($title, $id)
     {	 
	 if(!in_the_loop())
	     return $title;
	 
	 global $wp_query;
	 
	 $search = SmartSearch::get_instance();
	 $config = $search->get_config();
	 $ruoter_config = $config['search_providers'][$this->router_name];
	 
	 // Bing specific boundaries
	 $pattern_begin = "/\x{e000}/u";
	 $pattern_end = "/\x{e001}/u";
	 $pattern_full = "/\x{e000}(.*)\x{e001}/u";
	 // get title specific render options	 
	 $option_begin = '<span class="ss_hlights_title" style="background-color:' .$ruoter_config['highlight_title_color']. ';color:'.$ruoter_config['highlight_title_txt_color'].'"\'>';
	 $option_end = '</span>';
	 
	 $shared_results = $wp_query->get('smart_search_found_items');
	 
	 if ($config['search_providers'][$this->router_name]['use_remote_title']) { // @TODO always use pattern_full
	     // crawled title
	     $title = preg_replace($pattern_begin, $option_begin, $shared_results[$id]->Title);
	     $title = preg_replace($pattern_end, $option_end, $title);
	 }
	 else {
	     // use WP post_title
	     $remote_title = $shared_results[$id]->Title;
	     // get all highlightable words
	     preg_match_all($pattern_full, $remote_title, $matches);
	     if (!empty($matches[1])) {
		 foreach ($matches[1] as $word) {
		     $title = str_ireplace($word, $option_begin . $word . $option_end, $title);
		 }
	     }
	 }
	 
	 return $title;
     }
     
     public function highlight_excerpt($excerpt)
     {
	 if(!in_the_loop())
	     return $excerpt;
	 
	 global $wp_query;
	 
	 $search = SmartSearch::get_instance();
	 $config = $search->get_config();
	 $ruoter_config = $config['search_providers'][$this->router_name];
	 
	 // Bing specific boundaries
	 $pattern_begin = "/\x{e000}/u";
	 $pattern_end = "/\x{e001}/u";
	 $pattern_full = "/\x{e000}(.*)\x{e001}/u";
	 // get title specific render options
	 $option_begin = '<span class="ss_hlights_excerpt" style="background-color:' .$ruoter_config['highlight_excerpt_color']. ';color:'.$ruoter_config['highlight_excerpt_txt_color'].'"\'>';
	 $option_end = '</span>';
	 // needed since apply_filters doesn't pass the post_id
	 $id = get_the_ID();
	 
	 $shared_results = $wp_query->get('smart_search_found_items');
	 
	 if($config['search_providers'][$this->router_name]['use_remote_excerpt'])
	 {	     
	     // crawled excerpt
	     $excerpt = preg_replace($pattern_begin, $option_begin, $shared_results[$id]->Description);
	     $excerpt = preg_replace($pattern_end, $option_end, $excerpt);
	 }
	 else
	 {
	     // use WP post_excerpt
	     $remote_excerpt = $shared_results[$id]->Description;
	     // get all highlightable words
	     preg_match_all($pattern_full, $remote_excerpt, $matches);
	     if(!empty($matches[1])) 
	     {
		 foreach ($matches[1] as $word)
		 {		     
		     $excerpt = str_ireplace($word, $option_begin.$word.$option_end, $excerpt);
		 }
	     }
	 }
	 
	 return $excerpt;
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
             //$this->handle_skip();
             remove_filter('posts_results', array($this, 'hook_posts_results'));
         }
         return $posts;
     }
     
     // unused
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
		 $next_results_match = array_merge( $post_in, $next_results );
                 $wp_query->set( 'post__in', array_merge( $post_in, $next_results_match ) );
                 $wp_query->set('skipped_on_page', $current_page);		 
                 // #cache_set to update with appended skip results
                 set_transient( $this->transient, $wp_query, $expiration );
             }
         }
     }
     
     // useless
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
	 global $wp_query;
	 
         $string = $this->get_router_name()
             . '_s=' . $this->search_string
             . '_d=' .  $this->domain
             . '_n=' . $this->max_result
	     . '_p=' . $wp_query->get('paged')
             . '_sk=' . $this->skip;

         return $string;
     }
     
 }

 