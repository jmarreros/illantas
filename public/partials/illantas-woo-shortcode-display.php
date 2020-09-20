<?php

if( ! function_exists('wc_get_products')) {
    return;
  }

  // Parámetros de filtro
  $anclaje = get_query_var('anclaje', 'todos');
  $diametro = get_query_var('diametro', 'todos');

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
      // [
      //   'taxonomy'  => 'pa_marca',
      //   'field'     => 'slug',
      //   'terms'     => 'ford'
      // ],
      [
        'taxonomy'  => 'pa_anclaje',
        'field'     => 'slug',
        'terms'     => '5x100'
      ],
      [
        'taxonomy'  => 'pa_diametro',
        'field'     => 'slug',
        'terms'     => '17-0'
      ],
    ),
  ));


    // Mostrar Filtros
    require_once 'illantas-woo-filters-display.php';

echo "<hr>";
    // echo "Anclaje:" . $anclaje . " Diametro:" .$diametro;
  echo "Total productos: ". $sel_products->total;

  wc_set_loop_prop('current_page', $paged);
  wc_set_loop_prop('is_paginated', wc_string_to_bool(true));
  wc_set_loop_prop('page_template', get_page_template_slug());
  wc_set_loop_prop('per_page', $products_per_page);
  wc_set_loop_prop('total', $sel_products->total);
  wc_set_loop_prop('total_pages', $sel_products->max_num_pages);



  // Probamos


  $tax_query  = WC_Query::get_main_tax_query();
  $meta_query = WC_Query::get_main_meta_query();

  error_log(print_r($tax_query->sql, true));

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


// Todo Revisar función
function get_filtered_term_product_counts( $term_ids, $taxonomy, $query_type ) {
  global $wpdb;

  $tax_query  = WC_Query::get_main_tax_query();
  $meta_query = WC_Query::get_main_meta_query();

  if ( 'or' === $query_type ) {
    foreach ( $tax_query as $key => $query ) {
      if ( is_array( $query ) && $taxonomy === $query['taxonomy'] ) {
        unset( $tax_query[ $key ] );
      }
    }
  }

  $meta_query     = new WP_Meta_Query( $meta_query );
  $tax_query      = new WP_Tax_Query( $tax_query );
  $meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
  $tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

  // Generate query
  $query           = array();
  $query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
  $query['from']   = "FROM {$wpdb->posts}";
  $query['join']   = "
  INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
  INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
  INNER JOIN {$wpdb->terms} AS terms USING( term_id )
  " . $tax_query_sql['join'] . $meta_query_sql['join'];

  $query['where'] = "
  WHERE {$wpdb->posts}.post_type IN ( 'product' )
  AND {$wpdb->posts}.post_status = 'publish'
  " . $tax_query_sql['where'] . $meta_query_sql['where'] . "
  AND terms.term_id IN (" . implode( ',', array_map( 'absint', $term_ids ) ) . ")
";

  if ( $search = WC_Query::get_main_search_query_sql() ) {
    $query['where'] .= ' AND ' . $search;
  }

  $query['group_by'] = "GROUP BY terms.term_id";
  $query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
  $query             = implode( ' ', $query );
  $results           = $wpdb->get_results( $query );

  error_log(print_r($results,true));

  return wp_list_pluck( $results, 'term_count', 'term_count_id' );
}