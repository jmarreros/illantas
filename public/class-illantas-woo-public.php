<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link 			https://mundollantas.es
 * @since 			1.0.0
 *
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/public
 * @author 			jmarreros
 */
class Illantas_Woo_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 		$plugin_name 		The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 		$version 		The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 		1.0.0
	 * @param 		string 		$plugin_name 		The name of the plugin.
	 * @param 		string 		$version 			The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	} // __construct()


	// Creación del shortcode
	public function illantas_shortcodes(){
		add_shortcode(SHORTCODE_NAME, array($this,'generar_filtro_illantas'));
	}

	public function generar_filtro_illantas( $atts , $content ){
		$atts 		= shortcode_atts(['marca' => 'todos'], $atts, SHORTCODE_NAME );
		$marca 	= $atts['marca'];
		include_once 'partials/illantas-woo-shortcode-display.php';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 		1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/illantas-woo-public.css', array(), $this->version, 'all' );

	} // enqueue_styles()

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 		1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/illantas-woo-public.js', array( 'jquery' ), $this->version, false );

	} // enqueue_scripts()

} // class


	// Asignación de parámetros en la url
	// public function illantas_shortcodes_query_args($query_vars){
	// 	$attrs = wc_get_attribute_taxonomies();
	// 	$vars = wp_list_pluck($attrs, 'attribute_name');

	// 	if ( ! get_query_var($vars[0]) ){
	// 		foreach ($vars as $var) {
	// 			$query_vars[] = $var;
	// 		}
	// 	}

	// 	error_log(print_r($query_vars,true));
	// 	return $query_vars;
	// }
