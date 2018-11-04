<?php

$sel_marca = 0;
$rel = new Illantas_Woo_Relations();
$terms_marca = $rel->get_marcas();

if ( isset ( $taxonomy->term_id ) ){ //Edición del term (pa_modelo)

	$term_id = $taxonomy->term_id; // pa_modelo
	$sel_marca = get_term_meta( $term_id , TERM_META , true); // term_meta sel-marca
	?>
	<tr class="form-field">
	    <th scope="row">
	        <label for="sel-marcas">Marca</label>
	    </th>
		<td>
	       <?php make_select_marcas( $terms_marca, $sel_marca ); ?>
	    </td>
	</tr>

<?php } else { // nuevo term (pa_modelo) ?>

	<div class="form-field term-sel-marcas-wrap">
		<label for="sel-marcas">Marca</label>
		<?php make_select_marcas( $terms_marca, $sel_marca ); ?>
	</div>

<?php }


function  make_select_marcas( $terms_marca, $sel_marca ){ ?>
	<select class="postform" id="sel-marcas" name="sel-marcas">
            <option value="0">Ninguno</option>
            <?php foreach( $terms_marca as $item ) { ?>
				<option value="<?php echo $item->term_id; ?>" <?php selected( $sel_marca, $item->term_id) ?> ><?php echo $item->name; ?></option>
            <?php } ?>
        </select>
    <p class="description">Selecciona una marca para el modelo</p>
<?php }

