
(function($){

    // Seccion regulariza productos existentes en base a anclaje
    // ==========================================================

    //inicializaciones
    $('.processing-nuevos').hide();
    $('.processing-existentes').hide();
    $('.processing-atributos').hide();

    // --> Productos existentes
    // Change select marcas
    $('#frm-regulariza-existentes #marcas').on('change',function(){
        var id_marca = $(this).val();
        var modelos = obj_marcas_modelo[id_marca];

        $('#frm-regulariza-existentes #modelos').empty();
        if ( modelos ){
            modelos.forEach(function(item) {
                $('#frm-regulariza-existentes #modelos').append($('<option>', {
                    value: item,
                    text : obj_modelos[item]
                }));
            });
        }

        $('#txt-marca').text($(this).find('option:selected').text());
        $('#frm-regulariza-existentes #modelos').trigger('change');
    });

    // Change select modelos
    $('#frm-regulariza-existentes #modelos').on('change',function(){
        var id_modelo = $(this).val();
        var id_anclaje = obj_modelo_anclaje[id_modelo];

        $('#txt-modelo').text($(this).find('option:selected').text());
        $('#anclaje-name span').text(obj_anclajes[id_anclaje]?obj_anclajes[id_anclaje]:'');
    });

    // On submit productos existentes
    $('#frm-regulariza-existentes').on('submit', function(e){
        e.preventDefault();

        // Validate
        if ( $('#anclaje-name span').text() == '' ){
            alert('El modelo no tiene anclaje, asigna un anclaje antes ✋');
            return false;
        }

        $.ajax({
            url: dcms_vars.ajaxurl,
            type: 'post',
            data:{
                action:'illantas_regulariza_existentes',
                id_modelo: $('#frm-regulariza-existentes #modelos').val(),
                id_marca : $('#frm-regulariza-existentes #marcas').val(),
                id_anclaje: obj_modelo_anclaje[$('#frm-regulariza-existentes #modelos').val()]
            },
            beforeSend:function(){
                $('#submit-existentes').attr('disabled', true);
                $('.processing-existentes').show();
                $('.processing-existentes .procesando').show();
            },
            error: function(){
                $('.processing-existentes .procesando').hide();
                $('.processing-existentes .msg').html('<strong>Ocurrió algún error!!</strong>');
            },
            success: function (res){
                $('.processing-existentes .procesando').hide();

                if ( ! res ){
                    $('.processing-existentes .msg').html('<strong>Ocurrió algún error!!</strong>');
                }
                else{
                    const txt_modelo = $('#frm-regulariza-existentes #modelos option:selected').text();
                    const txt_marca = $('#frm-regulariza-existentes #marcas option:selected').text();

                    $('.processing-existentes .msg').html(`
                        <span>Se actualizaron <strong>${res} productos</strong></span>
                        <span>- con la marca <strong>${txt_marca}</strong> </span>
                        <span>- con el modelo <strong>${txt_modelo}</strong> </span>
                        <a id="link-refresh" href="#">Regulariza otro anclaje</a>` );
                    $('#submit-existentes').hide();
                }
            }

            });

    });

    // Refrescar
    $('.processing-existentes').on('click', '#link-refresh', function(e) {
        e.preventDefault();
        $('.processing-existentes .msg').text('');
        $('#submit-existentes').attr('disabled', false);
        $('#submit-existentes').show();
    });

    $('#frm-regulariza-existentes #marcas').trigger('change');


    // Seccion nuevos productos
    // ===========================

    // --> On submit nuevos productos
    $('#frm-regulariza-nuevos').on('submit', function(e){
            e.preventDefault();

            $.ajax({
            url: dcms_vars.ajaxurl,
            type: 'post',
            data:{
                action:'illantas_regulariza_nuevos'
            },
            beforeSend:function(){
                $('#submit-nuevos').attr('disabled',true);
                $('.processing-nuevos').show();
            },
            error: function(){
                $('.processing-nuevos').html('<strong>Ocurrió algún error!!</strong>');
            },
            success: function (res){
                if ( parseInt(res) <= 0 ){
                    $('.processing-nuevos').html('<strong>Ocurrió algún error!!</strong>');
                }
                else{
                    $('.processing-nuevos').html('<strong>El proceso culminó correctamente</strong>');
                    $('#submit-nuevos').hide();
                }
            }

            });

    });

    // Sección regulariza atributos
    // ============================

    // --> On submit regulariza atributos anclaje-modelo-marca
    $('#frm-regulariza-atributos').on('submit', function(e){
            e.preventDefault();

            $.ajax({
            url: dcms_vars.ajaxurl,
            type: 'post',
            data:{
                action:'illantas_regulariza_atributos'
            },
            beforeSend:function(){
                $('#submit-atributos').attr('disabled',true);
                $('.processing-atributos').show();
            },
            error: function(){
                $('.processing-atributos').html('<strong>Ocurrió algún error!!</strong>');
            },
            success: function (res){
                if ( parseInt(res) <= 0 ){
                    $('.processing-atributos').html('<strong>Ocurrió algún error!!</strong>');
                }
                else{
                    $('.processing-atributos').html('<strong>El proceso culminó correctamente</strong>');
                    $('#submit-atributos').hide();
                }
            }

            });

    });



})(jQuery);