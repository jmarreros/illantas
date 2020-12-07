<h1 class="wp-heading-inline"> Regulariza Marcas / Modelos </h1>
<hr class="wp-header-end">

<?php

$marcas = $rel->get_marcas();
$modelos = $rel->get_modelos();
$anclajes = $rel->get_anclajes();

$modelos_marca = json_encode($rel->get_all_modelos_marca());
$modelos_meta_anclaje = json_encode($rel->get_modelos_meta_anclaje());
?>
<!-- Regularización de productos existentes en base al anclaje -->
<h3>Regulariza productos</h3>

<?php if ( ! empty($modelos) ): ?>
<form id="frm-regulariza-existentes" method="post">
    <div>
        <div class="container-inline">
            <label>Filtrar Marca:</label>
            <select id="marcas" name="marcas">
                <?php foreach( $marcas as $marca ): ?>
                    <option value="<?= $marca->term_id; ?>"><?= $marca->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="container-inline">
            <label>Filtrar Modelo:</label>
            <select id="modelos" name="modelos">
            </select>

        </div>

        <p>Agrega marca <strong id="txt-marca"></strong> y modelo <strong id="txt-modelo"></strong> a todos los productos <strong>que tengan el siguiente anclaje</strong></p>

        <div class="container-block">
            <div class="container-inline">
                <div id="anclaje-name">Anclaje: <span></span></div>
            </div>

            <div class="container-inline">
                <input name="submit" id="submit-existentes" class="button button-primary" value="Regularizar" type="submit" >
            </div>
        </div>

        <div class="container-block">
            <div class="processing-existentes">
                <span class="procesando">
                    Procesando ... <img src="<?php echo ILLANTAS_URL.'/assets/loader.gif' ?>" />
                </span>
                <div class="msg">
                </div>
            </div>
        </div>

    </div>
</form>
<?php else: ?>
    <p><strong>No hay modelos agregados</strong> 🔥</p>
<?php endif; ?>

<!-- Regularización de nuevos productos importados -->
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


<?php
// Mostramos la opción de regularizar relacion de anclajes con modelos y marcas sólo en el subsitio
if ( is_multisite() && ! is_main_site() ): ?>

<!-- regularización de terminos anclaje modelo y marca en subsitios -->
<hr>
<h3>Regulariza relación Anclaje - Modelo y Marca</h3>
<p>La siguiente opción es para regularizar los datos de anclaje-modelo-marca del sitio principal en este sitio. Necesario si se ha agregado nuevas relaciones en el sitio principal</p>

<form id="frm-regulariza-atributos" method="post">
    <div class="submit">
        <input name="submit" id="submit-atributos" class="button button-primary" value="Regularizar" type="submit" >
        <span class="processing-atributos">
            Procesando ... <img src="<?php echo ILLANTAS_URL.'/assets/loader.gif' ?>" />
        </span>
    </div>
</form>


<?php endif; ?>

<script>
<?php
// Simplificamos objetos de modelos y anclajes y los pasamos como variables para Javascript

$arr_modelo = array();
foreach ($modelos as $modelo){
    $arr_modelo[$modelo->term_id] = $modelo->name;
};

$arr_anclaje = array();
foreach ($anclajes as $anclaje){
    $arr_anclaje[$anclaje->term_id] = $anclaje->name;
};

echo "let obj_modelos = JSON.parse('" . json_encode($arr_modelo) . "');\n";
echo "let obj_anclajes = JSON.parse('" . json_encode($arr_anclaje) . "');\n";
echo "let obj_marcas_modelo = JSON.parse('" . $modelos_marca . "');\n";
echo "let obj_modelo_anclaje = JSON.parse('". $modelos_meta_anclaje ."');\n";
?>
</script>
