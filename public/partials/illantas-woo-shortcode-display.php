<?php

  if( ! function_exists('wc_get_products')) {
    return;
  }


  // Construimos los la consulta de las taxonomias para filtra atributos
  // en la consulta principal en base a los argumentos de la url
  $tax_query = array();
  $attrs = wc_get_attribute_taxonomies();
  $attrs = wp_list_pluck($attrs, 'attribute_name');

  foreach ($attrs as $attr) {
    $attr = 'pa_'.$attr;
    if ( ! get_query_var($attr) ) continue;

    $tax_query[] = [
      'taxonomy'  => $attr,
      'field'     => 'slug',
      'terms'     => get_query_var($attr)
    ];
  }

  $paged                   = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
  $ordering                = WC()->query->get_catalog_ordering_args();
  $ordering['orderby']     = array_shift(explode(' ', $ordering['orderby']));
  $ordering['orderby']     = stristr($ordering['orderby'], 'price') ? 'meta_value_num' : $ordering['orderby'];
  $products_per_page       = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

  $sel_products       = wc_get_products(array(
    'status'               => 'publish',
    'limit'                => $products_per_page,
    'page'                 => $paged,
    'paginate'             => true,
    'return'               => 'ids',
    'orderby'              => $ordering['orderby'],
    'order'                => $ordering['order'],
    'tax_query'            => array(
      'relation' => 'AND',
      $tax_query
    ),
  ));

  // Mostrar Filtros
  require_once 'illantas-woo-filters-display.php';

  echo "<hr>";
  echo "Total productos: ". $sel_products->total;

  wc_set_loop_prop('current_page', $paged);
  wc_set_loop_prop('is_paginated', wc_string_to_bool(true));
  wc_set_loop_prop('page_template', get_page_template_slug());
  wc_set_loop_prop('per_page', $products_per_page);
  wc_set_loop_prop('total', $sel_products->total);
  wc_set_loop_prop('total_pages', $sel_products->max_num_pages);


  if($sel_products) {

    ob_start();

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

echo ob_get_clean();


// tax_query exmample
// ===================
// [
//   'taxonomy'  => 'pa_marca',
//   'field'     => 'slug',
//   'terms'     => 'ford'
// ],
// [
//   'taxonomy'  => 'pa_anclaje',
//   'field'     => 'slug',
//   'terms'     => '5x100'
// ],
// [
//   'taxonomy'  => 'pa_diametro',
//   'field'     => 'slug',
//   'terms'     => '17-0'
// ],