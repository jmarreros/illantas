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


	// Agregar campo adicional a la taxonomia pa_modelos
	public function add_marcas_field( $taxonomy ) {
		include_once ILLANTAS_DIR . 'includes/class-illantas-woo-relations.php';		
		include_once ILLANTAS_DIR . 'admin/partials/illantas-woo-field-term-display.php';		
	}


	// Graba las marcas del campo añadido
	public function save_marcas_fields( $term_id ) {  
	    if ( isset( $_POST['sel-marcas'] ) ) {
	    	$id_marca = $_POST['sel-marcas'];
	        update_term_meta( $term_id, TERM_META, $id_marca );
	    }  
	}  



	// Save product attributes

	public function illantas_save_attributes(){

		if ( isset ( $_POST['post_id'] ) ){

    		$product_id = absint($_POST['post_id']);
			parse_str($_POST['data'], $data);

			$product_meta = get_post_meta( $product_id, POST_META_MARCA, true ); // recupero los valores guardados anteriormente
			$attrs_names = $data['attribute_names']; // para comprobar si tiene atributo marca
			$attrs_values = array(); // para recuperar los valores de marcas que tiene

			if ( in_array( TAX_MARCA , $attrs_names ) ){

				//consistencia en caso elimine el atributo y luego lo agregue, siempre recupera el último
				$keys_marcas = array_keys( $attrs_names , TAX_MARCA ); 
				$index = end( $keys_marcas ); 
				
				//verificar si existen valores de marcas
				if ( array_key_exists( $index, $data['attribute_values'] ) ) {
					$attrs_values = $data['attribute_values'][$index]; // recupero todos los valores de marcas
				}

			}

			// Agrego o elimino modelos de acuerdo a la comparación de arrays de marcas
			$this->add_remove_attributes( $product_id, $product_meta, $attrs_values );

			// actualizo los valores de las marcas actuales
			update_post_meta( $product_id, POST_META_MARCA, $attrs_values ); 

		}

	}


	private function add_remove_attributes( $product_id, $arr_before, $arr_after){
		
		if ( ! $product_id ) return;

		foreach ( $arr_before as $item ){
			if ( ! in_array( $item, $arr_after ) ){
				// eliminamos modelos
				error_log("Eliminar modelos de la marca $item");
			}
		}
		foreach ( $arr_after as $item ){
			if ( ! in_array( $item, $arr_before ) ){
				// agregamos modelos
				error_log("Agregar modelo de la marca $item");

				// wp_set_object_terms( $product_id, );
			}			
		}
	}

	private function get_modelos_marca( $id_marca ){
		
	}


	// error_log( print_r( $data['attribute_values'][$index], true) );

	// Después de grabar un producto
	// public function illantas_save_product( $post_id, $post, $update ) {


	// 	// 	$terms = wp_get_post_terms( $post_id, 'pa_marca' );

	// 	// 	error_log( print_r( $terms, true ) );


	// 	// // error_log( print_r($post, true) );

	// 	// if ( $post_id ){ //edición

	// 		// $product = wc_get_product( $post_id );
	// 		// $attrs = $product->get_attributes();



	// 		// error_log( print_r( $attrs, true ) );

	// 		// if ( isset( $attrs['pa_marca'] ) ){
	// 		// 	error_log( print_r( $attrs['pa_marca'], true ) );
	// 		// }

	// 		// $saved_marcas =  get_post_meta( $post_id, POST_META_MARCA, true );

	// 		// foreach ( $saved_marcas as $marca ) {
				
	// 		// }

	// 	}
	 //    $product = wc_get_product( $post_id );
	    
	 //    $attr = $product->get_attributes();

		// $brands = wp_get_post_terms( $post_id, 'product_brand', ['fields' => 'all']);

		// $marcas = wp_get_post_terms( $post_id, 'pa_marca' );
		
		// error_log( print_r( $brands, true ) );
		// error_log( print_r( $marcas, true ) );

	 //    // foreach ($attr as $key => $value) {
	 //    // 	error_log( print_r( $value, true ) );
	 //    // }

	 //    // error_log($update);

	// }







} // class




	// public function enqueue_styles() {
	// 	wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/illantas-woo-admin.css', array(), $this->version, 'all' );
	// } // enqueue_styles()


	// public function enqueue_scripts() {
	// 	wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/illantas-woo-admin.js', array( 'jquery' ), $this->version, false );
	// } // enqueue_scripts()



	// Menú bajo Woocommerce
	// public function illantas_admin_menu() {
		
	// 	add_submenu_page( 'woocommerce',
	// 					'iLlantas',
	// 					'iLlantas',
	// 					'manage_options', 
	// 					'illantas',
	// 					array( $this, 'illantas_admin_relations') );

	// }


	// // Muestra la opción de menú
	// public function illantas_admin_relations(){
	// 	//include_once ILLANTAS_DIR . 'admin/partials/illantas-woo-admin-display.php';
	// }



