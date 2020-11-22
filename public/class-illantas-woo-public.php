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

include_once ILLANTAS_DIR.'includes/class-illantas-woo-relations.php';

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

	public function generar_filtro_illantas( $attsc , $content ){

		$attsc 		= shortcode_atts(['marca' => '', 'fabricante' => ''], $attsc, SHORTCODE_NAME );

		$param_marca 	= $attsc['marca'];
		$param_fabricante = $attsc['fabricante'];

		if ( is_singular() ){
			include_once 'partials/illantas-woo-shortcode-display.php';
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 		1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/illantas-filter.css', array(), $this->version, 'all' );

	} // enqueue_styles()

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 		1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/illantas-filter.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( $this->plugin_name.'-fix', plugin_dir_url( __FILE__ ) . 'js/fix-woo-filter.js', array( 'jquery' ), $this->version, true );
		wp_localize_script($this->plugin_name.'-fix', 'dcms_vars', ['ajaxurl'=>admin_url('admin-ajax.php')]);

		// Definimos la página base para el shortcode
		$current_page = BASE_PAGE_SHORTCODE;
		if (is_home() || is_front_page()){
			$current_page = '/';
		}

		//Definimos las variables WordPress a enviar dentro de un array
		$params = array (
			'currentPage' => $current_page
		);

		//Usamos esta función para que coloque los valores inline
		wp_localize_script($this->plugin_name,'dcms_vars', $params);

	} // enqueue_scripts()


	// Ocultar algunos atributos en la página detalle de producto
	public function illantas_hide_attributes_single_product($attributes, $product){

		if ( ! is_singular('product') ) return $attributes;

		$hidden_attributes = [
			'pa_ano',
			'pa_modelo',
			'pa_marca',
		];

		foreach ( $hidden_attributes as $hidden_attribute ) {

			if ( ! isset( $attributes[ $hidden_attribute ] ) ) {
				continue;
			}

			$attribute = $attributes[ $hidden_attribute ];

			$attribute->set_visible( false );
		}

		return $attributes;
	}



	// Función para corregir la relación de marca - model en los filtros por defecto de WooCommerce
	public function illantas_fix_woo_filter(){

		$filter_marca = $_POST['filter_marca'];

		if ( $filter_marca ){
			$relation = new Illantas_Woo_Relations();
			$result = $relation->get_modelos_marca_by_slug($filter_marca);

			error_log(print_r($result,true));
			echo wp_json_encode($result);
		}

		wp_die();
	}

} // class


	/*
		Para asegurarse de agregar las variables en los parámetros
	*/
	// public function query_vars_illantas( $vars ){
	// 	$arr = [
	// 		'pa_modelo',
	// 		'pa_diametro',
	// 		'pa_anchura',
	// 		'pa_acabado',
	// 	];

	// 	foreach ($arr as $value) {
	// 		if ( ! in_array($value, $vars ) ){
	// 			$vars[] = $value;
	// 		}
	// 	}

	// 	return $vars;
	// }
