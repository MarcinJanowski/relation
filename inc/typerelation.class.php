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
class PluginRelationTyperelation extends CommonDBTM {

   // From CommonDBTM
   public $table            = 'glpi_plugin_relation_typerelations';
   public $type             = 'PluginRelationTyperelation';
   

   static $rightname = 'config';

   public static function getTypeName($nb=0) {
      return __('Tipo relacion', 'relation');
   }

   
  public function getSearchOptions() {
	  
      $tab = array(
         '1' => array(
            'table'         => $this->getTable(),
            'field'         => 'name',
            'name'          => __('Nombre de la relación','Nombre de la relación'),
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
            'field'         => 'invname',
            'name'          => __('Nombre de la relación (sentido inverso)','Nombre de la relación (sentido inverso)'),
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
            'datatype'      => 'dropdown',
            'massiveaction' => true,
         )	 
       );
      return $tab;	  
	  

   }


    public function showForm ($ID, $options=array()) {
      $this->initForm($ID, $options);
      $this->showFormHeader($options);
      
      echo "<tr class='tab_bg_1'>";
      //Nombre de la relación
      echo "<td>".__('Nombre de la relación','Nombre de la relación').": </td><td>";
	  Html::autocompletionTextField($this,"name",array('size' => "15"));
      echo "</td>";
      //Nombre de la relación en sentido inverso
      echo "<td>".__('Nombre de la relación (sentido inverso)','Nombre de la relación (sentido inverso)').": </td>";
      echo "<td>";
      Html::autocompletionTextField($this,"invname",array('size' => "15"));
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
   
   function getRights($interface='central') {

      $values = parent::getRights();

      if ($interface == 'helpdesk') {
         unset($values[CREATE], $values[DELETE], $values[PURGE]);
      }
      return $values;
   }   
}
?>
