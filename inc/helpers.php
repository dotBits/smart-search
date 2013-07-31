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
    // quanti posts_per_page mi aspetto?
    $per_page = $query->get('posts_per_page');
    $found_posts = $query->get('real_found_posts');
    $ppp = ($per_page > 0) ? $per_page : get_option( 'posts_per_page' );
    // quante pagine dovrei avere?
    $max = ceil( intval($found_posts) / $ppp );
    // è l'ultima fra le pagine che mi aspetto?    
    $current_page = $query->get('paged');
    
    $is_last = ($max > 0 && $current_page == ($max-1));
    
    return $is_last;
}

function is_skip_needed()
{
    return false;
}
