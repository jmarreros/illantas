<?php

// Muestra los controles de seleccion para los distintos atributos
// de acuerdo a las dependencias con productos

// Parámetros desde archivo externo
// Funcion get_custom_params
// $param_fabricante
// $param_marca
// $attrs

include_once ILLANTAS_DIR . 'includes/class-illantas-woo-filters.php';



//Creamos el array de valores para los filtros desde la variable $attrs;
$args = array();
foreach ($attrs as $attr) {
    $attr = 'pa_'.$attr;
    if ( ! get_custom_params($attr) ) continue;

    $args[] = get_custom_params($attr);
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
$filtro_selects = new Illantas_Woo_Filters($args);

// Muestra los filtros de acuerdo al orden en el array:  key => Etiqueta
$show_filters = [
    'marca' => 'Marca:', // La marca incluye el modelo
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
    if ( $key !== 'modelo' || ! $param_marca ){ // Si la marca esta en todos o sea diferente del modelo
        echo $filtro_selects->create_generic_select($key);
    } else {
        echo $filtro_selects->create_modelo_select($param_marca); // Sólo cuando una marca esta seleccionada
    }
    echo "</div>";
}

echo " <a class='btn-filter-clean' href='#'>Limpiar</a>";
echo "</section>";