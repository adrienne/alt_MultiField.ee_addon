<?php

/**
 * DataGrab Date fieldtype class
 *
 * @package   DataGrab
 * @author    Andrew Weaver <aweaver@brandnewbox.co.uk>
 * @copyright Copyright (c) Andrew Weaver
 */
class Datagrab_alt_multifield extends Datagrab_fieldtype {

	function prepare_post_date( $DG, $item, $field_id, $field, &$data, $update = FALSE ) {
        $mearr = array();
		$myself = $DG->datatype->get_item( $item, $DG->settings[ $field ] );
        $myself = explode("!",$myself);
        foreach($myself as $key=>$row) {
            $r2 = explode(":",$row);
            $mearr[$r2[0]] = $r2[1];
            }
        
		$data[ "field_id_" . $field_id ] = ($mearr);
		
	}

}

?>