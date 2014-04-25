<?php

$config['search_providers'] = array(
        
        'bing' => array(
                'slugs' => array('bing', 'microsoft'),
                'base_uri' => 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json',
                'max_result' => 10,
                'cache_expire' => 3600,
		'context_domain' => '',
		'no_results_url' => ''
        )
);
// used to override WP. must be registered above. Default to "none"
$config['default_search_engine'] = 'bing'; 
