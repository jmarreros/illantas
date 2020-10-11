<?php
// para usar la función get_custom_params()
require_once ILLANTAS_DIR . '/helpers/functions.php';

// Mostramos el shortcode

// Validamos que exista WooCommerce
if( ! function_exists('wc_get_products')) {
  return;
}

// Recuperamos la lista de atributos que tiene disponible WooCommerce
$attrs = wc_get_attribute_taxonomies();
$attrs = wp_list_pluck($attrs, 'attribute_name');

ob_start(); //Inicio impresión

require_once 'illantas-woo-filters-display.php'; // Muestra la barra lateral de filtros
require_once 'illantas-woo-products-display.php'; // Muestra la lista de productos

// Imprimimos todo el contenido
echo '<section class="illantas-filter-container">'.ob_get_clean().'</section>';

