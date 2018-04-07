<?php

$sel_marca = 0;

if ( isset ( $taxonomy->term_id ) ){ //Edición del term (pa_modelo)

	$term_id = $taxonomy->term_id;
	$sel_marca = get_term_meta( $term_id , TERM_META , true);

	?>
	<tr class="form-field">  
	    <th scope="row">  
	        <label for="sel-marcas">Marcas</label>
	    </th>  
		<td>
	        <select class="postform" id="sel-marcas" name="sel-marcas">
	            <option value="0">Ninguno</option>
	            <option value="1" <?php selected( $sel_marca, 1 ); ?> >1</option>
	            <option value="2" <?php selected( $sel_marca, 2 ); ?> >2</option>
	            <option value="3" <?php selected( $sel_marca, 3 ); ?> >3</option>
	        </select>
	        <p class="description">Selecciona una marca para el modelo</p>
	    </td>  
	</tr>

<?php } else { // nuevo term (pa_modelo) ?> 

	<div class="form-field term-sel-marcas-wrap">
		<label for="sel-marcas">Marcas</label>
	
		<select class="postform" id="sel-marcas" name="sel-marcas">
	        <option value="0">Ninguno</option>
	        <option value="1" <?php selected( $sel_marca, 1 ); ?> >1</option>
	        <option value="2" <?php selected( $sel_marca, 2 ); ?> >2</option>
	        <option value="3" <?php selected( $sel_marca, 3 ); ?> >3</option>
		</select>
	    <p class="description">Selecciona una marca para el nuevo modelo</p>

	</div>

<?php }
	   