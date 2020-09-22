<?php

// Muestra los controles de seleccion para los distintos atributos
// de acuerdo a las dependencias con productos

// Parámetros desde archivo externo
// $param_fabricante
// $param_marca
// $attrs

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

// Creamos la clase pasándo como parámetro los atributos seleccionados
$filtro_marcas = new Illantas_Woo_Filters($args);

// Muestra los filtros de acuerdo al orden en el array:  key => Etiqueta
$show_filters = [
    'marca' => 'Marca:',
    'modelo' => 'Modelo:',
    'diametro' => 'Diámetro:',
    'anchura' => 'Ancho:',
    'acabado' => 'Acabado:',
    'fabricante' => 'Fabricante:',
];

// Imprimimos la sección de filtros
echo "<section class='illantas-filter-sidebar'>";
echo "<h3>Compara y compra la mejor llanta de coche</h3>";

foreach ($show_filters as $key => $value) {
    echo "<div class='illantas-filter'>";
    echo "<label>".$value."</label>";
    echo $filtro_marcas->create_generic_select($key);
    echo "</div>";
}

echo " <a class='btn-filter-clean' href='#'>Limpiar</a>";
echo "</section>";