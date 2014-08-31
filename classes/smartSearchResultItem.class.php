<?php
 
 class SmartSearchResultItem
 {
     public $url;
     public $title;
     public $description;
     
     public function __construct(array $args)
     {
         $this->url = $args['post_permalink'];
         $this->title = $args['post_title'];
         $this->description = $args['post_excerpt'];
     }
 }