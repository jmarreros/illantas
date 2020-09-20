<?php

// Muestra los controles de seleccion para los distintos atributos
// De acuerdo a las dependencias con productos

include_once ILLANTAS_DIR . 'includes/class-illantas-woo-filters.php';


//Creamos el array de valores para los filtros con la variable $attrs;



// Creamos la clase pasándo como parámetro los atributos seleccionados
$filtro_marcas = new Illantas_Woo_Filters(['5x108']);

// Creamos el select para marca
echo " - Marca:".$filtro_marcas->create_generic_select('marca');
echo " - Diámetro".$filtro_marcas->create_generic_select('diametro');
echo " - Anclaje".$filtro_marcas->create_generic_select('anclaje');
echo " - Modelo".$filtro_marcas->create_generic_select('modelo');



