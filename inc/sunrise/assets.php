<?php

 // Enqueue backend assets
 if (is_admin())
 {
     wp_enqueue_style($this->slug . '-backend', $this->assets_url.'/css/backend.css', false, $this->version, 'all');
     wp_enqueue_script('jquery');
     wp_enqueue_script($this->slug . '-backend', $this->assets_url.'/js/backend.js', array('jquery'), $this->version, false);
 }
?>