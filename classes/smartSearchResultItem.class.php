<?php
 
 class SmartSearchResultItem
 {
     public $hash; // the search engine unique id
     public $ID = 0; // this is for WP
     public $post_type = 'post';
     public $post_status = 'publish';     
     public $post_title;
     public $post_excerpt;
     public $post_content;
     public $post_permalink;

     public function __construct(stdClass $post)
     {
         foreach ( get_object_vars( $post ) as $key => $value ) {
             $this->$key = $value;
         }	
     }
 }