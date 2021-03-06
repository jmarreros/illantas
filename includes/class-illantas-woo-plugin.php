<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link 			https://mundollantas.es
 * @since 			1.0.0
 *
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/includes
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
 * @since 			1.0.0
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/includes
 * @author 			jmarreros
 */
class Illantas_Woo {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 		1.0.0
	 * @access 		protected
	 * @var 		Illantas_Woo_Loader 		$loader 		Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 		1.0.0
	 * @access 		protected
	 * @var 		string 		$plugin_name 		The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 		1.0.0
	 * @access 		protected
	 * @var 		string 		$version 		The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 		1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'illantas-woo';
		$this->version = VERSION_PLUGIN;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	} // __construct()

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Illantas_Woo_Loader. Orchestrates the hooks of the plugin.
	 * - Illantas_Woo_i18n. Defines internationalization functionality.
	 * - Illantas_Woo_Admin. Defines all hooks for the admin area.
	 * - Illantas_Woo_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-illantas-woo-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-illantas-woo-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-illantas-woo-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-illantas-woo-public.php';

		$this->loader = new Illantas_Woo_Loader();

	} // load_dependencies()

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Illantas_Woo_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 */
	private function set_locale() {

		$plugin_i18n = new Illantas_Woo_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	} // set_locale()

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Illantas_Woo_Admin( $this->get_plugin_name(), $this->get_version() );

		// graba cambios al grabar los atributos de un producto
		// $this->loader->add_action('woocommerce_process_product_meta_simple', $plugin_admin, 'illantas_save_attributes', 10);
		$this->loader->add_action( 'wp_ajax_woocommerce_save_attributes', $plugin_admin, 'illantas_save_attributes', 0);
		$this->loader->add_action( 'updated_post_meta', $plugin_admin, 'illantas_update_post_meta', 10, 4);

		// Regulariza las marcas/modelos manualmente
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'illantas_admin_menu', 99 );
		$this->loader->add_action( 'wp_ajax_illantas_regulariza_nuevos', $plugin_admin, 'illantas_regulariza_nuevos_ajax' );
		$this->loader->add_action( 'wp_ajax_illantas_regulariza_existentes', $plugin_admin, 'illantas_regulariza_existentes_ajax' );
		$this->loader->add_action( 'wp_ajax_illantas_regulariza_atributos', $plugin_admin, 'illantas_regulariza_atributos_ajax' );

		// Agrega campo marca en taxonomia modelo
		$this->loader->add_action( TAX_MODELO.'_edit_form_fields', $plugin_admin, 'add_marcas_field', 10, 2);
		$this->loader->add_action( TAX_MODELO.'_add_form_fields', $plugin_admin, 'add_marcas_field', 10, 2);
		// graba cambios en taxonomia modelo
		$this->loader->add_action( 'edited_'.TAX_MODELO, $plugin_admin, 'save_marcas_fields', 10, 2);
		$this->loader->add_action( 'created_'.TAX_MODELO, $plugin_admin, 'save_marcas_fields', 10, 2);


		// Agrega campo anclaje en taxonomia modelo
		$this->loader->add_action( TAX_MODELO.'_edit_form_fields', $plugin_admin, 'add_anclajes_field', 10, 2);
		$this->loader->add_action( TAX_MODELO.'_add_form_fields', $plugin_admin, 'add_anclajes_field', 10, 2);
		// graba cambios en taxonomia modelo
		$this->loader->add_action( 'edited_'.TAX_MODELO, $plugin_admin, 'save_anclajes_fields', 10, 2);
		$this->loader->add_action( 'created_'.TAX_MODELO, $plugin_admin, 'save_anclajes_fields', 10, 2);


		// Agrega los atributos del producto en los items de una orden
		$this->loader->add_action( 'woocommerce_after_order_itemmeta', $plugin_admin, 'add_info_attributos_order', 10, 3);

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_scripts' );

		// //Importer CSV hooks
		// $this->loader->add_filter( 'woocommerce_product_import_before_process_item', $plugin_admin, 'illantas_csv_before_import', 10, 1);
		// $this->loader->add_filter( 'woocommerce_product_import_inserted_product_object', $plugin_admin, 'illantas_csv_after_import', 10, 2);

	} // define_admin_hooks()


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin. Para los shortcodes
	 *
	 * @since 		1.0.0
	 * @access 		private
	 */
	private function define_public_hooks() {

		$plugin_public = new Illantas_Woo_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'illantas_shortcodes' );
		$this->loader->add_action( 'woocommerce_product_get_attributes', $plugin_public, 'illantas_hide_attributes_single_product', 10, 2 );
		$this->loader->add_action( 'wp_ajax_nopriv_fix_woo_filter', $plugin_public, 'illantas_fix_woo_filter' );
		$this->loader->add_action( 'wp_ajax_fix_woo_filter', $plugin_public, 'illantas_fix_woo_filter' );

	} // define_public_hooks()





	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 		1.0.0
	 */
	public function run() {

		$this->loader->run();

	} // run()

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 		1.0.0
	 * @return 		string 		The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;

	} // get_plugin_name()

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 		1.0.0
	 * @return 		Illantas_Woo_Loader 		Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {

		return $this->loader;

	} // get_loader()

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 		1.0.0
	 * @return 		string 		The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;

	} // get_version()

} // class





// $this->loader->add_filter( 'query_vars', $plugin_public, 'query_vars_illantas' );


