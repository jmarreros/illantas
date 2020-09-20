<?php
include_once ILLANTAS_DIR . 'includes/class-illantas-woo-filters.php';


$filtro_marcas = new Illantas_Woo_Filters();

error_log(print_r($filtro_marcas->create_select_marcas(), true));
// echo $filtro_marcas->create_select_marcas();

