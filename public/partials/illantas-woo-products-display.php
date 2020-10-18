<?php
// Construimos la consulta de las taxonomias para filtra atributos en base a los argumentos
// de la url de la variable $attrs y mostramos la lista de productos

// Parámetros desde archivos externos
// Funcion get_custom_params
// $attrs
// $param_fabricante
// $param_marca

$tax_query = null;

foreach ($attrs as $attr) {
  $attr = 'pa_'.$attr;
  if ( ! get_custom_params($attr) ) continue;

  $tax_query[] = [
    'taxonomy'  => $attr,
    'field'     => 'slug',
    'terms'     => get_custom_params($attr)
  ];
}

// Estamos en alguna página de marca, forzamos filtro
if ( $param_marca ){
  $tax_query[] = [
    'taxonomy'  => 'pa_marca',
    'field'     => 'slug',
    'terms'     => $param_marca
  ];
}
// Estamos en alguna página de fabricante, forzamos filtro
if ( $param_fabricante ){
  $tax_query[] = [
    'taxonomy'  => 'pa_fabricante',
    'field'     => 'slug',
    'terms'     => $param_fabricante
  ];
}

$paged                = (get_query_var('paged') || get_query_var('page')) ? absint(get_query_var('paged')) + absint(get_query_var('page')) : 1;
$ordering             = get_ordering();
$products_per_page    = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

$sel_products       = wc_get_products(array(
  'status'               => 'publish',
  'limit'                => $products_per_page,
  'page'                 => $paged,
  'paginate'             => true,
  'return'               => 'ids',
  'meta_key'             => $ordering['meta_key'],
  'orderby'              => $ordering['orderby'],
  'order'                => $ordering['order'],
  'tax_query'            => array(
    'relation' => 'AND',
    $tax_query
  ),
));

// Muestra la lista de produtos
echo "<section class='illantas-filter-products'>";

echo "<div id='fixed-sidebar-aux' class='illantas-products-count'>";
if ( $sel_products->total > 1 ){
  echo "Te podemos ofrecer <span> ". $sel_products->total ." productos </span> disponibles";
}
echo "</div>";

wc_set_loop_prop('current_page', $paged);
wc_set_loop_prop('is_paginated', wc_string_to_bool(true));
wc_set_loop_prop('page_template', get_page_template_slug());
wc_set_loop_prop('per_page', $products_per_page);
wc_set_loop_prop('total', $sel_products->total);
wc_set_loop_prop('total_pages', $sel_products->max_num_pages);


if($sel_products) {
  do_action('woocommerce_before_shop_loop');
  woocommerce_product_loop_start();

  foreach($sel_products->products as $product) {
    $post_object = get_post($product);
    setup_postdata($GLOBALS['post'] =& $post_object);
    wc_get_template_part('content', 'product');
  }

  wp_reset_postdata();
  woocommerce_product_loop_end();
  do_action('woocommerce_after_shop_loop');

} else {
  do_action('woocommerce_no_products_found');
}

echo "</section>";



// $ordering                = WC()->query->get_catalog_ordering_args();
// $arr_orderby             = explode(' ', $ordering['orderby']);
// $ordering['orderby']     = array_shift($arr_orderby);
// $ordering['orderby']     = stristr($ordering['orderby'], 'price') ? 'meta_value_num' : $ordering['orderby'];


// error_log('===== Sel_1 =====');
// error_log(print_r($sel_products,true));

// $args = array(
//   'post_type'       => 'product',
//   'post_status'     => 'publish',
//   'posts_per_page'  => $products_per_page,
//   'paged'           => $paged,
//   'fields'          => 'ids',
//   'meta_key'        => '_price',
//   'orderby'         => $ordering['orderby'],
//   'order'           => $ordering['order'],
//   'tax_query'       => array(
//     'relation' => 'AND',
//     $tax_query
//   ),
// );

// $sel_products2 = new WP_Query( $args );

// error_log('===== Sel_2 =====');
// error_log(print_r($sel_products2->posts,true));
// error_log($sel_products2->found_posts);
// error_log($sel_products2->max_num_pages);
