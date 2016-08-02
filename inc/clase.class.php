<?php
/*
 * @version $Id: HEADER 10411 2010-02-09 07:58:26Z moyo $
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2010 by the INDEPNET Development Team.

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
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')) {
        die("Sorry. You can't access directly to this file");
}

// Class of the defined type
class PluginRelationClase extends CommonDBTM {

   // From CommonDBTM
   public $table            = 'glpi_plugin_relation_clases';
   public $type             = 'PluginRelationClase';

   static $rightname = 'config';

   public static function getTypeName($nb=0) {
      return __('Clase', 'clase');
   }

  public function getSearchOptions() {
	  
      $tab = array(
         '1' => array(
            'table'         => $this->getTable(),
            'field'         => 'name',
            'name'          => __('Class name','Nombre de la clase'),
			'datatype'      => 'itemlink',
            'massiveaction' => true,
         ),
         '2' => array(
            'table'         => $this->getTable(),
            'field'         => 'id',
            'name'          => __('ID'),
			'datatype'      => 'number',
            'massiveaction' => false,
         ),		 
         '3' => array(
            'table'         => $this->getTable(),
            'field'         => 'viewname',
            'name'          => __('Display name for the class','Nombre a mostrar para la clase'),
            'datatype'      => 'text',
            'massiveaction' => true,
         ),
         '4' => array(
            'table'         => $this->getTable(),
            'field'         => 'comment',
            'name'          => __('Description', 'relation'),
            'datatype'      => 'text',			
            'massiveaction' => true,
         ),
         '5' => array(
            'table'         => $this->getTable(),
            'field'         => 'img',
            'name'          => __('Image','Imagen'),
            'datatype'      => 'text',			
            'massiveaction' => false,
         ),	
         '6' => array(
            'table'         => $this->getTable(),
            'field'         => 'is_visible',
            'name'          => __('Is visible','Es visible'),
			'datatype'      => 'bool',
            'massiveaction' => false,
         ),		 
         '7' => array(
            'table'         => 'glpi_entities',
            'field'         => 'completename',
            'name'          => _n('Entity', 'Entities', 1),
            'datatype'      => 'dropdown',
            'massiveaction' => true,
         ),
         '8' => array(
            'table'         => $this->getTable(),
            'field'         => 'is_recursive',
            'name'          => __('Child entities'),
            'datatype'      => 'bool',
            'massiveaction' => true,
         )	 
       );
      return $tab;	  
	  

   }
   

    public function showForm ($ID, $options=array()) {
	global $CFG_GLPI;
      $this->initForm($ID, $options);
      $this->showFormHeader($options);
      $relation = new PluginRelationRelation();
      echo "<tr class='tab_bg_1'>";
      //Nombre de la clase en GLPI
      echo "<td>".__('Class name','Nombre de la clase').": </td><td>";
	  //Html::autocompletionTextField($this,"name",array('size' => "15"));
	  $relation->dropdownType("name", $relation->getIdDropdown($this->fields["name"]));
      echo "</td>";
      //Nombre a mostrar en los desplegables para la clase
      echo "<td>".__('Display name for the class','Nombre a mostrar para la clase').": </td>";
      echo "<td>";
      Html::autocompletionTextField($this,"viewname",array('size' => "15"));
      echo "</td>";
      echo "</tr>";
      echo "<tr class='tab_bg_1'>";
      //Descripción
      echo "<td>".__('Description','Descripción').": </td><td>";
	  Html::autocompletionTextField($this,"comment",array('size' => "15"));
      echo "</td>";
      //Imagen
      echo "<td>".__('Image','Imagen').": </td>";
      echo "<td>";
      //file
      //$rep = "../plugins/relation/pics/";
	  $rep = '../plugins/relation/pics/';
      $dir = opendir($rep);
      echo "<select name=\"img\" id=\"img\">";
	  $sel = "";
	  
      while ($f = readdir($dir)) {

	  
         if (is_file($rep.$f)) {
		 	if ($f==rtrim($this->fields["img"]))
			{
			  $sel = "selected";
			}
			else
			{
				$sel = "";
			}
            echo "<option value='".$f."' data-image='".$_SESSION["glpiroot"]."/plugins/relation/pics/".$f."' ".$sel.">".$f."</option>";
         }
      }	  
      echo "</select>&nbsp;";
      closedir($dir);      
      echo "</td>";	  
	  echo "</tr>";
	  
	  
	  
      echo "<tr class='tab_bg_1'>";
      //Nombre de la clase en GLPI
      echo "<td>".__('Is visible','Es visible')."</td><td>";
	  //Html::autocompletionTextField($this,"name",array('size' => "15"));
	  Dropdown::showYesNo('is_visible', $this->fields['is_visible']);
      echo "</td>";
      //Nombre a mostrar en los desplegables para la clase
      echo "<td></td>";
      echo "<td></td>";
      echo "</tr>";
	  
	  $this->showFormButtons($options);
	  
       Html::scriptStart(); 
       echo "$(document).ready(function(e){
			try {
			$('#img').msDropDown();
			} catch(e) {
			alert(e.message);
			}
			});"; 
       echo Html::scriptEnd(); 
	  
      return true;
   }

	static function DropdownClase($myname, $value=0){
	global $DB,$CFG_GLPI;
	$query = "select id, name from glpi_plugin_relation_clases order by 1";
		$result=$DB->query($query);
		//Desplegable clase
		echo "<select name=$myname id=$myname>\n";
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				echo "<option value='".$data[0]."'>".$data[1]."</option>\n";			
			}
		}
		echo "</select>\n";		
		
		
	}   
   
}
?>
