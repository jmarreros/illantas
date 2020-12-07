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

	// Recupera todos los modelos de una marca pasándole el slug de la marca y devuelve un array asociativo de modelos
	public function get_modelos_marca_by_slug( $slug_marca ){

		// TODO, PENDIENTE DE REVISAR YA QUE PODRÍA TRABAJAR AHORA CON EL SUBSITE AL TENER LOS ATRIBUTOS COMPLETOS
		// Comprobación si es un multisite
		// if ( is_multisite() ) switch_to_blog(1);

		global $wpdb;

		$term_marca = get_term_by('slug', $slug_marca, TAX_MARCA);
		$id_marca = $term_marca->term_id;

		$query = "SELECT t.name, t.slug FROM {$wpdb->term_taxonomy} tt
					INNER JOIN {$wpdb->terms} t USING(term_id)
					INNER JOIN {$wpdb->termmeta} tm USING(term_id)
					WHERE tt.taxonomy = '".TAX_MODELO."'
					AND tm.meta_key = '".TERM_META_MARCA."'
					AND tm.meta_value = {$id_marca}
					ORDER BY t.name";


		$result = $wpdb->get_results( $query , ARRAY_A);

		// if ( is_multisite() ) restore_current_blog();

		return $result;
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

		// Validación si no hay modelos no hacer nada
		if ( empty($modelos) ) return

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

		// Actualizamos flag de producto nuevo sólo cuando hay datos
		update_post_meta( $post_id, PRODUCT_EXIST, true );

		// Nos aseguramos de que tenga tipo de producto simple
		wp_set_object_terms($post_id, 2, 'product_type');
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

		}

	}


	// Regulariza los modelos y marcas de los anclajes para nuevos productos
	public function regularizacion_productos_existentes(){
		global $wpdb;

		$id_modelo 	= intval($_REQUEST['id_modelo']);
		$id_marca 	= intval($_REQUEST['id_marca']);
		$id_anclaje	= intval($_REQUEST['id_anclaje']);

		// validación de valores y consistencia en relación
		if ( ! $id_modelo  || $id_anclaje != get_term_meta( $id_modelo, TERM_META_ANCLAJE, true ) ) return false;

		// Obtenemos los productos que tienen ese anclaje $id_anclaje
		$table_relationship_taxonomy = $wpdb->prefix.'term_relationships';
		$table_termtaxonomy = $wpdb->prefix.'term_taxonomy';

		$sql = $wpdb->prepare("SELECT object_id as id FROM {$table_relationship_taxonomy} tr
					INNER JOIN {$table_termtaxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
					WHERE tt.term_id = %d", $id_anclaje);

		$results = $wpdb->get_results($sql);

		foreach ($results as $item) {
			$product = wc_get_product( $item->id );

			$this->add_term_product( $product, TAX_MODELO, $id_modelo );
			$this->add_term_product( $product, TAX_MARCA, $id_marca );
		}

		return count($results);
	}

	// Función para agregar la marca y modelo al producto
	private function add_term_product( $product, $taxonomy, $term_id){

		$id_product = $product->get_id();
		$attributes = (array) $product->get_attributes();

		// Verificamos si existe la taxonomia en el producto
		if ( array_key_exists( $taxonomy, $attributes ) ){

			foreach( $attributes as $key => $attribute ){
				if( $key == $taxonomy ){
					$options = (array) $attribute->get_options();
					$options[] = $term_id;
					$attribute->set_options($options);
					$attributes[$key] = $attribute;
					break;
				}
			}
			$product->set_attributes( $attributes );

		} else { //sino existe la taxonomia

			$attribute = new WC_Product_Attribute();

			$attribute->set_id( sizeof( $attributes) + 1 );
			$attribute->set_name( $taxonomy );
			$attribute->set_options( array( $term_id ) );
			$attribute->set_position( sizeof( $attributes) + 1 );
			$attribute->set_visible( true );
			$attribute->set_variation( false );
			$attributes[] = $attribute;

			$product->set_attributes( $attributes );

		}

		$product->save();

		if( ! has_term( $term_id, $taxonomy, $id_product )){
			wp_set_object_terms($id_product, $term_id, $taxonomy, true );
		}

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

	// Regulariza la relación de Modelo - Anclaje y Marca en el subsitio en base a los datos del sitio principal
	public function regularizacion_relacion_atributos(){

		// Comprobación si estamos en un subsite
		if ( is_multisite() && ! is_main_site()  ){

			$rel_modelo = $this->get_relations_modelo();

			$list_modelos = $this->get_modelos();
			$list_marcas = $this->get_marcas();
			$list_anclajes = $this->get_anclajes();
			// Pasamos a array
			$list_marcas = wp_list_pluck( $list_marcas, 'name', 'term_id' );
			$list_anclajes = wp_list_pluck( $list_anclajes, 'name', 'term_id' );
			$list_modelos = wp_list_pluck( $list_modelos, 'name', 'term_id' );

			$name_modelo = '';
			$id_modelo = 0;

			foreach ($rel_modelo as $item) {

				if ( $name_modelo !== $item->modelo){
					$name_modelo = $item->modelo;
					$id_modelo = array_search($name_modelo, $list_modelos); // Encontramos el id del modelo
				}
				$valor = $item->valor; // valor de marca o anclaje

				// Sino existe el modelo lo creamos
				if ( ! $id_modelo ) {
					$term_insert = wp_insert_term( $name_modelo, TAX_MODELO);
					$id_modelo = $term_insert['term_id'];
				};

				// Para el id_modelo asignamos el valor de sel-marca y sel-anclaje
				switch ($item->relacion) {
					case TERM_META_MARCA:
						// Obtenemos el ID de la marca
						$id_marca = array_search($valor, $list_marcas);
						if ( ! $id_marca ) {
							$term_insert = wp_insert_term( $valor, TAX_MARCA);
							$id_marca = $term_insert['term_id'];
							$list_marcas[$id_marca] = $valor;
						};
						update_term_meta($id_modelo, TERM_META_MARCA, $id_marca);
						break;
					case TERM_META_ANCLAJE:
						// Obtenemos el id del anclaje
						$id_anclaje = array_search($valor, $list_anclajes);
						if ( ! $id_anclaje ) {
							$term_insert = wp_insert_term( $valor, TAX_ANCLAJE);
							$id_anclaje = $term_insert['term_id'];
							$list_anclajes[$id_anclaje] = $valor;
						};
						update_term_meta($id_modelo, TERM_META_ANCLAJE, $id_anclaje);
						break;
					default:
						break;
				}

			}

		}

		return true;
	}

	// Función auxiliar para obtener los datos de la relación modelo - anclaje y marca del sitio principal
	private function get_relations_modelo(){
		global $wpdb;

		//---> Cambiamos temporalmente al sitio principal
		switch_to_blog( get_main_site_id() );

		$table_taxonomy = $wpdb->prefix.'term_taxonomy';
		$table_termmeta = $wpdb->prefix.'termmeta';
		$table_terms 	= $wpdb->prefix.'terms';

		$sql = $wpdb->prepare("SELECT t.name as modelo, tm.meta_key as relacion, tmv.name as valor FROM {$table_taxonomy} tt
			INNER JOIN {$table_terms} t ON t.term_id = tt.term_id
			INNER JOIN {$table_termmeta} tm ON tm.term_id = t.term_id
			INNER JOIN {$table_terms} tmv ON tmv.term_id = tm.meta_value
			WHERE tt.taxonomy = '%s' AND tm.meta_key in ('%s', '%s')", TAX_MODELO, TERM_META_MARCA, TERM_META_ANCLAJE);

		$results = $wpdb->get_results($sql);

		//---> Cambiamos nuevamente al sitio principal
		restore_current_blog();

		return $results;
	}

} // class



	// $modelos = $this->get_modelos_producto($id_product); //modelos anteriores
	// $modelos[] = $id_modelo;
	// $this->save_post_meta_attributes( $id_product, $modelos );




	// // Por cada modelo añadido agregamos su anclaje correspondiente
	// private function get_anclas_modelos( $modelos){

	// 	$anclajes = array();
	// 	foreach($modelos as $modelo){
	// 		 $val =  (int)get_term_meta( $modelo, TERM_META_ANCLAJE, true );
	// 		 if ( $val ) $anclajes[] = $val;
	// 	}

	// 	return $anclajes;
	// }



// $results = $wpdb->get_results($sql);

// return;
// // TODO: Sólo me interesa los productos que tengan el anclaje id_anclaje ---
// $products = $this->get_products();

// error_log(print_r($products,true));

// //Recorremos todos los productos obtenidos
// foreach ( $products as $product ){

// 	$id_product = $product->ID;

// 	$term_anclaje_producto = get_the_terms($id_product, TAX_ANCLAJE);

// 	foreach( $term_anclaje_producto as $item ){

// 		// Si el producto tiene un anclaje igual a id_anclaje
// 		if ( $item->term_id == $id_anclaje  ) {
// 			$modelos = $this->get_modelos_producto($id_product); //modelos anteriores
// 			$modelos[] = $id_modelo;
// 			$this->save_post_meta_attributes( $id_product, $modelos );
// 			break;
// 		}

// 	}// term_anclaje_producto

// }// product

// return true;



	// $id_product = $product->id;
	// $modelos = $this->get_modelos_producto($id_product); //modelos anteriores
	// $modelos[] = $id_modelo;
	// $this->save_post_meta_attributes( $id_product, $modelos );

