<?php

$config['search_providers'] = array(
        
        'bing' => array(
                'slugs' => array('bing', 'microsoft'),
                'base_uri' => 'https://api.datamarket.azure.com/Bing/Search/v1/Web?$format=json',
                'max_result' => 10,
                'cache_expire' => 3600,
		'context_domain' => '',
		'no_results_url' => '',
		'use_remote_title' => false,
		'use_remote_excerpt' => false,
		'highlight_title' => true,
		'highlight_title_color' => '',
		'highlight_title_txt_color' => '',
		'highlight_excerpt' => true,
		'highlight_excerpt_color' => '',
		'highlight_excerpt_txt_color' => ''
        )
);
// used to override WP. must be registered above. Default to "none"
$config['default_search_engine'] = 'bing'; 
