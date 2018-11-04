<?php

$sel_marca = 0;
// $rel = new Illantas_Woo_Relations();
// $terms_marca = $rel->get_marcas();

if ( isset ( $taxonomy->term_id ) ){ //Edición del term (pa_modelo)

	$term_id = $taxonomy->term_id; // pa_modelo
	// $sel_marca = get_term_meta( $term_id , TERM_META , true); // term_meta sel-marca
	?>
	<tr class="form-field">
	    <th scope="row">
	        <label for="sel-marcas">Anclaje</label>
	    </th>
		<td>
	       <?php // make_select_marcas( $terms_marca, $sel_marca ); ?>
	    </td>
	</tr>

<?php } else { // nuevo term (pa_modelo) ?>

	<div class="form-field term-sel-marcas-wrap">
		<label for="sel-marcas">Anclaje</label>
		<?php // make_select_marcas( $terms_marca, $sel_marca ); ?>
	</div>

<?php }

