<?php

/**
 * Fired during plugin activation
 *
 * @link 			https://mundollantas.es
 * @since 			1.0.0
 *
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since 			1.0.0
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/includes
 * @author 			jmarreros
 */
class Illantas_Woo_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since 		1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$table = $wpdb->prefix . ILLANTAS_TABLE;


		$charset_collate = $wpdb->get_charset_collate();
		$sql = '';

		if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
			$sql = "CREATE TABLE $table (
					id_marca INT NOT NULL,
					id_modelo INT NOT NULL,
					CONSTRAINT PK_Summary PRIMARY KEY ( id_marca, id_modelo )
				) $charset_collate;";
		}

		if ( ! empty( $sql) ){
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

	} // activate()


} // class
