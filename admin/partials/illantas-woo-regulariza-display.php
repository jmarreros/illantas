<h1 class="wp-heading-inline"> Regulariza Marcas / Modelos </h1>
<hr class="wp-header-end">

<p>La siguiente opción te permite importar los modelos de las marcas de productos</p>

<form id="frm-regulariza" method="post">
    <p class="submit">
        <input name="submit" id="submit" class="button button-primary" value="Regulariza Marcas/Modelos" type="submit">
    </p>
</form>
<?php echo admin_url('admin-ajax.php') ?>


<script>

(function($){

$('#frm-regulariza').on('submit', function(e){
     e.preventDefault();
     console.log('Se hizo click');


     $.ajax({
        url: "<?php echo admin_url('admin-ajax.php') ?>",
        type: 'post',
        data:{
            action:'illantas_regulariza'
        },
        success: function (res){
                    console.log(res);
                }
     });

    //  link = $(this);
    //  id   = link.attr('href').replace(/^.*#more-/,'');

    // $.ajax({
    //     url : dcms_vars.ajaxurl,
    //     type: 'post',
    //     data: {
    //         action : 'dcms_ajax_readmore',
    //         id_post: id
    //     },
    //     beforeSend: function(){
    //         link.html('Cargando ...');
    //     },
    //     success: function(resultado){
    //          $('#post-'+id).find('.entry-content').html(resultado);
    //     }

    // });

});

})(jQuery);

</script>