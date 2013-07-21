<?php

 define('MIN_WP_VER', '3.3.0');
 define('INC_PATH', dirname(__FILE__) );
 define('INC_OPT_PATH', dirname(__FILE__) . '/sunrise' );
 define('PLUGIN_PATH', plugin_dir_path(dirname(__FILE__)) );
 define('CLASS_PATH', PLUGIN_PATH . '/classes');
 define('ROUTERS_PATH', CLASS_PATH . '/routers');
 
 define('PLUGIN_URL', plugin_dir_url(dirname(__FILE__))); 
 define('PLUGIN_SLUG', 'smart-search');
 define('PLUGIN_TXT_DOMAIN', 'smart-search-textdomain');