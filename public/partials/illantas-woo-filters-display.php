<?php

// Muestra los controles de seleccion para los distintos atributos
// De acuerdo a las dependencias con productos

include_once ILLANTAS_DIR . 'includes/class-illantas-woo-filters.php';


//Creamos el array de valores para los filtros desde la variable $attrs;
$args = array();
foreach ($attrs as $attr) {
    $attr = 'pa_'.$attr;
    if ( ! get_query_var($attr) ) continue;

    $args[] = get_query_var($attr);
}

// Forzar filtro para una página de marca
if ( $param_marca ){
    $args[] = $param_marca;
}
// Forzar filtro para una página de fabricante
if ( $param_fabricante ){
    $args[] = $param_fabricante;
}

error_log("Fabricante: ". $param_fabricante);

// Creamos la clase pasándo como parámetro los atributos seleccionados
$filtro_marcas = new Illantas_Woo_Filters($args);

// Creamos el select para marca
echo " <strong>Marca</strong>: ".$filtro_marcas->create_generic_select('marca');
echo " <strong>Diámetro</strong> ".$filtro_marcas->create_generic_select('diametro');
echo " <strong>Fabricante</strong> ".$filtro_marcas->create_generic_select('fabricante');
echo " <strong>Anclaje</strong> ".$filtro_marcas->create_generic_select('anclaje');
echo " <strong>Modelo</strong> ".$filtro_marcas->create_generic_select('modelo');



