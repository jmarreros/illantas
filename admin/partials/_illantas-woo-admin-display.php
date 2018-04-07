<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link 			https://mundollantas.es
 * @since 			1.0.0
 *
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/admin/partials
 */
?>

<?php include_once ILLANTAS_DIR . 'includes/class-illantas-woo-relations.php'; ?>

<?php

// Obtenemos las marcas
$id_marca = 0;
$rel = new Illantas_Woo_Relations();

$terms_marca = $rel->get_marcas();

error_log( print_r($terms_marca, true) );

// Grabar modelo
if ( isset( $_POST['save'] ) ){
	$id_marca = $_POST['id_marca'];
	$modelo = $_POST['modelo'];
	
	if ( $rel->save_modelo( $id_marca, $modelo) ){
		echo "<div id='message' class='notice notice-success'><p>Se agregó modelo correctamente</p></div>";
	}

}

// Cambio de marca
if ( isset( $_POST['change'] ) ){
	$id_marca = $_POST['marca'];	
}


if ( isset( $_GET['action']) ){
	$id_marca = $_GET['marca'];
	$modelo = $_GET['modelo'];
	$action = $_GET['action'];

	if ( $action == 'delete' && $id_marca && $modelo ){
		if ( $rel->delete_modelo( $id_marca, $modelo ) ){
			echo "<div id='message' class='notice notice-success'><p>Se eliminó el registro</p></div>";
		}
	}

}

?>
<p>
	<form id="frm_marcas" name="frm_marcas" method="post" action="<?php echo admin_url('admin.php?page=illantas'); ?>">
		<div>
		<label for="marca">Marca: </label>
		
		<select id="marca" name="marca">
			<?php foreach ($terms_marca as $item) { ?>
				<option value="<?php echo $item->term_id; ?>" <?php selected( $id_marca, $item->term_id) ?> ><?php echo $item->name; ?></option>
			<?php } ?>
		</select>
		<input name="change" id="change" class="button button-primary"  value="Mostrar" type="submit">
		</div>


	</form>
</p>

<hr/>
<br>

<?php if ( $id_marca > 0 ):
	  $modelos = $rel->get_modelos_marca( $id_marca ); ?>

	<table id="tbl-modelos" class="wp-list-table widefat">
		<thead>
			<tr>
				<th>Modelo</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( isset($modelos) ): ?>
			<?php foreach( $modelos as $item ) {?>
				<tr>
					<td><?php echo $item->modelo ?></td>
					<td><a class="delete" href="<?php echo esc_url(admin_url('admin.php?page=illantas&modelo='.$item->modelo.'&marca='.$id_marca.'&action=delete')); ?>">Eliminar</a></td>
				</tr>
			<?php } ?>
		<?php endif; ?>
		</tbody>
	</table>
	
	<p>
		<form id="frm_modelo" name="frm_modelo" method="post" action="<?php echo admin_url('admin.php?page=illantas'); ?>">
			<div>
			<label for="marca">Agregar Modelo: </label>
			
			<input type="text" name="modelo" name="modelo" value="" required>

			<input type="hidden" name="id_marca" value="<?php echo $id_marca; ?>">
			<input name="save" id="save" class="button button-primary"  value="Grabar" type="submit">
			</div>
		</form>
	</p>

<?php endif; ?>



