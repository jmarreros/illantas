<?php

// Funcion personalizada para obtener los parámetros
function get_custom_params($param){
  if ( isset($_GET[$param]) ) return $_GET[$param];
  else return false;
}


// Para obtener los parámetros de orden
function get_ordering(){
  $ordering = [];

  $ordering['meta_key'] = '_price';
  $ordering['order'] = 'asc';
  $ordering['orderby'] = 'meta_value_num';

  if (!isset($_GET['orderby'])) return $ordering;

  switch ($_GET['orderby']) {
    case 'popularity':
      $ordering['meta_key'] = 'total_sales';
      $ordering['order'] = 'desc';
      $ordering['orderby'] = 'meta_value_num';
      break;
    case 'rating':
      $ordering['meta_key'] = '_wc_average_rating';
      $ordering['order'] = 'desc';
      $ordering['orderby'] = 'meta_value_num';
      break;
    case 'date':
      $ordering['meta_key'] = '';
      $ordering['order'] = 'desc';
      $ordering['orderby'] = 'date';
      break;
    case 'price-desc':
      $ordering['meta_key'] = '_price';
      $ordering['order'] = 'desc';
      $ordering['orderby'] = 'meta_value_num';
      break;
  }

  return $ordering;
}