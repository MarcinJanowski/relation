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

// Define actions :
$plugin_relations_columnas = array();

// Install process for plugin : need to return true if succeeded
function plugin_relation_install() {
   global $DB;

   $migration = new Migration(PLUGIN_RELATIONS_VERSION);
   
   if (!file_exists(GLPI_PLUGIN_DOC_DIR."/relation")) {
      mkdir(GLPI_PLUGIN_DOC_DIR."/relation");
   }
   
   if (!TableExists("glpi_plugin_relation_relation")) { // not installed
      $DB->runFile(GLPI_ROOT . '/plugins/relation/sql/script_carm.sql');

   } 
	//CRI. Migrar Datos de 0836
	if (TableExists("glpi_plugin_relations_relations")) {
		plugin_relation_upgradetocarm();
	}   
   
   include_once GLPI_ROOT.'/plugins/relation/inc/profile.class.php';
   include_once GLPI_ROOT.'/plugins/relation/inc/config.class.php';
   PluginRelationProfile::initProfile();

   return true;
}



// Uninstall process for plugin : need to return true if succeeded
function plugin_relation_uninstall() {
	global $DB;

	$tables = array('glpi_plugin_relation_relations',
					'glpi_plugin_relation_typerelations',
					'glpi_plugin_relation_relationclases',
					'glpi_plugin_relation_clases');

	foreach ( $tables as $table ){
		$DB->query("DROP TABLE `$table`");
	}
	
	$query = "DELETE FROM `glpi_displaypreferences`
		WHERE `num` = 2250 OR `num` = 2251";
	$DB->query($query);
	
   
   include_once GLPI_ROOT.'/plugins/relation/inc/profile.class.php';
   PluginRelationProfile::removeRights();

   return true;
}


function plugin_relation_postinit() {
   global $CFG_GLPI, $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['plugin_uninstall_after']['relation'] = array();
   $PLUGIN_HOOKS['item_purge']['relation'] = array();

   foreach (PluginRelationRelation::getTypes(true) as $type) {
   
      $PLUGIN_HOOKS['item_purge']['relation'][$type]
         = array('PluginRelationRelation','cleanForItem');

      CommonGLPI::registerStandardTab($type, 'PluginRelationRelation');
   }   
   
	//$PLUGIN_HOOKS['add_css']['relation']=array('dd.css');
	//$PLUGIN_HOOKS['add_javascript']['relation']=array('jquery-1.9.0.min.js','jquery.dd.min.js');      
}

////// SEARCH FUNCTIONS ///////

// Define search options
function plugin_relation_getAddSearchOptions($itemtype){

	$sopt = array();

	if (Session::haveRight("plugin_relation", CREATE)) {

	$sopt['relation'] = 'Relaciones';
	
		if ( in_array($itemtype, PluginRelationRelation::getTypes(true)) ){

			$sopt[2250]['table'] = 'glpi_plugin_relation_relations';
			$sopt[2250]['field'] = '';
			$sopt[2250]['linkfield'] = 'parent';
            $sopt[2250]['name']           = PluginRelationRelation::getTypeName(2)." - ".
                                         __('Inversa');
			$sopt[2250]['datatype']	= 'itemlink';
			//$sopt[2250]['datatype']	= 'dropdown';
			$sopt[2250]['itemlink_type'] = $itemtype;
			$sopt[2250]['forcegroupby']  = true;
            $sopt[2250]['massiveaction'] = false;


			$sopt[2251]['table'] = 'glpi_plugin_relation_relations';
			$sopt[2251]['field'] = '';
			$sopt[2251]['linkfield'] = 'children';
            $sopt[2251]['name']           = PluginRelationRelation::getTypeName(2)." - ".
                                         __('Directa');
			$sopt[2251]['forcegroupby'] = true;
			//$sopt[2251]['datatype'] = 'dropdown';
			$sopt[2251]['datatype'] = 'itemlink';
			$sopt[2251]['itemlink_type'] = $itemtype;
            $sopt[2251]['massiveaction'] = false;			
         
			
			//$sopt[2251]['link2'] = 'children2';
			//$sopt[2251]["splititems"] = true;
		}
	}
			
	return $sopt;
}

/*
Calidad CARM funcion: plugin_relation_addSelect
Modificado por Oscar, 25/10/2013
Para poder filtrar por nombre de los objetos relacionados, relacion directa e indirecta.
*/
function plugin_relation_addSelect($type,$ID,$num){
    global $plugin_relations_columnas; 
	
	$out = '';
	switch($ID) {
		case 2250 :
			$out = "GROUP_CONCAT(DISTINCT CONCAT(glpi_plugin_relation3.parent_type, '$$' , glpi_plugin_relation3.parent_id, '$$',glpi_plugin_relation3.relation_type) 
			       SEPARATOR '$$$$') AS ITEM_$num, ";		   
	   		//$plugin_relations_columnas[2250]="CONCAT(glpi_plugin_relations3.parent_type, '$$' , glpi_plugin_relations3.parent_id, '$$',glpi_plugin_relations3.relation_type)";
			if (plugin_relation_parent_filterAnd($type)=="")
			{
				$plugin_relations_columnas[2250]="glpi_plugin_relation3.relation_type";
			}
			else
			{
				$plugin_relations_columnas[2250]=plugin_relation_parent_filterAnd($type);
			}
			break;
		case 2251 :
			$out = "GROUP_CONCAT(DISTINCT CONCAT(glpi_plugin_relation2.itemtype, '$$' , glpi_plugin_relation2.items_id, '$$',glpi_plugin_relation2.relation_type) 
			       SEPARATOR '$$$$') AS ITEM_$num, ";
			//$plugin_relations_columnas[2251]="CONCAT(glpi_plugin_relations2.itemtype, '$$' , glpi_plugin_relations2.items_id, '$$',glpi_plugin_relations2.relation_type)";
			if (plugin_relation_children_filterAnd($type)=="")
			{
				$plugin_relations_columnas[2251]="glpi_plugin_relation2.relation_type";
			}
			else
			{
				$plugin_relations_columnas[2251]=plugin_relation_children_filterAnd($type);
			}			
			
			break;
	}
	return $out;
}

function plugin_relation_parent_filterAnd($itemtype)
{
	global $DB;
	$sql = "select parent_type from glpi_plugin_relation_relations where itemtype = '".$itemtype."' group by parent_type";
	$fstringsql = "";
	$stringsql = "";
	$result = $DB->query($sql);
	
		if ($DB->numrows($result)){
			$stringsqlcab = "concat_ws(' ',";
			while ($data=$DB->fetch_array($result)){
				if (getTableForItemType($data[0])=="glpi_users")
				{ 
					$stringsql .= getTableForItemType($data[0])."3.name,".getTableForItemType($data[0])."3.firstname,".getTableForItemType($data[0])."3.realname,"; 
				}
				else
				{ 
					$stringsql .= getTableForItemType($data[0])."3.name,"; 
				}
			}
			$stringsqlpie= ")";
			$stringsql=substr($stringsql,0,strlen($stringsql)-1);
			$fstringsql=$stringsqlcab.$stringsql.$stringsqlpie;
		}
	return $fstringsql;
	
}

function plugin_relation_children_filterAnd($parent_type)
{
	global $DB;
	$sql = "select itemtype from glpi_plugin_relation_relations where parent_type = '".$parent_type."' group by itemtype";
	$fstringsql = "";
	$stringsql = "";
	$result = $DB->query($sql);
	
		if ($DB->numrows($result)){
			$stringsqlcab = "concat_ws(' ',";
			while ($data=$DB->fetch_array($result)){
				if (getTableForItemType($data[0])=="glpi_users")
				{ 
					$stringsql .= getTableForItemType($data[0])."2.name,".getTableForItemType($data[0])."2.firstname,".getTableForItemType($data[0])."2.realname,"; 
				}
				else
				{ 
				$stringsql .= getTableForItemType($data[0])."2.name,"; 
				}	
				
			}
			$stringsqlpie= ")";
			$stringsql=substr($stringsql,0,strlen($stringsql)-1);
			$fstringsql=$stringsqlcab.$stringsql.$stringsqlpie;
		}
	return $fstringsql;
	
}

/*
Calidad CARM funcion: plugin_relations_addLeftJoin
Modificado por Oscar, 25/10/2013
Para poder filtrar por nombre de los objetos relacionados, relacion directa e indirecta.
*/

// Define how to join the tables when doing a search
function plugin_relation_addLeftJoin($type,$ref_table,$new_table,$linkfield,&$already_link_tables)
{
	if ( $new_table == 'glpi_plugin_relation_relations')
	{
		if( $linkfield == 'parent' ){

			$out = "LEFT JOIN glpi_plugin_relation_relations glpi_plugin_relation3
				ON ( $ref_table.id = glpi_plugin_relation3.items_id  and glpi_plugin_relation3.itemtype = '$type') \n ".plugin_relation_parent_obtenerLeftjoin($type); 
		}
		else {
			$out= "LEFT JOIN glpi_plugin_relation_relations glpi_plugin_relation2
				ON ( $ref_table.id = glpi_plugin_relation2.parent_id  and glpi_plugin_relation2.parent_type = '$type') \n ".plugin_relation_children_obtenerLeftjoin($type);  

		}
		return $out;
	}
}

function plugin_relation_parent_obtenerLeftjoin($itemtype)
{
	global $DB;
	$sql = "select parent_type from glpi_plugin_relation_relations where itemtype = '".$itemtype."' group by parent_type";
	$stringsql = "";
	$result = $DB->query($sql);
	
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				$stringsql .= " LEFT JOIN ".getTableForItemType($data[0])." AS ".getTableForItemType($data[0])."3 ON ( ".getTableForItemType($data[0])."3.id = glpi_plugin_relation3.parent_id and glpi_plugin_relation3.parent_type = '".$data[0]."') \n";			
				
			}
		}
	return $stringsql;
	
}

/*
Calidad CARM
Añadido por Oscar, 25/10/2013
Para poder filtrar por nombre de los objetos relacionados, relacion directa e indirecta.
*/
function plugin_relation_children_obtenerLeftjoin($parent_type)
{
	global $DB;
	$sql = "select itemtype from glpi_plugin_relation_relations where parent_type = '".$parent_type."' group by itemtype";
	$stringsql = "";
	$result = $DB->query($sql);
	
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				$stringsql .= " LEFT JOIN ".getTableForItemType($data[0])." AS ".getTableForItemType($data[0])."2 ON ( ".getTableForItemType($data[0])."2.id = glpi_plugin_relation2.items_id and glpi_plugin_relation2.itemtype = '".$data[0]."') \n";			
				
			}
		}
	return $stringsql;
	
}

function plugin_relation_addWhere($link,$nott,$type,$ID,$val) {
	global $plugin_relations_columnas;
	
	$out='';
	switch($ID) {
		case 2250: $out="$link ($plugin_relations_columnas[2250] LIKE '%$val%')"; break;
		case 2251: $out="$link ($plugin_relations_columnas[2251] LIKE '%$val%')"; break;
	}
	return $out;
}

// Return the string that will be displayed in device views
function plugin_relation_giveItem($type,$ID,$data,$num) {
	global $DB;

	$searchopt = &Search::getOptions($type);
	if (isset($searchopt[$ID]["itemlink_type"]))
	{
		$form = Toolbox::getItemTypeFormURL($searchopt[$ID]["itemlink_type"]);
	}

	$separator = "<br/>";
	$dataraw = $data['raw'];
	$split = explode("$$$$",$dataraw['ITEM_'.$num]);
	$count = 0;
	$string = "";
	//print_r($split);
	for ( $i = 0; $i < count($split); $i++ ){
		if ( strlen(trim($split[$i])) > 0 ){
			$item = explode("$$",$split[$i]);
			if ( isset($item[0]) && isset($item[1]) && $item[1] > 0 ){
				if ( $count ){
					$string .= $separator;
				}
				$count++;
				if ( ! class_exists($item[0]) ) {
					continue;
				}
				$objeto = new $item[0]();
				
				if ($objeto->getFromDB($item[1])) {
					$nombre_objeto = $objeto->getNameID();
					//if ( $objeto->canView() ) {
						$form = Toolbox::getItemTypeFormURL($item[0]);
						$string .= "<a id='PluginRelationRelation' href='";
						$string .= $form . "?id=" . $item[1] . "'>";
						switch($ID) {
							case 2250:
								$string .= $nombre_objeto. "</a>". " Clase: ". $item[0] . " Tipo Relacion: ". PluginRelationRelation::getNombreTiporelacion($item[2], 1)  ;
							    break;
							case 2251: 
								$string .= $nombre_objeto. "</a>". " Clase: ". $item[0] . " Tipo Relacion: ". PluginRelationRelation::getNombreTiporelacion($item[2], 0)  ;
							    break;
						}				
						
					//}					
				}				
				unset($objeto);
			}
		}
	}
	return $string;
}

function plugin_relation_upgradetocarm() {

	global $DB;

   //INICIALIZAR LAS TABLAS DEL PLUGIN CON LOS DATOS
   
   if (TableExists("glpi_plugin_relations_clases")) {
      $query = "TRUNCATE TABLE `glpi_plugin_relation_clases`";
      $DB->query($query) or die($DB->error());
	  
	  $query = "INSERT INTO `glpi_plugin_relation_clases` SELECT * FROM `glpi_plugin_relations_clases`";
	  $DB->query($query) or die($DB->error());
	 	  
   }
   
   if (TableExists("glpi_plugin_relations_relations")) {
      $query = "TRUNCATE TABLE `glpi_plugin_relation_relations`";
      $DB->query($query) or die($DB->error());
	  
	  $query = "INSERT INTO `glpi_plugin_relation_relations` SELECT * FROM `glpi_plugin_relations_relations`";
	  $DB->query($query) or die($DB->error());
	 	  
   }
   
   if (TableExists("glpi_plugin_relations_tiposrelaciones")) {
      $query = "TRUNCATE TABLE `glpi_plugin_relation_typerelations`";
      $DB->query($query) or die($DB->error());
	  
	  $query = "INSERT INTO `glpi_plugin_relation_typerelations` SELECT * FROM `glpi_plugin_relations_tiposrelaciones`";
	  $DB->query($query) or die($DB->error());
	 	  
   }
   
    if (TableExists("glpi_plugin_relations_relacionesclases")) {
      $query = "TRUNCATE TABLE `glpi_plugin_relation_relationclases`";
      $DB->query($query) or die($DB->error());
	  
	  $query = "INSERT INTO `glpi_plugin_relation_relationclases` SELECT * FROM `glpi_plugin_relations_relacionesclases`";
	  $DB->query($query) or die($DB->error());
	 	  
   } 
   
    if (TableExists("glpi_plugin_relations_profiles")) {

	  $query = "SELECT id, relations FROM `glpi_plugin_relations_profiles`";
	  $result = $DB->query($query) or die($DB->error());
	
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
					  $query = "INSERT INTO `glpi_profilerights` (`profiles_id`, `name`, `rights`)
								VALUES (".$data[0].",'plugin_relation',".plugin_relation_translatearight($data[1]).")";
					  $DB->query($query) or die($DB->error());
				}
		}	  
  
   }  

   $tables = array(
		// Propios del plugins
      'glpi_plugin_relations_clases',
      'glpi_plugin_relations_relations',	
      'glpi_plugin_relations_tiposrelaciones',
      'glpi_plugin_relations_relacionesclases',	
	  'glpi_plugin_relations_profiles'
   );
   
   foreach ($tables as $table) {
      $DB->query("DROP TABLE `$table`");
   }   
   
}

function plugin_relation_translatearight($old_right) {
      switch ($old_right) {
         case '': 
            return 0;
         case 'r' :
            return READ;
         case 'w':
            return CREATE;
         case '0':
         case '1':
            return $old_right;
            
         default :
            return 0;
      }
}
   
?>