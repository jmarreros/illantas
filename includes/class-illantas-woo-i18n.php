<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link 			https://mundollantas.es
 * @since 			1.0.0
 *
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since 			1.0.0
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/includes
 * @author 			jmarreros
 */
class Illantas_Woo_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 		1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'illantas-woo',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	} // load_plugin_textdomain()

} // class
