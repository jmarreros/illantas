<?php

/**
 * The plugin bootstrap file
 *
 * @link 				https://mundollantas.es
 * @since 				1.0.0
 * @package 			Illantas_Woo
 *
 * @wordpress-plugin
 * Plugin Name: 		Illantas Woo
 * Plugin URI: 			https://mundollantas.es/
 * Description: 		Plugins para mostrar relaciones entre marcas y modelos en productos Woocommerce
 * Version: 			1.4.0
 * Author: 				jmarreros
 * Author URI: 			https://decodecms.com
 * License: 			GPL-2.0+
 * License URI: 		http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 		illantas-woo
 * Domain Path: 		/languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// PLUGIN CONSTANS
//===============

define ('VERSION_PLUGIN', '1.4');

define( 'ILLANTAS_DIR', plugin_dir_path( __FILE__ ) );
define( 'ILLANTAS_URL', plugin_dir_url( __FILE__ ) );

define( 'TAX_MARCA', 'pa_marca');
define( 'TAX_MODELO', 'pa_modelo');
define( 'TAX_ANCLAJE', 'pa_anclaje');

define( 'TERM_META_MARCA', 'sel-marcas');
define( 'TERM_META_ANCLAJE', 'sel-anclajes');

define( 'POST_META_MARCA', '_saved-marcas' );
define( 'POST_META_ANCLAJE', '_saved-anclajes' );

define( 'TRANSIENT_MARCAS_GRABAR', 'illantas_marcas_grabar');
define( 'TRANSIENT_ANCLAJES_GRABAR', 'illantas_anclajes_grabar');

define( 'PRODUCT_EXIST', '_producto_existente' );

//Shortcode

define( 'SHORTCODE_NAME', 'filtroIllantas' );

// Página base, adicional al home
define( 'BASE_PAGE_SHORTCODE', '/llantas' );

//===============


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-illantas-woo-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 		1.0.0
 */
function run_illantas_woo() {

	$plugin = new Illantas_Woo();
	$plugin->run();

}
run_illantas_woo();
