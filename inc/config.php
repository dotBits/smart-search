<?php

$config['search_providers'] = array(
        
        'bing' => array(
                'slugs' => array('bing', 'microsoft'),
                'base_uri' => 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json',
                'API_KEY' => 'wtn4cvGk7uEqzSP/XBwwYGZtenwexkKitkZtGeb0ViA',
                'max_result' => 10,
                'cache_expire' => 3600
        ),
        
        'google' => array(
                'slugs' => array('google', 'big_g'),
                'base_uri' => null
        )
);
// used to override WP. must be registered above. Default to "none"
$config['default_search_engine'] = 'bing'; 
