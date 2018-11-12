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

include_once ILLANTAS_DIR . 'includes/class-illantas-woo-relations.php';


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


	//=== Agrega o edita campo marca en taxonomia modelo ===
	// =======================================================


	// Menú bajo Woocommerce
	public function illantas_admin_menu() {
		add_submenu_page( 'woocommerce',
						'Regulariza Marcas y Modelos',
						'Regulariza Modelos',
						'manage_options',
						'illantas-regulariza',
						array( $this, 'illantas_admin_regulariza_modelos') );
	}

	// Muestra la opción de menú de regularización marca modelo
	public function illantas_admin_regulariza_modelos(){
			include_once ILLANTAS_DIR . 'admin/partials/illantas-woo-regulariza-display.php';
	}

	// Regulariza las marcas / modelos para nuevos productos
	public function illantas_regulariza_ajax(){
		$rel = new Illantas_Woo_Relations();
		$rel->regularizacion_marcas_modelos();
		wp_die();
	}


	//=== Agrega o edita campo marca en taxonomia modelo ===
	// =======================================================

	// Agregar campo adicional a la taxonomia pa_modelos
	public function add_marcas_field( $taxonomy ) {
		include_once ILLANTAS_DIR . 'admin/partials/illantas-woo-field-marca-display.php';
	}

	// Graba las marcas del campo añadido
	public function save_marcas_fields( $term_id ) {
	    if ( isset( $_POST['sel-marcas'] ) ) {
	    	$id_marca = $_POST['sel-marcas'];
	        update_term_meta( $term_id, TERM_META_MARCA, $id_marca );
	    }
	}


	//=== Agrega o edita campo anclaje en taxonomia modelo ===
	// =======================================================

	// Agregar campo adicional a la taxonomia pa_modelos
	public function add_anclajes_field( $taxonomy ) {
		include_once ILLANTAS_DIR . 'admin/partials/illantas-woo-field-anclaje-display.php';
	}

	// Graba los anclajes del campo añadido
	public function save_anclajes_fields( $term_id ) {
	    if ( isset( $_POST['sel-anclajes'] ) ) {
	    	$id_anclaje = $_POST['sel-anclajes'];
	        update_term_meta( $term_id, TERM_META_ANCLAJE, $id_anclaje );
	    }
	}


	//=== Grabado de autocompletado campo modelo en base a marca en el detalle de producto ===
	// =========================================================================================

	// Grabado al final de todo grabado del producto
	public function illantas_update_post_meta( $meta_id, $post_id, $meta_key, $meta_value ){

		if( $meta_key == '_edit_lock' ) {

			$nombre_transient = TRANSIENT_MARCAS_GRABAR . '|' . $post_id;
			$modelos = get_transient( $nombre_transient );


			if ( ! $modelos ) return; // validación

			$modelos_anteriores =  wp_get_object_terms( $post_id, TAX_MODELO );

			foreach ($modelos_anteriores as $item) {
				$modelos[] = $item->term_id;
			}

			$rel = new Illantas_Woo_Relations();
			$rel->save_post_meta_attributes( $post_id, $modelos ); //grabar en el post_meta

			//delete_transient( $nombre_transient ); // Eliminamos transient
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
				if ( isset( $data['attribute_values'] ) && array_key_exists( $index, $data['attribute_values'] ) ) {
					$attrs_values = $data['attribute_values'][$index]; // recupero todos los valores de marcas
				}

			}

			// Agrego o elimino modelos de acuerdo a la comparación de arrays de marcas
			$this->transient_add_attributes( $product_id, $product_meta, $attrs_values );

			// actualizo los valores de las marcas actuales
			update_post_meta( $product_id, POST_META_MARCA, $attrs_values );
		}

	}

	// Almacenamiento temporal de los modelos de las marcas agregadas
	private function transient_add_attributes( $product_id, $arr_before, $arr_after ){

		if ( ! $product_id ) return;

		$rel = new Illantas_Woo_Relations();
		$modelos = Array();

		foreach ( $arr_after as $item ){ //Agregar modelos
			if ( ! in_array( $item, $arr_before ) ){
				$modelos = array_merge( $modelos, $rel->get_modelos_marca( $item ) ); // un array continuo de elementos modelos
			}
		}

		$transient_name = TRANSIENT_MARCAS_GRABAR . '|' . $product_id;
		set_transient( $transient_name, $modelos, MINUTE_IN_SECONDS*5 );

	}



} // class





// public function illantas_csv_before_import($data){
// 	$id = $data['id'];
// 	$product = wc_get_product( $id );

// 	error_log('Producto:');
// 	error_log(print_r($product,true));

// 	error_log('Datos:');
// 	error_log(print_r($data,true));

// 	// error_log('Importar');
// 	// error_log( print_r($data, true) );
// }


// public function illantas_csv_after_import($object, $data){
// 	// error_log('Object');
// 	// error_log(print_r($object,true));
// 	// error_log('Data');
// 	// error_log( print_r($data, true) );
// }


// include_once ILLANTAS_DIR . 'includes/class-illantas-woo-relations.php';
// $rel = new Illantas_Woo_Relations();
// $arr = $rel->get_modelos_marca( 34 );

// print_r($arr);

// $product_id = 68;

// $modelos[] = 35;
// $modelos[] = 36;


// wp_set_object_terms( $product_id, $modelos, TAX_MODELO ); // agregamos modelos
// $meta_data_post = [
//      				TAX_MODELO => [ 'name'=> TAX_MODELO,
//      								'value'=> $modelos,
//            						  	'is_visible' => '1',
//            						  	'is_variation' => '0',
//            						  	'is_taxonomy' => '1' ]
// 				  ];

// update_post_meta( $product_id, '_product_attributes', $meta_data_post );

// $products = wc_get_products(
// 	[
// 		'return' => 'ids',
// 		'limit' => -1
// 	]
// );




