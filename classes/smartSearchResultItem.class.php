<?php
 
 class SmartSearchResultItem
 {
     public $ID = 0;
     public $post_type = 'post';
     public $post_status = 'publish';
     public $guid;
     public $post_title;
     public $post_excerpt;
     public $post_content;

     public function __construct(stdClass $post)
     {
         foreach ( get_object_vars( $post ) as $key => $value ) {
             $this->$key = $value;
         }	
     }
 }