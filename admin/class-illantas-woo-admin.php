<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link 			https://mundollantas.es
 * @since 			1.0.0
 *
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/admin
 * @author 			jmarreros
 */
class Illantas_Woo_Admin {

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
	 * @param 		string 		$plugin_name 		The name of this plugin.
	 * @param 		string 		$version 			The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	} // __ construct()

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 		1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/illantas-woo-admin.css', array(), $this->version, 'all' );
	} // enqueue_styles()

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 		1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/illantas-woo-admin.js', array( 'jquery' ), $this->version, false );
	} // enqueue_scripts()


	// Menú bajo Woocommerce
	public function illantas_admin_menu() {
		
		add_submenu_page( 'woocommerce',
						'Marca/Modelo',
						'Marca/Modelo',
						'manage_options', 
						'illantas',
						array( $this, 'illantas_admin_relations') );

	}


	// Muestra la opción de menú
	public function illantas_admin_relations(){
		include_once ILLANTAS_DIR . 'admin/partials/illantas-woo-admin-display.php';
	}


	// Agregar campo adicional a la taxonomia pa_modelos
	public function add_marcas_field( $taxonomy ) {
		include_once ILLANTAS_DIR . 'admin/partials/illantas-woo-field-term-display.php';		
	}



	public function save_marcas_fields( $term_id ) {  
	    if ( isset( $_POST['sel-marcas'] ) ) {

	    	$id_marca = $_POST['sel-marcas'];

	        update_term_meta( $term_id, TERM_META, $id_marca );
	    }  
	}  



	// Después de grabar un producto
	public function illantas_save_product( $post_id, $post, $update ) {
	    $product = wc_get_product( $post_id );
	    
	    $attr = $product->get_attributes();

		$brands = wp_get_post_terms( $post_id, 'product_brand', ['fields' => 'all']);

		$marcas = wp_get_post_terms( $post_id, 'pa_marca' );
		
		error_log( print_r( $brands, true ) );
		error_log( print_r( $marcas, true ) );

	    // foreach ($attr as $key => $value) {
	    // 	error_log( print_r( $value, true ) );
	    // }

	    // error_log($update);

	}







} // class
