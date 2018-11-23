<h1 class="wp-heading-inline"> Regulariza Marcas / Modelos </h1>
<hr class="wp-header-end">

<?php

$marcas = $rel->get_marcas();
$modelos = $rel->get_modelos();
$anclajes = $rel->get_anclajes();

$modelos_marca = json_encode($rel->get_all_modelos_marca());
$modelos_meta_anclaje = json_encode($rel->get_modelos_meta_anclaje());
?>

<style>
    .container-inline{
        display:inline-block;
    }
    #anclaje-name{
        margin-top:10px;
        margin-bottom:15px;
    }
    .processing-existentes,
    .processing-nuevos{
        display:inline-block;
        margin-top:-10px;
    }

    #frm-regulariza-existentes .container-inline{
        vertical-align:top;
        margin-right:6px;
    }

    #anclaje-name{
        display:inline-block;
        vertical-align:top;
        margin-top:6px;
    }
    #anclaje-name span{
        vertical-align:top;
        min-width:70px;
        display:inline-block;
        background:#ccc;
        padding:2px 4px ;
        min-height:20px;
        border-radius:3px;
        margin-top:-2px;
    }
    #marcas,
    #modelos{
        min-width:120px;
    }
</style>

<h3>Regulariza productos</h3>
<p>Regulariza todos los productos <strong>que tengan el anclaje</strong> al que pertenece el <strong>modelo seleccionado</strong>. Se agregará el modelo y marca(si aplica)</p>
<?php if ( ! empty($modelos) ): ?>
<form id="frm-regulariza-existentes" method="post">
    <div class="submit">

        <div class="container-inline">
            <label>Filtrar:</label>
            <select id="marcas" name="marcas">
                <?php foreach( $marcas as $marca ): ?>
                    <option value="<?= $marca->term_id; ?>"><?= $marca->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="container-inline">
            <label>Modelo:</label>
            <select id="modelos" name="modelos">
            </select>

        </div>

        <div class="container-inline">
            <div id="anclaje-name">Anclaje: <span></span></div>
        </div>

        <div class="container-inline">
            <input name="submit" id="submit-existentes" class="button button-primary" value="Regularizar" type="submit" >
        </div>

        <div class="container-inline">
            <div class="processing-existentes">
                Procesando ... <img src="<?php echo ILLANTAS_URL.'/assets/loader.gif' ?>" />
            </div>
        </div>

    </div>
</form>
<?php else: ?>
    <p><strong>No hay modelos agregados</strong> 🔥</p>
<?php endif; ?>

<hr>
<h3>Regulariza nuevos productos</h3>
<p>La siguiente opción te permite regularizar los <strong>modelos y marcas</strong> de los anclajes de los <strong>nuevos productos importados</strong></p>

<form id="frm-regulariza-nuevos" method="post">
    <div class="submit">
        <input name="submit" id="submit-nuevos" class="button button-primary" value="Regularizar" type="submit" >
        <span class="processing-nuevos">
            Procesando ... <img src="<?php echo ILLANTAS_URL.'/assets/loader.gif' ?>" />
        </span>
    </div>
</form>


<script>

<?php
// Simplificamos objetos de modelos y anclajes

$arr_modelo = array();
foreach ($modelos as $modelo){
    $arr_modelo[$modelo->term_id] = $modelo->name;
};

$arr_anclaje = array();
foreach ($anclajes as $anclaje){
    $arr_anclaje[$anclaje->term_id] = $anclaje->name;
};

echo "var obj_modelos = JSON.parse('" . json_encode($arr_modelo) . "');";
echo "var obj_anclajes = JSON.parse('" . json_encode($arr_anclaje) . "');";
echo "var obj_marcas_modelo = JSON.parse('" . $modelos_marca . "');";
echo "var obj_modelo_anclaje = JSON.parse('". $modelos_meta_anclaje ."');";
?>

(function($){

//inicializaciones
$('.processing-nuevos').hide();
$('.processing-existentes').hide();


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
    $('#frm-regulariza-existentes #modelos').trigger('change');
});

// Change select modelos
$('#frm-regulariza-existentes #modelos').on('change',function(){
    var id_modelo = $(this).val();
    var id_anclaje = obj_modelo_anclaje[id_modelo];

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
        url: "<?php echo admin_url('admin-ajax.php') ?>",
        type: 'post',
        data:{
            action:'illantas_regulariza_existentes',
            id_modelo: $('#frm-regulariza-existentes #modelos').val(),
            id_anclaje: obj_modelo_anclaje[$('#frm-regulariza-existentes #modelos').val()]
        },
        beforeSend:function(){
            $('#submit-existentes').attr('disabled',true);
            $('.processing-existentes').show();
        },
        error: function(){
            $('.processing-existentes').html('<strong>Ocurrió algún error!!</strong>');
        },
        success: function (res){
            if ( parseInt(res) <= 0 ){
                $('.processing-existentes').html('<strong>Ocurrió algún error!!</strong>');
            }
            else{
                $('.processing-existentes').html('<strong>El proceso culminó correctamente.</strong> <a href="' + window.location.href + '">Regulariza otro modelo</a>' );
                $('#submit-existentes').hide();
            }
        }

     });

});

// On submit nuevos productos
$('#frm-regulariza-nuevos').on('submit', function(e){
     e.preventDefault();

     $.ajax({
        url: "<?php echo admin_url('admin-ajax.php') ?>",
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

$('#frm-regulariza-existentes #marcas').trigger('change');


})(jQuery);

</script>