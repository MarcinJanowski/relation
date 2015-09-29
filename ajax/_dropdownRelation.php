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
// Sponsor: Oregon Dept. of Administrative Services, State Data Center
// Original Author of file: Ryan Foster
// Contact: Matt Hoover <dev@opensourcegov.net>
// Project Website: http://www.opensourcegov.net
// Purpose of file:
// ----------------------------------------------------------------------


//if( ereg('dropdownRelation.php',$_SERVER['PHP_SELF']) ){
// ereg deprecated in PHP 5.3+
include ('../../../inc/includes.php');

header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();
/*else {
	echo "<br>arraySelect=";
	print_r ($arraySelect);
	echo "<br>";
}
*/
Session::checkCentralAccess();

// Make a select box

if ( isset($_POST['type_relations']) ){

	$rand=$_POST['rand'];

	

	$itemtype = $_POST['itemtype'];
	if ( ! class_exists($itemtype) ) {
		$table = "";
	} else {
		$objeto = new $itemtype();
		$table = $objeto->getTable();
	}
	
	
	
	//$use_ajax=$CFG_GLPI['use_ajax'];
	//echo $CFG_GLPI['use_ajax'];

	$tabletype=PluginRelationRelation::getNombreClaseRelacionada($_POST['type_relations']);
	/*
	switch ($tabletype){
		case "Group" :
			$use_ajax=0;
			break;
		case "User" :
			$use_ajax=0;
			break;			
		default:
			$use_ajax=$CFG_GLPI['use_ajax'];
			break;
	}	
	*/
	//echo $tabletype."-".$use_ajax;
	//$params = array();

	$params = array('searchText'=>'__VALUE__',
			'type_relations'=>$_POST['type_relations'],
			'entity_restrict'=>$_POST['entity_restrict'],
			'itemtype'=>$_POST['itemtype'],
			'rand'=>$_POST['rand'],
			'myname'=>$_POST['myname']
			);
			//'used'=>$_POST['used']
			
	//print_r($params);
	//Session::addMessageAfterRedirect($_POST , false, ERROR);
	 $field_id = Html::cleanId("show_".$_POST['myname'].$rand);
	//$field_id = "show_".$_POST['myname'].$rand;
	//$default='<select name="'.$_POST['myname'].'"><option value="0">------</option></select>';
	//Ajax::dropdown($use_ajax,'/plugins/relation/ajax/dropdownValue.php',$params,$default,$rand);
    echo Html::jsAjaxDropdown($_POST['myname'], $field_id,
                              $CFG_GLPI['root_doc']."/plugins/relation/ajax/dropdownValue.php",
                              $params);	
	

}		
?>
