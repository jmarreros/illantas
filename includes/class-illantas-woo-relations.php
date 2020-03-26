<?php

/**
 * Class for manage relation marca / modelo
 *
 * Gestiona los datos de marcas y modelos de la tabla wp_illantas_relations
 *
 * @since 			1.0.0
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/includes
 * @author 			jmarreros
 */
class Illantas_Woo_Relations {

	// Recupera todas las anclas
	public function get_anclajes(){
		$terms_anclaje = get_terms( [ 'taxonomy' => TAX_ANCLAJE, 'hide_empty' => false ]);
		return $terms_anclaje;
	}

	// Recupera todos las marcas
	public function get_marcas(){
		$terms_marca = get_terms( [ 'taxonomy' => TAX_MARCA, 'hide_empty' => false ]);
		return $terms_marca;
	}

	// Recupera todos los modelos
	public function get_modelos(){
		$terms_modelo = get_terms( [ 'taxonomy' => TAX_MODELO, 'hide_empty' => false ]);
		return $terms_modelo;
	}


	// ---- Anteriores ---

	// Recupera todos los modelos de una marca pasandole el id
	public function get_modelos_marca( $id_marca ){

		$terms_modelo = $this->get_modelos();
		$modelos_marca = array();

		// Buscamos en todos los modelos si tiene el meta de marca
		foreach ($terms_modelo as $item) {
			$marca = get_term_meta( $item->term_id, TERM_META_MARCA, true );

			if ( $marca == $id_marca ){ // sólo es un valor
				$modelos_marca[] = $item->term_id;
			}
		}

		return $modelos_marca;
	}

	// Recupera todos los modelos de un anclaje pasandole el id
	public function get_modelos_anclaje( $id_anclaje ){

		$terms_modelo = $this->get_modelos();
		$modelos_anclaje = array();

		// Buscamos en todos los modelos si tiene el meta de marca
		foreach ($terms_modelo as $item) {
			$anclaje = get_term_meta( $item->term_id, TERM_META_ANCLAJE, true );

			if ( $anclaje == $id_anclaje ){ // sólo es un valor
				$modelos_anclaje[] = $item->term_id;
			}
		}

		return $modelos_anclaje;
	}


	// Obtiene todos los modelos relacionados con su anclaje al que pertenece
	// en formato de array multidimensional con el key inicial como anclaje
	// $modelo_anclaje[anclaje][]=modelo
	public function get_all_modelos_anclaje(){
		$terms_modelo = $this->get_modelos();
		$modelo_anclaje = array();

		foreach($terms_modelo as $item){
			$anclaje =  (int)get_term_meta( $item->term_id, TERM_META_ANCLAJE, true );
			if ( $anclaje ){
				$modelo_anclaje[$anclaje][] = $item->term_id;
			}
		}

		return $modelo_anclaje;
	}

	// Obtiene un array de modelos relacionados con el anclaje que tiene
	public function get_modelos_meta_anclaje(){
		$modelos = $this->get_modelos();
		$modelos_meta_anclaje = array();

		foreach($modelos as $modelo){
			$anclaje =  (int)get_term_meta( $modelo->term_id, TERM_META_ANCLAJE, true );
			if ( $anclaje ){
				$modelos_meta_anclaje[$modelo->term_id] = $anclaje;
			}
		}

		return $modelos_meta_anclaje;
	}


	// Obtiene todos los modelos relacionados con su marca a la que pertenece
	// en formato de array multidimensional con el key inicial como marca
	// $modelo_marca[marca][]=modelo
	public function get_all_modelos_marca(){
		$terms_modelo = $this->get_modelos();
		$modelo_marca = array();

		foreach($terms_modelo as $item){
			$marca =  (int)get_term_meta( $item->term_id, TERM_META_MARCA, true );
			if ( $marca ){
				$modelo_marca[$marca][] = $item->term_id;
			}

		}

		return $modelo_marca;
	}



	// Grabar datos en atributos de WooCommerce también en el post_meta
	public function save_post_meta_attributes($post_id, $modelos){

		$meta_data_post = Array();

		// Obtenemos los atributos guardados por Woocommerce
		$meta_tmp = get_post_meta( $post_id, '_product_attributes', true );

		foreach ($meta_tmp as $item) {
			$meta_data_post[ $item['name'] ] = $item; // Guardamos los atributos en un array
		}

		wp_set_object_terms( $post_id, $modelos, TAX_MODELO ); // agregamos los atributos de modelo marcas
		$meta_data_post[TAX_MODELO] = [ 'name'=> TAX_MODELO,
										'value'=> $modelos,
										'is_visible' => '1',
										'position' => '2',
										'is_variation' => '0',
										'is_taxonomy' => '1' ];

		// -- Grabar Anclajes

		// Obtenemos todos las marcas de los modelos
		$marcas = $this->get_marcas_modelos( $modelos );

		if ( ! empty($marcas) ){
			wp_set_object_terms( $post_id, $marcas, TAX_MARCA ); // agregamos los atributos de modelos anclajes
			$meta_data_post[TAX_MARCA] = [ 'name'=> TAX_MARCA,
											'value'=> $marcas,
											'is_visible' => '1',
											'position' => '3',
											'is_variation' => '0',
											'is_taxonomy' => '1' ];
		}
		// -- Fin Grabar Anclaje

		update_post_meta( $post_id, '_product_attributes', $meta_data_post );
		update_post_meta( $post_id, PRODUCT_EXIST, true );
	}


	// Función que por cada modelo añadido agregamos su marca correspondiente
	private function get_marcas_modelos( $modelos ){
		$marcas = array();
		foreach($modelos as $modelo){
			$val = (int)get_term_meta( $modelo, TERM_META_MARCA, true );
			if ($val) $marcas[] = $val;
		}
		return $marcas;
	}

	// Get all products
	private function get_products(){
		$args     = [
			'post_type' => 'product',
			'posts_per_page' => -1
			];
		return get_posts( $args );
	}

	// Obtenemos los nuevos ids de productos nuevos, basándonos en su valor de metadata PRODUCT_EXIST
	private function get_new_products(){
		global $wpdb;

		$table_posts = $wpdb->prefix.'posts';
		$table_meta = $wpdb->prefix.'postmeta';

		$sql = $wpdb->prepare("SELECT id FROM {$table_posts} WHERE post_type='product' AND id not in (
			SELECT post_id FROM {$table_meta} WHERE meta_key='%s' AND meta_value='1')", PRODUCT_EXIST);

		$values = $wpdb->get_col( $sql );

		return $values;
	}

	// Regulariza los modelos y marcas de los anclajes para nuevos productos
	public function regularizacion_productos_nuevos(){

		//para un anclaje específi a $modelos_anclaje[anclaje] retorna un array() de modelos
		$modelos_anclaje = $this->get_all_modelos_anclaje();

		$id_products = $this->get_new_products();

		//Recorremos todos los productos obtenidos
		foreach ( $id_products as $id_product ){

			//recupero todas las marcas por producto
			$term_anclaje_producto = get_the_terms($id_product, TAX_ANCLAJE);

			// Inicializa el array de modelos y limpia el atributo, para poner la función como append = true
			$modelos = array();
			wp_set_object_terms( $id_product, $modelos, TAX_MODELO );

			// Obtengo todos los modelos de todas los anclajes para el producto
			foreach($term_anclaje_producto as $item){
				if ( array_key_exists( $item->term_id, $modelos_anclaje ) ){
					$modelos = array_merge( $modelos, $modelos_anclaje[$item->term_id]);
				}
			}

			//Actualizo el atributo de modelos
			//wp_set_object_terms( $id_product, $modelos, TAX_MODELO);

			$this->save_post_meta_attributes( $id_product, $modelos ); //grabar en el post_meta

			//Actualizamos estado de la meta para la validación
			update_post_meta( $id_product, PRODUCT_EXIST, true );
		}

	}


	// Regulariza los modelos y marcas de los anclajes para nuevos productos
	public function regularizacion_productos_existentes(){

		$id_modelo = intval($_REQUEST['id_modelo']);
		$id_anclaje = intval($_REQUEST['id_anclaje']);

		// validación de valores y consistencia en relación
		if ( ! $id_modelo  || $id_anclaje != get_term_meta( $id_modelo, TERM_META_ANCLAJE, true ) ) return false;

		$products = $this->get_products();

		//Recorremos todos los productos obtenidos
		foreach ( $products as $product ){

			$id_product = $product->ID;

			$term_anclaje_producto = get_the_terms($id_product, TAX_ANCLAJE);

			foreach( $term_anclaje_producto as $item ){

				// Si el producto tiene un anclaje igual a id_anclaje
				if ( $item->term_id == $id_anclaje  ) {
					$modelos = $this->get_modelos_producto($id_product); //modelos anteriores
					$modelos[] = $id_modelo;
					$this->save_post_meta_attributes( $id_product, $modelos );
					break;
				}

			}// term_anclaje_producto

		}// product

		return true;
	}

	// Función que recupera los modelos actuales de un producto
	private function get_modelos_producto($id_product){
		$modelos = array();

		$term_modelos_producto = get_the_terms($id_product, TAX_MODELO);

		foreach ($term_modelos_producto as $item){
			$modelos[] = $item->term_id;
		}

		return $modelos;
	}


} // class





	// // Por cada modelo añadido agregamos su anclaje correspondiente
	// private function get_anclas_modelos( $modelos){

	// 	$anclajes = array();
	// 	foreach($modelos as $modelo){
	// 		 $val =  (int)get_term_meta( $modelo, TERM_META_ANCLAJE, true );
	// 		 if ( $val ) $anclajes[] = $val;
	// 	}

	// 	return $anclajes;
	// }