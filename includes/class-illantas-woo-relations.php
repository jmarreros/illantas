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


} // class
