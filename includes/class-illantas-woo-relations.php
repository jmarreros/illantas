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
			$marca = get_term_meta( $item->term_id, TERM_META, true );

			if ( $marca == $id_marca ){ // sólo es un valor
				$modelos_marca[] = $item->term_id;
			}
		}

		return $modelos_marca;
	}


	// public function get_modelos_marca( $id_marca ){
	// 	global $wpdb;

	// 	$table_rel = $wpdb->prefix . ILLANTAS_TABLE;
	// 	$table_terms = $wpdb->prefix . "terms";

	// 	$query = "SELECT ir.id_term, t.name, ir.modelo FROM $table_rel  ir
	// 			INNER JOIN $table_terms t ON t.term_id = ir.id_term WHERE ir.id_term = $id_marca";

	// 	return $wpdb->get_results( $query );
	// }
	// Mantenimiento de la tabla wp_illantas_relations
	// ==============================================

	// public function get_modelos_marca( $id_marca ) {
	// 	global $wpdb;

	// 	$table_rel = $wpdb->prefix . ILLANTAS_TABLE;
	// 	$table_terms = $wpdb->prefix . "terms";

	// 	$query = "SELECT ir.id_term, t.name, ir.modelo FROM $table_rel  ir
	// 			INNER JOIN $table_terms t ON t.term_id = ir.id_term WHERE ir.id_term = $id_marca";

	// 	return $wpdb->get_results( $query );

	// } // activate()


	// // Save new models
	// public function save_modelo( $id_marca, $modelo ){
	// 	global $wpdb;

	// 	$table_rel = $wpdb->prefix . ILLANTAS_TABLE;

	// 	$data['id_term'] = $id_marca;
	// 	$data['modelo'] = $modelo;

	// 	return $wpdb->replace( $table_rel, $data );
	// }


	// // Delete model
	// public function delete_modelo( $id_marca, $modelo ){
	// 	global $wpdb;

	// 	$table_rel = $wpdb->prefix . ILLANTAS_TABLE;

	// 	$data['id_term'] = $id_marca;
	// 	$data['modelo'] = $modelo;

	// 	return $wpdb->delete( $table_rel, $data );
	// }

	// Relaciones con tablas Woocommerce
	// =================================




} // class
