<?php
/*
 ------------------------------------------------------------------------- 
 GLPI - Gestionnaire Libre de Parc Informatique 
 Copyright (C) 2003-2009 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Julien Dombre
// Purpose of file:
// ----------------------------------------------------------------------
Session::addMessageAfterRedirect($_POST , false, ERROR);
// Direct access to file

if (strpos($_SERVER['PHP_SELF'], "dropdownValue.php")) {
   $AJAX_INCLUDE=1;
   include ('../../../inc/includes.php');
   header("Content-Type: text/html; charset=UTF-8");
   Html::header_nocache();
}

Session::checkCentralAccess();
// Make a select box with all glpi users
//$type es el índice del elemento seleccionado en el desplegable de clase
$type = $_POST['type_relations'];
//echo "<input type='hidden' size='30' name='type2' value='".$type."'>";

$field_isdel  = "";

$itemtype = $_POST['itemtype'];

//Obtengo la clase seleccionada
$tabletype=PluginRelationRelation::getNombreClaseRelacionada($type);
	switch ($tabletype){
		case "Group" :
			$field_isdel = "1=1 ";
			break;
		default:
			$field_isdel = "is_deleted = 0 ";
			break;
	}

$where = " WHERE ".$field_isdel;

//echo $tabletype;
if ( ! class_exists($tabletype) ) {
	$datatable = "";
} else {
	$objeto = new $tabletype();
	$datatable = $objeto->getTable();
}	


if (isset($_POST["entity_restrict"])&&$_POST["entity_restrict"]>=0){
	$where.=getEntitiesRestrictRequest("AND",$datatable,'',$_POST["entity_restrict"],false);
} else {
	$where.=getEntitiesRestrictRequest("AND",$datatable,'','',false);
}

if ($_POST['searchText']!=$CFG_GLPI["ajax_wildcard"])
	$where.=" AND $datatable.name ".Search::makeTextSearch($_POST['searchText']);

$NBMAX=$CFG_GLPI["dropdown_max"];
$LIMIT="LIMIT 0,$NBMAX";
if ( $_POST['searchText'] == $CFG_GLPI["ajax_wildcard"]) $LIMIT="";

$leftjoin='';
if( $_POST['myname'] == 'childID'){
	// Insure that the device does not already have a parent
	$leftjoin=" LEFT JOIN glpi_plugin_relation_relations AS pc "
		. "ON $datatable.id = pc.items_id ";
}


//$query = "SELECT $datatable.id, name, entities_id FROM $datatable "."$where ORDER BY entities_id, name $LIMIT";

$query = "SELECT id, name, entities_id FROM glpi_computers";
//echo $query;
$result = $DB->query($query);

	switch ($itemtype){
		/*
		case "User" :

				  User::dropdown(array('name'   => $_POST['myname'],
										'right'  => 'all'
									   //'value'  => $this->fields["autorizador_id"],
									   //'right'  => 'interface',
									   //'entity' => $_POST["entity_restrict"]
									   ));
							   
										
						   
			break;
		case "Group" :
				 Dropdown::show('Group', array('name'      => $_POST['myname']//,
											   //'value'     => $this->fields['groups_id_tech'],
											   //'entity'    =>  $_POST["entity_restrict"]
											   ));						   
			break;	
		*/			
		default:

		//echo "<select name=\"".$_POST['myname']."\">";
		//echo "<option value=\"0\">-----</option>";

		$return = array('results' => array(array('id' => null, 'text' => '-----')));		
		$results = &$return['results'];
		if ($count = $DB->numrows($result)) {
			$prev = -1;
			$tmp_results = array();
			while ($data=$DB->fetch_array($result)) {

				$entities_id = 0;
				if (isset($data["entities_id"])) {
				$entities_id = $data["entities_id"];
				}
				$tmp_results[$entities_id]['text']= Dropdown::getDropdownName("glpi_entities", $entities_id);			
				$tmp_results[$entities_id]['children'][] = array('id'    => $tabletype.";".
														   $data['id'].";".
														   $data['spec'],
												'level' => 1,
												'text'  => substr($data["name"], 0, 
																 $CFG_GLPI["dropdown_chars_limit"]));
																 
			}

			
		}

	   foreach ($tmp_results as $tmp_result) {
		  $results[] = $tmp_result;
	   }		
		//echo "</select>";
		
			break;
	}

$return['count'] = $count;
echo json_encode($return);

?>
