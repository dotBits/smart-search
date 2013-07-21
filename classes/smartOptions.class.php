<?php

 /**
  * this class is ported from Sunrise Plugin Framework Class
  * 
  * @author Christian Ronzio <cristian.ronzio@gmail.com>
  * @copyright John Doe Vladimir Anokhin <ano.vladimir@gmail.com>
  * @link http://gndev.info/sunrise/
  * @version 1.3.0
  */
 class SmartSearchOptions
 {
     /**
      * The name WP will use to store options
      * 
      * @var string
      */
     private $option_name;
     
     /**
      * Local reference 
      * 
      * @var mixed
      */
     private $options;
         
     /**
      * Constructor
      *
      * @param string $inc Relative path to includes directory. Default: '../inc/sunrise'
      */
     function __construct()
     {
         $this->option_name = 'smart_search_options';
     }

     /**
      * Set plugin settings to default
      */
     function default_settings()
     {
         
     }

     /**
      * Get plugin options
      *
      * @return mixed $options
      */
     function get_options()
     {
         $this->options = get_site_option($this->option_name, false, false);
         return $this->options;
     }

     /**
      * Get single option value from local reference
      *
      * @return mixed $option Returns option by specified key
      */
     function get_option($option)
     {
         
     }

     /**
      * Update single option value
      *
      * @return mixed $option Returns option by specified key
      */
     function update_option($key, $value)
     {
         if(empty($this->options))
             $this->get_options();
         
         $this->options[$key] = $value;
     }

     /**
      * Delete all options
      */
     function delete_options()
     {
         return delete_site_option($this->option_name);
     }

 }