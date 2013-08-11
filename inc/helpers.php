<?php

/**
 * Wether the current page is the last one of paginated set
 * @param WP_Query $query Wordpress query to check against
 * @return bool true if is last - false if not
 */
function is_penultimate_page(WP_Query $query = null)
{
    if($query === null)
    {
        global $wp_the_query;
        $query = &$wp_the_query;
    }
    
    $per_page = $query->get('posts_per_page');
    $found_posts = $query->get('real_found_posts');
    $ppp = ($per_page > 0) ? $per_page : get_option( 'posts_per_page' );

    $max = ceil( intval($found_posts) / $ppp );

    $current_page = $query->get('paged');
    
    $is_last = ($max > 0 && $current_page == ($max-1));
    
    return $is_last;
}

/**
  * Outputs or Return any file passing an array of usable parameters
  * @param string $filepath absolute path for file to render
  * @param array $data parameters that file use
  * @param boolean $echo wether to echo or return the file string
  * @return string
  */
 function render_view($filepath, array $data, $echo = true)
 {
     ob_start();
     if (!file_exists( $filepath ))
     {
         echo 'Fatal Error: no such file ' . $filepath;
         exit;
     }
     include $filepath;
     $result = ob_get_contents();
     ob_end_clean();
     if ($echo === true)
     {
         echo $result;
     }
     else
     {
         return $result;
     }
 }
