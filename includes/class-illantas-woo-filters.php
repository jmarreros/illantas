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
class Illantas_Woo_Filters {

    private $data;
    private $attributes;

    public function __construct($attributes = ['5x100', '18-0']){
        $this->attributes = $attributes;
        $this->data = $this->get_data_attributes();
    }

    public function create_select_marcas(){
        $data = [
            'ford' => 'Ford',
            'honda' => 'Honda',
            'audi' => 'Audi',
        ];

        // return $this->create_select_generic('dropdown_nav_marca', 'marca', $data, 0);
        return $this->data;
    }

    // Crea un lista genérica
    private function create_select_generic($class, $url, $data, $selected){
        $out = '<select class="'.esc_attr($class).'"';
        $out .= ' data-filter-url="'.esc_attr ($url).'">';

        foreach ($data as $key => $value) {
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

        if ($count > 0){
            $condition = " AND t.slug IN ('". implode("','", $this->attributes) ."') ";
            $having = " HAVING count(p.id) >= $count";
        }

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

        $results = $wpdb->get_results( $query );

        return $results;

    }
}