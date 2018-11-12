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

		// Obtenemos todos los anclajes de los modelos
		$anclajes = $this->get_anclas_modelos( $modelos );
		if ( ! empty($anclajes) ){
			wp_set_object_terms( $post_id, $anclajes, TAX_ANCLAJE ); // agregamos los atributos de modelos anclajes
			$meta_data_post[TAX_ANCLAJE] = [ 'name'=> TAX_ANCLAJE,
											'value'=> $anclajes,
											'is_visible' => '1',
											'position' => '2',
											'is_variation' => '0',
											'is_taxonomy' => '1' ];
		}
		// -- Fin Grabar Anclaje

		update_post_meta( $post_id, '_product_attributes', $meta_data_post );
		update_post_meta( $post_id, PRODUCT_EXIST, true );
	}


	// Por cada modelo añadido agregamos su anclaje correspondiente
	private function get_anclas_modelos( $modelos){

		$anclajes = array();
		foreach($modelos as $modelo){
			 $val =  (int)get_term_meta( $modelo, TERM_META_ANCLAJE, true );
			 if ( $val ) $anclajes[] = $val;
		}

		return $anclajes;
	}

	// Regulariza los modelos de las marcas para nuevos productos
	public function regularizacion_marcas_modelos(){

		$modelos_marca = $this->get_all_modelos_marca();  //para una marca específi a $modelos_marca[marca] retorna un array() de modelos

		// Obtenemos todos los productos, solo me interesa el id del producto
		$args     = [
					'post_type' => 'product',
					'posts_per_page' => -1
					];
		$products = get_posts( $args );


		//Recorremos todos los productos obtenidos
		foreach ( $products as $product ){

			$id_product = $product->ID;

			//Validamos para saber si es un nuevo producto
			if ( get_post_meta( $id_product, PRODUCT_EXIST, true) ) {
				continue;
			}

			//recupero todas las marcas por producto
			$term_marcas_producto = get_the_terms($id_product, TAX_MARCA);

			// Inicializa el array de modelos y limpia el atributo, para poner la función como append = true
			$modelos = array();
			wp_set_object_terms( $id_product, $modelos, TAX_MODELO );

			// Obtengo todos los modelos de todas las marcas para el producto
			foreach($term_marcas_producto as $item){
				if ( array_key_exists( $item->term_id, $modelos_marca ) ){
					$modelos = array_merge( $modelos, $modelos_marca[$item->term_id]);
				}
			}

			//Actualizo el atributo de modelos
			wp_set_object_terms( $id_product, $modelos, TAX_MODELO);

			$this->save_post_meta_attributes( $id_product, $modelos ); //grabar en el post_meta

			//Actualizamos estado de la meta para la validación
			update_post_meta( $id_product, PRODUCT_EXIST, true );
		}

	}


} // class
