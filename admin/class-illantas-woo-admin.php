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


	// Agrega archivo de estilos

	public function enqueue_admin_styles(){
		wp_enqueue_style('admin-styles', plugin_dir_url(__FILE__).'../assets/illantas.css');
	}

	//=== Agrega o edita campo marca en taxonomia modelo ===
	// =======================================================


	// Menú bajo Woocommerce
	// -----------------------
	public function illantas_admin_menu() {
		// Funcionalidad regularización
		add_submenu_page( 'woocommerce',
						'Regulariza Marcas y Modelos',
						'Regulariza Modelos Illantas',
						'manage_options',
						'illantas-regulariza',
						array( $this, 'illantas_admin_regulariza_modelos') );

		// Funcionalidad Shortcodes
		add_submenu_page( 'woocommerce',
				'Shortcodes Filtros Illantas',
				'Shortcodes Filtros Illantas',
				'manage_options',
				'illantas-shortcodes',
				array( $this, 'illantas_admin_screen_shortcode') );
	}

	//-- Muestra la opción de menú de regularización marca modelo
	public function illantas_admin_regulariza_modelos(){
			$rel = new Illantas_Woo_Relations();
			include_once ILLANTAS_DIR . 'admin/partials/illantas-woo-regulariza-display.php';
	}

	//-- Muestra la opción de menú para mostrar información de uso de los shortcodes
	public function illantas_admin_screen_shortcode(){
		include_once ILLANTAS_DIR . 'admin/partials/illantas-woo-screen-shortcode.php';
	}



	// Regulariza los modelos y marcas para nuevos productos
	public function illantas_regulariza_nuevos_ajax(){
		$rel = new Illantas_Woo_Relations();
		$rel->regularizacion_productos_nuevos();
		wp_die();
	}

	// Regulariza las modelos y marcas para productos existentes
	public function illantas_regulariza_existentes_ajax(){
		$rel = new Illantas_Woo_Relations();
		$rel->regularizacion_productos_existentes();
		wp_die();
	}

	// Regulariza la relación de anclaje, modelo y marca en un subsitio
	public function illantas_regulariza_atributos_ajax(){
		$rel = new Illantas_Woo_Relations();
		$rel->regularizacion_relacion_atributos();
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

	// Grabar y Autocompletar Modelos y Marcas de acuerdo al anclaje
	// =================================================================

	// Función para grabar los atributos, llamada por ajax cuando se guarda atributos
	public function illantas_save_attributes(){

		if ( isset ( $_POST['post_id'] ) ){

    		$product_id = absint($_POST['post_id']);
			parse_str($_POST['data'], $data);

			$attr_before = get_post_meta( $product_id, POST_META_ANCLAJE, true ); // recupero los valores guardados anteriormente
			$attr_current = array(); // para recuperar los valores de los anclajes asignados recientemente
			$attr_anclaje = $data['attribute_names']; // para comprobar si tiene atributo anclaje

			if ( in_array( TAX_ANCLAJE , $attr_anclaje ) ){

				//consistencia en caso elimine el atributo y luego lo agregue, siempre recupera el último
				$keys_anclaje = array_keys( $attr_anclaje , TAX_ANCLAJE );
				$index = end( $keys_anclaje );

				//verificar si existen valores de anclaje
				if ( isset( $data['attribute_values'] ) && array_key_exists( $index, $data['attribute_values'] ) ) {
					$attr_current = $data['attribute_values'][$index]; // recupero todos los valores de anclajes
				}
			}

			// Agrego o elimino modelos de acuerdo a la comparación de arrays de anclajes
			$this->prepare_save_attributes( $product_id, $attr_before, $attr_current );

			// actualizo los valores de los anclajes actuales
			update_post_meta( $product_id, POST_META_ANCLAJE, $attr_current );
		}

        wp_send_json_success( true );
		wp_die();
	}


	// Almacenamiento temporal de los modelos de los anclajes agregados en un transient
	private function prepare_save_attributes( $product_id, $arr_before, $arr_after ){

		if ( ! $product_id ) return;

		$rel = new Illantas_Woo_Relations();
		$modelos = Array();

		foreach ( $arr_after as $item ){ //Agregar modelos
			if ( ! in_array( $item, $arr_before ) ){
				$modelos = array_merge( $modelos, $rel->get_modelos_anclaje( $item ) ); // un array continuo de elementos modelos
			}
		}

		$transient_name = TRANSIENT_ANCLAJES_GRABAR . '|' . $product_id;
		set_transient( $transient_name, $modelos, MINUTE_IN_SECONDS*5 );

	}


	// Grabado al final de todo grabado del producto
	public function illantas_update_post_meta( $meta_id, $post_id, $meta_key, $meta_value ){

		if( $meta_key == '_edit_lock' ) {

			$nombre_transient = TRANSIENT_ANCLAJES_GRABAR . '|' . $post_id;
			$modelos = get_transient( $nombre_transient );

			if ( ! $modelos ) return; // validación

			$modelos_anteriores =  wp_get_object_terms( $post_id, TAX_MODELO );

			foreach ($modelos_anteriores as $item) {
				$modelos[] = $item->term_id;
			}

			$rel = new Illantas_Woo_Relations();
			$rel->save_post_meta_attributes( $post_id, $modelos ); //grabar en el post_meta

			// delete_transient( $nombre_transient ); // Eliminamos transient
    	}

	}

	// Agrega información adicional de atributos a los items de orden
	// ========================================================

	public function add_info_attributos_order($item_id, $item, $product){
		$filter = ['diametro', 'et', 'buje', 'fabricante', 'anclaje', 'acabado', 'anchura'];

		if ( $product ){

			$attrs = $product->get_attributes();
			ksort($attrs);

			foreach ($attrs as $key => $value) {

				if ( in_array(substr($key, 3), $filter) ){  // Buscamos a partir del tercer caracter 'pa_'
					$terms_id = $value['data']['options'];

					$term_name = [];
					foreach ($terms_id as $term_id) {
						$term = get_term($term_id, $key);
						$term_name[] = $term->name;
					}

					$result = sprintf("<strong>%s:</strong>%s<br>", substr($key, 3), join(',', $term_name));
					echo $result;
				}
			}
		}
	}




} // class







