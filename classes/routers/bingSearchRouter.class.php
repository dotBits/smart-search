<?php

 require_once ROUTERS_PATH . '/searchRouter.abst.php';

 class BingSearchRouterImpl extends SearchRouter
 {
     /**
      * BING Search API key
      * @var string $apikey
      */
     private $apikey = null;
     
     /**
      * BING's matching items
      */
     public $results = array();
     
     /**
      *
      * @var int @TODO
      */
     public $skip = null;
     
     public function __construct($search_query = "")
     {
         parent::__construct($search_query);         
     }
     
     protected function set_transient()
     {
         global $wp_query;
         $string = $this->get_router_name()
             . '_s=' . $this->search_query
             . '_d=' . $this->context_domain
             . '_n=' . $wp_query->get('posts_per_page', get_option('posts_per_page'))
	     . '_p=' . $wp_query->get('paged');

         return $string;
     }
     
     protected function init()
     {
         $router = $this->get_router_name();
         $this->apikey = $this->plugin->config['search_providers'][$router]['API_KEY'];
         $this->set_remote_search_url_params();
     }
     
     private function set_remote_search_url_params()
     {
         $this->remote_search_url .= "&Query='" . urlencode(urldecode($this->search_query));
         $domain = 'site:' . $this->context_domain;

         $skip = ($this->skip > 0) ? '&$skip=' . $this->skip : "";
         $this->remote_search_url .= urlencode(" $domain'") . $skip;
         if(
             $this->plugin->config['search_providers'][$this->router_name]['highlight_title'] || 
             $this->plugin->config['search_providers'][$this->router_name]['highlight_title']
             ) {
             $this->remote_search_url .= "&Options='EnableHighlighting'";
         }
         
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

         $json = curl_exec($ch);
         curl_close($ch);

         $response = json_decode($json);
         if(!empty($response->d->results)) {
             $this->results = $response->d->results;
         }         
     }
 }

 