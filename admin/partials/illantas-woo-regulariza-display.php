<h1 class="wp-heading-inline"> Regulariza Marcas / Modelos </h1>
<hr class="wp-header-end">

<p>La siguiente opción te permite regularizar los <strong>modelos y anclajes</strong> de las marcas de los <strong>Nuevos Productos Importados</strong></p>

<form id="frm-regulariza" method="post">
    <p class="submit">
        <input name="submit" id="submit" class="button button-primary" value="Regulariza Marcas/Modelos" type="submit" >
        <span class="processing">
            Procesando ... <img src="<?php echo ILLANTAS_URL.'/assets/loader.gif' ?>" />
        </span>
    </p>

</form>

<script>

(function($){
$('.processing').hide();
$('#frm-regulariza').on('submit', function(e){
     e.preventDefault();
     console.log('Se hizo click');

     $.ajax({
        url: "<?php echo admin_url('admin-ajax.php') ?>",
        type: 'post',
        data:{
            action:'illantas_regulariza'
        },
        beforeSend:function(){
            $('#submit').attr('disabled',true);
            $('.processing').show();
        },
        error: function(){
            $('.processing').html('<strong>Ocurrió algún error!!</strong>');
        },
        success: function (res){
            if ( parseInt(res) <=0 ){
                $('.processing').html('<strong>Ocurrió algún error!!</strong>');
            }
            else{
                $('.processing').html('<strong>El proceso culminó correctamente</strong>');
                $('#submit').hide();
                console.log(res);
            }
        }

     });

});

})(jQuery);

</script>