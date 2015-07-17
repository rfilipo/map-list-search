<?php
             //echo "<p>TESTE FROM MAPLIST SEARCH";

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://kobkob.org
 * @since      1.0.0
 *
 * @package    Map_List_Search
 * @subpackage Map_List_Search/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Map_List_Search
 * @subpackage Map_List_Search/includes
 * @author     Monsenhor <filipo@kobkob.org>
 */
class Map_List_Search {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Map_List_Search_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $map_list_search    The string used to uniquely identify this plugin.
	 */
	protected $map_list_search;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->map_list_search = 'map-list-search';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->init();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Map_List_Search_Loader. Orchestrates the hooks of the plugin.
	 * - Map_List_Search_i18n. Defines internationalization functionality.
	 * - Map_List_Search_Admin. Defines all hooks for the admin area.
	 * - Map_List_Search_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for init hooks
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-map-list-search-init.php';


		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-map-list-search-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-map-list-search-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-map-list-search-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-map-list-search-public.php';

		$this->loader = new Map_List_Search_Loader();

	}

	/**
	 * Init the plugin data, options, custom types and fields.
	 *
	 * Uses the Map_List_Search_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function init() {

		$plugin_init = new Map_List_Search_Init( $this->get_map_list_search(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_init, 'create_types' );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Map_List_Search_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Map_List_Search_i18n();
		$plugin_i18n->set_domain( $this->get_map_list_search() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Map_List_Search_Admin( $this->get_map_list_search(), $this->get_version() );
		
                $this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menus' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'add_dashboard_widgets' );
		$this->loader->add_action("manage_posts_custom_column", $plugin_admin, 'custom_columns', 10, 2 );
		$this->loader->add_filter("manage_map_list_posts_columns", $plugin_admin, 'change_columns' );

	        //$this->loader->add_action( 'save_post_map_list', $plugin_admin, 'map_list_meta_save' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Map_List_Search_Public( $this->get_map_list_search(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'register_short_codes' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		//$this->loader->add_action( "parse_request", $plugin_public, 'email_form' );
		//$this->loader->add_action( 'pre_get_posts', $plugin_public, 'search_filter' );

	        $this->loader->add_filter("query_vars", $plugin_public, 'add_query_vars');
	        $this->loader->add_filter("single_template", $plugin_public, 'map_list_template');
	        $this->loader->add_filter("the_content", $plugin_public, 'map_list_facade');
	        //$this->loader->add_filter("page_template", $plugin_public, 'map_list_page_template');
		//$this->loader->add_filter( "archive_template", $plugin_public, 'email_form' );
	        //$this->loader->add_filter("search_template", $plugin_public, 'map_list_search_template');
	        //$this->loader->add_filter("pre_get_posts", $plugin_public, 'custom_search_query');
	        //$this->loader->add_filter("post_where", $plugin_public, 'query_where');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_map_list_search() {
		return $this->map_list_search;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Map_List_Search_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
