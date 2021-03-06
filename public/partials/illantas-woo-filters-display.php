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

    $args[$attr] = get_custom_params($attr);
}

// Forzar filtro para una página de marca
if ( $param_marca ){
    $args['pa_marca'] = $param_marca;
}
// Forzar filtro para una página de fabricante
if ( $param_fabricante ){
    $args['pa_fabricante'] = $param_fabricante;
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
echo "<h3>Encuentra aquí tus llantas</h3>";

// Validación para que funcione en el home la función de create_modelo_select()
if ( is_home() || is_front_page() ){
    $param_marca = get_custom_params('pa_marca');
}

echo "<div class='illantas-filters'>";
foreach ($show_filters as $key => $value) {
    echo "<div class='illantas-filter'>";
    echo "<label>".$value."</label>";
    if ( $key !== 'modelo' || ! $param_marca  ){ // Si la marca esta en todos o sea diferente del modelo
        echo $filtro_selects->create_generic_select($key);
    } else {
        echo $filtro_selects->create_modelo_select($param_marca); // Sólo cuando una marca esta seleccionada
    }
    echo "</div>";
}
echo "</div>";

echo " <a class='btn-filter-clean' href='#'>Limpiar</a>";
echo "</section>";