<?php

$sel_anclaje = 0;
$rel = new Illantas_Woo_Relations();
$terms_anclaje = $rel->get_anclajes();

if ( isset ( $taxonomy->term_id ) ){ //Edición del term (pa_modelo)

	$term_id = $taxonomy->term_id; // pa_modelo
	$sel_anclaje = get_term_meta( $term_id ,  TERM_META_ANCLAJE, true); // term_meta sel-anclajes
	?>
	<tr class="form-field">
	    <th scope="row">
	        <label for="sel-anclaje">Anclaje</label>
	    </th>
		<td>
	       <?php make_select_anclajes( $terms_anclaje, $sel_anclaje ); ?>
	    </td>
	</tr>

<?php } else { // nuevo term (pa_modelo) ?>

	<div class="form-field term-sel-anclajes-wrap">
		<label for="sel-anclaje">Anclaje</label>
		<?php make_select_anclajes( $terms_anclaje, 0 ); ?>
	</div>

<?php }


function  make_select_anclajes( $terms_anclaje, $sel_anclaje ){ ?>
	<select class="postform" id="sel-anclajes" name="sel-anclajes">
            <option value="0" <?php selected( $sel_anclaje, 0) ?> >Ninguno</option>
            <?php foreach( $terms_anclaje as $item ) { ?>
				<option value="<?php echo $item->term_id; ?>" <?php selected( $sel_anclaje, $item->term_id) ?> ><?php echo $item->name; ?></option>
            <?php } ?>
    </select>
    <p class="description">Selecciona un anclaje para el modelo</p>
<?php }
