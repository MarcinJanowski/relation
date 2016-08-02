<?php

/*
   ------------------------------------------------------------------------
   Relation
   Copyright (C) 2009-2014 by the Relation plugin Development Team.

   https://forge.indepnet.net/projects/barscode
   ------------------------------------------------------------------------

   LICENSE

   This file is part of relation plugin project.

   Plugin Relation is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Plugin Relation is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with Plugin Relation. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   Plugin Relation
   @author    David Durieux
   @co-author
   @copyright Copyright (c) 2009-2014 Relation plugin Development team
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      https://forge.indepnet.net/projects/barscode
   @since     2009

   ------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access directly to this file");
}

class PluginRelationConfig extends CommonGLPI {


   public static function getTypeName($nb = 0) {
      return __('Relation', 'Relation');
   }   
 
   
   static function getMenuContent() {
      global $CFG_GLPI;

      $menu['page'] = "/plugins/relation/front/config.php";
      $menu['title'] = self::getTypeName();

      $menu['options']['typerelation']['page']                      = "/plugins/relation/front/typerelation.php";
      $menu['options']['typerelation']['title']                     = __("Relation types", 'relation');
      $menu['options']['typerelation']['links']['add']              = '/plugins/relation/front/typerelation.form.php';
         $menu['options']['typerelation']['links']['search']        = '/plugins/relation/front/typerelation.php';

      $menu['options']['clase']['page']               = "/plugins/relation/front/clase.php";
      $menu['options']['clase']['title']              = __("Classes", 'relation');
      $menu['options']['clase']['links']['add']       = '/plugins/relation/front/clase.form.php';
         $menu['options']['clase']['links']['search'] = '/plugins/relation/front/clase.php';

      $menu['options']['relationclase']['page']                    = "/plugins/relation/front/relationclase.php";
      $menu['options']['relationclase']['title']                   = __("Relations classes", 'relation');
      $menu['options']['relationclase']['links']['add']       = '/plugins/relation/front/relationclase.form.php';
         $menu['options']['relationclase']['links']['search'] = '/plugins/relation/front/relationclase.php';	  

      return $menu;
   }

   
   static function showConfigPage() {
      global $CFG_GLPI;

      $pbRelation = new PluginRelationRelation();
		
		echo "<div class='center'>";
		echo "<table class='tab_cadre'>";
		echo "<tr><th>".__('Configuration Relations plugin','Configuración plugin Relaciones')."</th></tr>";

		if (Session::haveRight('plugin_relation',CREATE)) {
		   echo "<tr class='tab_bg_1 center'><td>";
		   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/relation/front/typerelation.php' >".__('View or modify types of relationships','Ver o modificar tipos de relaciones')."</a>";
		   echo "</td/></tr>\n";
		 
		   echo "<tr class='tab_bg_1 center'><td>";
		   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/relation/front/clase.php' >".__('View or modify class involved in relationships','Ver o modificar clase que participan en las relaciones')."</a>";
		   echo "</td/></tr>\n";

		   echo "<tr class='tab_bg_1 center'><td>";
		   echo "<a href='".$CFG_GLPI['root_doc']."/plugins/relation/front/relationclase.php' >".__('View or modify relationships between class','Ver o modificar relaciones entre clase')."</a>";
		   echo "</td/></tr>\n";

		}

		echo "</table></div>";
	
   }

    public function getTabNameForItem(CommonGLPI $item, $withtemplate=0)
   {
      switch ($item->getType()) {
         case "PluginFormcreatorConfig":
            $object  = new self;
            $found = $object->find();
            $number  = count($found);
            return self::createTabEntry(self::getTypeName($number), $number);
            break;
         case "PluginFormcreatorForm":
            return __('Preview');
            break;
      }
      return '';
   }  
   

}

?>
