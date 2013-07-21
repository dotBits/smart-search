<?php

 require_once ROUTERS_PATH . '/searchRouter.abst.php';

 class BingSearchRouterImpl extends SearchRouter
 {

     function __construct(WP_Query $wp_query)
     {
         parent::__construct( $wp_query );         
         // prepare for search
         $this->init();
         // straight search
         $this->search();
     }

     protected function parse_custom_query()
     {
         $this->set_search_string( $this->query->get( 'search_query' ) );
     }

     protected function set_search_uri()
     {
         if (!empty( $this->search_string ))
         {
             $this->search_uri.= "&Query='" . $this->search_string;
             $domain = 'site:http://microsoft.com';
             $this->search_uri.= urlencode( " $domain'" );
             return true;
         }
         else
         {
             return false;
         }
     }

     protected function set_matched_post_ids()
     {
         $search = SmartSearch::get_instance();
         $apikey = $search->config['search_providers'][$this->query->get( 'search_router' )]['API_KEY'];

         // Encode the credentials and create the stream context.         
         $auth = base64_encode( $apikey . ':' . $apikey );
         $data = array(
                 'http' => array(
                         'request_fulluri' => true,
                         // ignore_errors can help debug – remove for production. This option added in PHP 5.2.10
                         'ignore_errors' => true,
                         'header' => "Authorization: Basic $auth")
         );
         $context = stream_context_create( $data );
         // Get the response from Bing
         $response = json_decode( file_get_contents( $this->search_uri, 0, $context ) );
         $results = (!empty( $response )) ? $response->d->results : array();
         foreach ($results as $result) {
             $post_id = url_to_postid( $result->Url );
             if ($post_id > 0)
             {
                 array_push($this->matched_post_ids, $post_id);
             }
         }
         $this->matched_post_ids = array_unique( $this->matched_post_ids );
     }
     
     protected function alter_main_query()
     {
         $this->query->is_search = (bool) 1;
         $this->query->set('post__in', $this->matched_post_ids);            
         $this->query->set('orderby', 'post__in');
     }

 }

 