<?php

 /**
  * SmartSearch class.
  *
  *
  * @package SmartSearch
  * @author  Cristian Ronzio <cristian.ronzio@gmail.com>
  */
 class SmartSearch
 {

     /**
      * Plugin version, used for cache-busting of style and script file references.
      *
      * @since   1.0.0
      *
      * @var     string
      */
     protected $version = '1.0.0';

     /**
      * Unique identifier for your plugin.
      *
      * @since    1.0.0
      *
      * @var      string
      */
     protected $plugin_slug = PLUGIN_SLUG;

     /**
      * Instance of this class.
      *
      * @since    1.0.0
      *
      * @var      object
      */
     protected static $instance = null;

     /**
      * Slug of the plugin screen.
      *
      * @since    1.0.0
      *
      * @var      string
      */
     protected $plugin_screen_hook_suffix = null;

     /**
      * Plugin config
      *
      * @since    1.0.0
      *
      * @var      mixed
      */
     public $config = null;

     /**
      * Concrete search delegate implementation
      * 
      * @var SearchRouter impl
      */
     private $search_router;
     
     /**
      * Plugin options saved by admin settings page
      * @var mixed
      */
     public $options;

     /**
      * Initialize the plugin by setting localization, filters, and administration functions.
      *
      * @since     1.0.0
      */
     public function __construct()
     {
         /*
           require_once CLASS_PATH . '/smartOptions.class.php';
           $this->option_handler = new SmartSearchOptions();
           $this->options = $this->option_handler->get_options();
          * 
          */
         $this->set_config();

         // Load plugin text domain
         add_action( 'init', array($this, 'load_plugin_textdomain') );
         // Load plugin options
         //add_action( 'init', array($this, 'load_plugin_options') );

         // Add the options page and menu item.
         add_action( 'admin_menu', array($this, 'add_plugin_admin_menu') );
         // save options hook
         add_action( 'admin_init', array($this, 'save_settings') );

         // Register admin styles and scripts
         add_action( 'admin_print_styles', array($this, 'enqueue_admin_styles') );
         add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );

         add_filter( 'generate_rewrite_rules', array($this, 'add_rewrite_rules') );
         add_filter( 'query_vars', array($this, 'define_query_vars') );

         add_action( 'pre_get_posts', array($this, 'route_request') );
         
     }

     /**
      * Return an instance of this class.
      *
      * @since     1.0.0
      *
      * @return    object    A single instance of this class.
      */
     public static function get_instance()
     {
         // If the single instance hasn't been set, set it now.
         if (null == self::$instance)
         {
             self::$instance = new self;
         }

         return self::$instance;
     }

     public function set_config()
     {
         $this->config = get_transient( 'bing-search-options' );
     }
     
     public function get_config()
     {
         return get_transient( 'bing-search-options' );
     }

     /**
      * Fired when the plugin is activated.
      *
      * @since    1.0.0
      *
      * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
      */
     public static function activate($network_wide)
     {
         $wp_ver = get_bloginfo('version');
         if( version_compare( $wp_ver, MIN_WP_VER, 'lt') )
         {
             $plugin = get_plugin_data(PLUGIN_PATH . '/smart-search.php', false);
             echo '<span style="color:red">Error: </span>'.
                 sprintf(__('%s plugin requires Wordpress %s. You are running %s', PLUGIN_TXT_DOMAIN), $plugin['Name'], MIN_WP_VER, $wp_ver);
             die();
         }
         // set default options if needed
         $options_transient = 'bing-search-options';
         if (false === ( $plugin_options = get_transient( $options_transient ) ))
         {
             // It wasn't there, so regenerate the data and save the transient
             $plugin_options['search_providers'] = array(
                     'bing' => array(
                             'base_uri' => 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json',
                             'API_KEY' => '',
                             'max_result' => 10,
                             'cache_expire' => 60*60*24
                     )
             );
             $plugin_options['default_search_engine'] = 'bing';
             
             set_transient( $options_transient, $plugin_options );
         }
     }

     /**
      * Fired when the plugin is deactivated.
      *
      * @since    1.0.0
      *
      * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
      */
     public static function deactivate($network_wide)
     {
         //delete_transient('bing-search-options');
     }

     /**
      * Load the plugin text domain for translation.
      *
      * @since    1.0.0
      */
     public function load_plugin_textdomain()
     {
         $domain = PLUGIN_TXT_DOMAIN;
         $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

         load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
         load_plugin_textdomain( $domain, FALSE, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/lang/' );
     }

     /**
      * Register and enqueue admin-specific style sheet.
      *
      * @since     1.0.0
      *
      * @return    null    Return early if no settings page is registered.
      */
     public function enqueue_admin_styles()
     {
         if (!isset( $this->plugin_screen_hook_suffix ))
         {
             return;
         }

         $screen = get_current_screen();
         if ($screen->id == $this->plugin_screen_hook_suffix)
         {
             wp_enqueue_style( $this->plugin_slug . '-admin-styles', PLUGIN_URL . '/css/admin.css', array(), $this->version );
         }
     }

     /**
      * Register and enqueue admin-specific JavaScript.
      *
      * @since     1.0.0
      *
      * @return    null    Return early if no settings page is registered.
      */
     public function enqueue_admin_scripts()
     {
         if (!isset( $this->plugin_screen_hook_suffix ))
         {
             return;
         }

         $screen = get_current_screen();
         if ($screen->id == $this->plugin_screen_hook_suffix)
         {
             wp_enqueue_script( 'underscore' );
             wp_enqueue_script( $this->plugin_slug . '-admin-script', PLUGIN_URL . '/js/admin.js', array('jquery', 'underscore'), $this->version );
             wp_localize_script($this->plugin_slug . '-admin-script', 'SS', array(
                     'invalid_expire_value' => __('Invalid Expire value', PLUGIN_TXT_DOMAIN),
                     'cache_disabled' => __('Cache disabled', PLUGIN_TXT_DOMAIN),
                     'hours' => __('Hours', PLUGIN_TXT_DOMAIN),
             ));
         }
     }

     /**
      * Register the administration menu for this plugin into the WordPress Dashboard menu.
      *
      * @since    1.0.0
      */
     public function add_plugin_admin_menu()
     {
         $this->plugin_screen_hook_suffix = add_plugins_page(
             __( 'Smart Search Options Page', PLUGIN_TXT_DOMAIN ), __( 'Search Settings', PLUGIN_TXT_DOMAIN ), 'manage_options', $this->plugin_slug, array($this, 'display_plugin_admin_page')
         );
     }

     /**
      * Render the settings page for this plugin.
      *
      * @since    1.0.0
      */
     public function display_plugin_admin_page()
     {         
         $options = $this->get_config();
         $data['API_KEY'] = $options['search_providers']['bing']['API_KEY'];
         $data['cache_expire'] = $options['search_providers']['bing']['cache_expire'];
         
         render_view(PLUGIN_PATH . '/views/admin.php', $data);
     }
     
     /**
      * Register the settings fields for this plugin
      */
     public function save_settings()
     {
         $admin_page = (!empty( $_GET['page'] )) ? $_GET['page'] : "";
         if (!empty($admin_page) && $admin_page === $this->plugin_slug)
         {
             if(!empty($_POST))
             {
                 $data = $_POST;
                 $options = $this->get_config();
                 $options['search_providers']['bing']['API_KEY'] = $data['API_KEY'];
                 $options['search_providers']['bing']['cache_expire'] = $data['cache_expire'];
                 
                 set_transient('bing-search-options', $options);
             }
         }
     }

     /**
      * Add SmartSearch rewrite rules
      * @param WP_Rewrite $wp_rewrite
      */
     public function add_rewrite_rules($wp_rewrite)
     {
         $search_base = $wp_rewrite->search_base;
         $page = $wp_rewrite->pagination_base;
         
         $new_rules = array();
         // rewrite rules         
         $new_rules[$search_base . '/([^/]+)/?$'] = 'index.php?search_router=bing&s=$matches[1]';
         $new_rules[$search_base . '/([^/]+)/' . $page . '/([\d]+)/?$'] = 'index.php?search_router=bing&s=$matches[1]&paged=$matches[2]';
         
         $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
     }

     public function define_query_vars($qvars)
     {
         global $wp_rewrite;

         $qvars[] = 'search_router';
         $qvars[] = 'search_query';

         return $qvars;
     }

     /**
      * If needed, instantiate a concrete search_router upon search_slug
      * 
      * @param WP_Query $wp_query
      * @uses $config to check if request needs to be handled
      */
     public function route_request($wp_query)
     {
         $prefix = $wp_query->get( 'search_router' );
         $s = $wp_query->get( 's' );
         // override Wordpress search behavior
         if (!empty( $s ))
         {
             $prefix = $this->config['default_search_engine'];
             $wp_query->set( 'search_router', $prefix );
             $wp_query->set( 'search_query', $s );
             // remove the search section of the database query
             add_filter( 'posts_search', array($this, 'override_wp_search') );
         }
         $search_query = $wp_query->get( 'search_query' );
         // execute outside admin section and within main_query, if and only if search_slug is handled
         if (!is_admin() && $wp_query->is_main_query() && !empty( $prefix ) && !empty( $search_query ))
         {
             $this->prepare_router( $prefix );
         }
     }

     /**
      * Cleanup original wordpress search clause if needed
      */
     public function override_wp_search($search_clause)
     {
         return "";
     }

     /**
      * New concrete Router instance
      * @param string $prefix
      * @param WP_Query $wp_query
      */
     public function prepare_router($prefix)
     {
         $fileName = strtolower( $prefix ) . 'SearchRouter.class.php';
         $className = ucfirst( $prefix ) . 'SearchRouterImpl';
         if (file_exists( ROUTERS_PATH . '/' . $fileName ))
         {
             require_once(ROUTERS_PATH . '/' . $fileName);
             $this->set_search_router( new $className() );
         }
     }

     public function get_search_router()
     {
         return $this->search_router;
     }

     public function set_search_router(SearchRouter $search_router)
     {
         $this->search_router = $search_router;
     }

 }