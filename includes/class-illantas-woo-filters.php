<?php

/**
 * Class for manage relation marca / modelo
 *
 * Gestiona los datos de marcas y modelos de la tabla wp_illantas_relations
 *
 * @since 			1.0.0
 * @package 		Illantas_Woo
 * @subpackage 		Illantas_Woo/includes
 * @author 			jmarreros
 */

include_once ILLANTAS_DIR.'includes/class-illantas-woo-relations.php';

class Illantas_Woo_Filters {

    private $data; // Almacena toda la data de todos los filtros
    private $attributes; // Almacena los atributos que se pasan como parámetros


    // Inicialización
    public function __construct( $attributes ){
        $this->attributes = $attributes;
        $this->data = $this->get_data_attributes();
    }


    // Obtiene datos y crea lista de acuerdo a la taxonomia
    public function create_generic_select($tax){
        $list = $this->filtrar_datos($tax);
        return $this->create_HTML_select('idropdown_'.$tax, $tax, $list);
    }


    // Caso especial para el modelo que se llenará en base a la marca
    public function create_modelo_select($param_marca){
        // Recuperarmos los modelos relacionados con las marcas
        $relations = new Illantas_Woo_Relations();
        $relation_modelos =  $relations->get_modelos_marca_by_slug($param_marca);

        // Recuperamos los modelos que se usan en todos los productos
        $products_modelos = $this->filtrar_datos('modelo');

        // Comparamos ambos arrays
        $modelos = $this->compare_arrays_model($relation_modelos, $products_modelos);

        return $this->create_HTML_select('idropdown_modelo', 'modelo', $modelos);
    }


    // Funcion para filtrar los datos que ya se han guardado en la variable data
    private function filtrar_datos($tax){
        $list = array();
        // Filtramos los datos de sólo una taxonomía específica
        $list = array_filter( $this->data, function( $item ) use($tax){
            return $item['taxonomy'] == 'pa_'.$tax;
        });
        // Extraemos sólo el nombre y slug
        $list = wp_list_pluck($list, 'name', 'slug');
        return $list;
    }

    // Funcion axiliar para comparar arrays, comparamos por nombre debido a las diferencias del slug
    private function compare_arrays_model($rel, $pro){
        $arr = [];
        foreach ($rel as $item) {
            foreach($pro as $key => $val){
                if ($item['name'] === $val){
                    $arr[$key]=$val;
                    break;
                }
            }
        }
        return $arr;
    }


    // Crea un lista HTML genérica
    private function create_HTML_select($class, $tax, $data){
        if ( ! count($data) ) return false;

        $tax = 'pa_'.esc_attr ($tax);
        $out = '<select class="idropdown '.esc_attr($class).'"';
        $out .= ' data-filter-url="'.$tax.'">';

        $out .= '<option value="">Todos</option>';

        $selected = false;
        foreach ($data as $key => $value) {

            if ( ! $selected  && isset($this->attributes[$tax]) && $this->attributes[$tax] == $key) {
                $selected = true;
                $out .= '<option value="'.$key.'" selected>'.$value.'</option>';
                continue;

            }
            $out .= '<option value="'.$key.'">'.$value.'</option>';
        }
        $out .= '</select>';

        return $out;
    }


    // Obtenemos los datos de atributos en base a los que estan seleccionados
    private function get_data_attributes(){
        global $wpdb;

        // Formamos la sub-consulta
        $count = count($this->attributes);
        $condition = '';
        $having = '';

        // Condiciones de los parámetros
        if ($count > 0){
            // formar array key y valor concatenado
            $concat_attributes = [];
            foreach ($this->attributes as $key => $value) {
                $concat_attributes[] = $key.$value;
            }

            $condition = " AND CONCAT(tt.taxonomy, t.slug) IN ('". implode("','", $concat_attributes) ."') ";
            $having = " HAVING count(p.id) >= $count";
        }

        // Subconsulta
        $subquery = "SELECT p.id FROM {$wpdb->posts} p
                    INNER JOIN {$wpdb->term_relationships} rs ON rs.object_id = p.id
                    INNER JOIN {$wpdb->term_taxonomy} tt USING (term_taxonomy_id)
                    INNER JOIN {$wpdb->terms} t USING (term_id)
                    WHERE
                    p.post_type = 'product' AND
                    p.post_status = 'publish'
                    {$condition}
                    GROUP BY p.id
                    {$having}";


        // Formamos la consulta principal
        $query = "SELECT t.name, t.slug, tt.taxonomy FROM {$wpdb->term_relationships} tr
                    INNER JOIN {$wpdb->term_taxonomy} tt USING (term_taxonomy_id)
                    INNER JOIN {$wpdb->terms} t USING (term_id)
                    INNER JOIN (" . $subquery . ") AS s ON s.id = tr.object_id
                    GROUP BY t.name, t.slug, tt.taxonomy
                    ORDER BY tt.taxonomy, t.name";

        $results = $wpdb->get_results( $query , ARRAY_A);

        return $results;

    }
}