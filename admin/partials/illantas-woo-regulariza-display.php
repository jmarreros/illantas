<h1 class="wp-heading-inline"> Regulariza Marcas / Modelos </h1>
<hr class="wp-header-end">

<?php

$modelos = $rel->get_modelos();
$anclajes = $rel->get_anclajes();

// print_r($anclajes);
// print_r($modelos_anclaje);
// print_r($modelos);
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
    #frm-regulariza-existentes .container-inline:last-child{
        vertical-align:top;
    }
</style>

<h3>Regulariza productos existentes</h3>
<p>Regulariza todos los productos <strong>que tengan el anclaje</strong> al que pertenece ese modelo seleccionado. Se agregará el modelo y marca(si aplica)</p>
<?php if ( ! empty($modelos) ): ?>
<form id="frm-regulariza-existentes" method="post">
    <div class="submit">
        <div class="container-inline">
            <label>Modelo:</label>
            <select id="modelos" name="modelos">
                <?php foreach( $modelos as $modelo ): ?>
                    <?php $anclaje = get_term_meta( $modelo->term_id, TERM_META_ANCLAJE, true ); ?>
                    <option data-anclaje="<?= $anclaje ?>" value="<?= $modelo->term_id ?>" ><?= $modelo->name ?></option>
                <?php endforeach; ?>
            </select>
            <div id="anclaje-name">Anclaje: <span></span></div>
        </div>

        <div class="container-inline">
            <input name="submit" id="submit-existentes" class="button button-primary" value="Regularizar" type="submit" >
            <span class="processing-existentes">
                Procesando ... <img src="<?php echo ILLANTAS_URL.'/assets/loader.gif' ?>" />
            </span>
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
// Grabar anclajes en variable javascript
$arr_anclaje = array();
foreach ($anclajes as $anclaje){
    $arr_anclaje[$anclaje->term_id] = $anclaje->name;
};

echo "var obj_anclajes = JSON.parse('" . json_encode($arr_anclaje) . "');";
?>

(function($){

//inicializaciones
$('.processing-nuevos').hide();
$('.processing-existentes').hide();

// Change select modelos
var id_anclaje;
$('#frm-regulariza-existentes #modelos').on('change',function(){
    id_anclaje = $(this).find(':selected').data("anclaje");
    $('#anclaje-name span').text(obj_anclajes[id_anclaje]?obj_anclajes[id_anclaje]:'');
});


// On submit nuevos productos
$('#frm-regulariza-nuevos').on('submit', function(e){
     e.preventDefault();
     console.log('Se hizo click');

     $.ajax({
        url: "<?php echo admin_url('admin-ajax.php') ?>",
        type: 'post',
        data:{
            action:'illantas_regulariza'
        },
        beforeSend:function(){
            $('#submit-nuevos').attr('disabled',true);
            $('.processing-nuevos').show();
        },
        error: function(){
            $('.processing-nuevos').html('<strong>Ocurrió algún error!!</strong>');
        },
        success: function (res){
            if ( parseInt(res) <=0 ){
                $('.processing-nuevos').html('<strong>Ocurrió algún error!!</strong>');
            }
            else{
                $('.processing-nuevos').html('<strong>El proceso culminó correctamente</strong>');
                $('#submit-nuevos').hide();
                console.log(res);
            }
        }

     });

});

$('#frm-regulariza-existentes #modelos').trigger('change');


})(jQuery);

</script>