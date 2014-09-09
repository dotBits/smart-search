<?php

 require_once ROUTERS_PATH . '/searchRouter.abst.php';
 require_once CLASS_PATH . '/SmartSearchResultItem.class.php';

 class BingSearchRouterImpl extends SearchRouter
 {
     /**
      * BING Search API key
      * @var string $apikey
      */
     private $apikey = null;
     
     private $skip_next_url = null;
     
     public function __construct($search_query = "")
     {
         parent::__construct($search_query);
     }
     
     protected function set_transient()
     {
         // @TODO add taxonomy parameters
         global $wp_query;
         $string = $this->get_router_name()
             . '_s=' . $this->search_query
             . '_d=' . $this->context_domain
             . '_n=' . $wp_query->get('posts_per_page', get_option('posts_per_page'))
	     . '_p=' . $wp_query->get('paged')
             . '_sk='. $this->skip_next_url;

         return $string;
     }
     
     protected function init()
     {
         $router = $this->get_router_name();
         $this->apikey = $this->plugin->config['search_providers'][$router]['API_KEY'];
         //$this->set_remote_search_url_params();
     }

     protected function set_remote_search_url()
     {
         $this->remote_search_url = $this->plugin->config['search_providers'][$this->router_name]['base_uri'];
         // query
         $this->remote_search_url .= "?Query='" . urlencode(urldecode($this->search_query));
         $domain = 'site:' . $this->context_domain;
         $this->remote_search_url .= urlencode(" $domain'");
         // options
         if (
                 $this->plugin->config['search_providers'][$this->router_name]['highlight_title'] ||
                 $this->plugin->config['search_providers'][$this->router_name]['highlight_title']
             ) {
                 $this->remote_search_url .= "&Options='EnableHighlighting'";
             }
         // top
         $this->remote_search_url .= '&$top=' . $this->n_results;
         // skip
         if (!empty($this->offset)) {
             $this->remote_search_url .= '&$skip=' . $this->offset;
         }
         // format
         $this->remote_search_url .= '&$format=json';
     }
     
     protected function get_remote_results()
     {
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $this->remote_search_url);
         curl_setopt($ch, CURLOPT_HEADER, false);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
         curl_setopt($ch, CURLOPT_USERAGENT, "Wordpress Smart Search Engine");
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
         curl_setopt($ch, CURLOPT_USERPWD, $this->apikey . ":" . $this->apikey);
         curl_setopt($ch, CURLOPT_VERBOSE, true);
         /*
           $verbose = fopen('php://temp', 'rw+');
           curl_setopt($ch, CURLOPT_STDERR, $verbose);
          * 
          */
         $json = curl_exec($ch);
         /*
           if ($json === FALSE) {
           printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
           htmlspecialchars(curl_error($ch)));
           }

           rewind($verbose);
           $verboseLog = stream_get_contents($verbose);

           echo "<pre>", htmlspecialchars($verboseLog), "</pre>\n";
          * 
          */
         curl_close($ch);

         $response = json_decode($json);
         if (empty($response->d->results)) {
             return false;
         }
	 // Messy BING! It always set __next URL even if there are NOT more results there
	 $this->has_next = isset($response->d->__next);
	 
         $results = array();
         foreach ($response->d->results as $result) {
             $post = new stdClass();
             $post->hash = $result->ID;
             $post->post_permalink = urldecode($result->Url);
             $post->post_title = $result->Title;
             $post->post_excerpt = $result->Description;
             $post->post_content = $result->Description;
             
             $results[] = new SmartSearchResultItem($post);
         }
         
         return $results;
     }
     
 }

 