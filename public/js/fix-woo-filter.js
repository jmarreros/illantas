
(function( $ ) {
    'use strict';
    let filter_marca = '';
    const sel_modelo_options = $('.widget_layered_nav h3:contains("MODELO")').parent().find('select');

    // Cuando se carga la primera vez se llama a las funciones
    $( document ).ready(function() {
        filter_marca = get_parameter_marca();
        if (filter_marca) call_validate_relation(filter_marca);
    });

    // Función de llamada ajax para ocultar los valores de modelos de acuerdo a la marca
    function call_validate_relation(filter_marca){
        $.ajax({
            global: false,
            url : dcms_vars.ajaxurl,
            type: 'post',
            data: {
                action : 'fix_woo_filter',
                filter_marca,
            },
            success: function(res){
                const obj_compare = res ? JSON.parse(res): null;
                if (obj_compare) remove_items_modelo(obj_compare);
            }
        });
    }

    // Función para remover elementos del select creado
    function remove_items_modelo(obj_compare){
        const options_select = sel_modelo_options.find('option');

        // removemos los items que no se encuentren en el objeto de comparacion
        Object.values(options_select).map( (item, index) => {

            if ( item.nodeName === 'OPTION' && index > 0 ){
                const exist = obj_compare.find( option => {
                    return option.name === item.text;
                });
                if (!exist) item.remove();
            }
        });
    }

})( jQuery );

// Función axiliar para obtener el parámetro de marca
function get_parameter_marca(){
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    return urlParams.get('filter_marca');
}

// Cada vez que se cambian los filtros se llama nuevamente a las funcionaes
// $( document ).ajaxStop(function() {
//     filter_marca = get_parameter_marca();
//     call_validate_relation(filter_marca);
// });