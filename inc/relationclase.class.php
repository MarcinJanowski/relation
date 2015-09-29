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
class PluginRelationRelationclase extends CommonDBTM {

   // From CommonDBTM
   public $table            = 'glpi_plugin_relation_relationclases';
   public $type             = 'PluginRelationRelationclase';

   // Should return the localized name of the type
   static $rightname = 'config';

   public static function getTypeName($nb=0) {
      return __('Relacion clase', 'relationclase');
   }

    public function getSearchOptions() {
	  
      $tab = array(
         '1' => array(
            'table'         => $this->getTable(),
            'field'         => 'classname',
            'name'          => __('Nombre de la clase','Nombre de la clase'),
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
            'field'         => 'classlist',
            'name'          => __('Clase con la que se relaciona','Clase con la que se relaciona'),
            'datatype'      => 'text',
            'massiveaction' => true,
         ),
         '4' => array(
            'table'         => $this->getTable(),
            'field'         => 'comment',
            'name'          => __('Description', 'relation'),
            'massiveaction' => true,
         ),
         '5' => array(
            'table'         => 'glpi_entities',
            'field'         => 'completename',
            'name'          => _n('Entity', 'Entities', 1),
            'datatype'      => 'dropdown',
            'massiveaction' => true,
         ),
         '6' => array(
            'table'         => $this->getTable(),
            'field'         => 'is_recursive',
            'name'          => __('Child entities'),
            'datatype'      => 'bool',
            'massiveaction' => true,
         )	 
       );
      return $tab;	  
	  

   }
   
  
    function showForm ($ID, $options=array()) {
      $relation = new PluginRelationRelation();
      $this->initForm($ID, $options);	  
      $this->showFormHeader($options);
         
      echo "<tr class='tab_bg_1'>";
      //Nombre de la clase
      echo "<td>".__('Nombre de la clase','Nombre de la clase').": </td><td>";
	  $relation->dropdownClase("classname",$this->fields["classname"]);
      echo "</td>";
      //Clase con las que se relaciona
      echo "<td>".__('Clase con la que se relaciona','Clase con la que se relaciona').": </td>";
      echo "<td>";
	   $relation->dropdownClase("classlist",$this->fields["classlist"]);
      echo "</td>";
      echo "</tr>";
      echo "<tr class='tab_bg_1'>";
      //Descripción
      echo "<td>".__('Descripción','Descripción').": </td><td>";
	  Html::autocompletionTextField($this,"comment",array('size' => "15"));
      echo "</td>";
      echo "</tr>";
      $this->showFormButtons($options);
	  return true;
    }

}
?>
