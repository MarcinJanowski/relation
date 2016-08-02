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

/**
 * Class to generate relations using PEAR Image_Relation
 **/
class PluginRelationRelation extends CommonDBTM{

   //static $rightname = 'plugin_relation';

   	static function getTypeName($nb=0){
		return _n('Relation', 'Relation', $nb, 'relation');
	}
   
   /**
	 * Constructor
	**/
   function __construct() {
      $this->docsPath = GLPI_PLUGIN_DOC_DIR.'/relation/';
   }


	public function getListClaseHelpDesk()
	{
		global $GO_CLASS_RELATIONS;
	  	//$array = array_unique($_SESSION["glpiactiveprofile"]["helpdesk_item_type"]);
		$array = array_merge($GO_CLASS_RELATIONS,$_SESSION["glpiactiveprofile"]["helpdesk_item_type"]);
		$array = array_unique($array);		
		sort($array);
		//$array = array_unique($DEBUG_AUTOLOAD);
		//integer j;
		/*
		for ($i = 0; $i < count($array); ++$i) {
				$name = $array[$i];
				echo $name;
				$class = new $name();
				
				if ($class->getField('is_helpdesk_visible'))
				{
					echo $class;
				}

			
		}*/

		
		//print_r($array);
		//array_splice($array, 0, 0, "Ticket");
		//array_splice($array, 1, 0, "Problem");
		//array_splice($array, 2, 0, "User");	
		//array_splice($array, 3, 0, "Group");
		//array_splice($array, 4, 0, "PluginFormcreatorForm");
		return $array;
	}	
	
   public function dropdownType($myname, $value=0) {
      global $LANG;
		$array = self::getListClaseHelpDesk();
		//print_r($array);
		Dropdown::showFromArray($myname, $array,  // Domain
                              array ('value' => $value));
   }

   public function getIdDropdown($name) {
      global $LANG;
	  
		$id= 0;
		$array = self::getListClaseHelpDesk();
		
		for ($i = 0; $i < count($array); ++$i) {
			if ($array[$i] == $name){
				$id = $i;
			}
		}
		
		return $id;
	}   
	
	public function dropdownClase($myname, $value=''){
	global $DB,$LANG,$CFG_GLPI;
	$query = "select id, name from glpi_plugin_relation_clases order by 1";
		$result=$DB->query($query);
		//Desplegable clases
		echo "<select name=$myname id=$myname>\n";
		echo "<option value='-1'>------</option>\n";			
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				echo "<option value=\"".$data[1]."\"";		
				if (rtrim($value) == rtrim($data[1]))
				{
					echo " selected ";			
				}
				echo " >".$data[1]."</option>\n" ;
			}
		}
		echo "</select>\n";		
		
		
	}  	

   public function getNameDropdown($array,$index) {
      global $LANG;
	  $name= '';
		for ($i = 0; $i < count($array); ++$i) {
			if ($i ==$index){
				$name = $array[$i];
			}
		}
		
		return $name;
	}	
	
	
	/*
	Metodos de visualización
	*/
	public static function getTypes($all=false){
        global $DB;

		$types = array();
        //En $query tengo una select que me obtiene todas las clases que intervienen en las relaciones separadas por comas
		$query = "select group_concat(distinct name separator ',') as clases from glpi_plugin_relation_clases";
		$result = $DB->query($query);
		
		if ( $data = $DB->fetch_array($result) ){
			$arrayClases = explode(',',$data[0]);
			//$arrayClases['Profile']="Profile";
			$i=0;
			foreach ( $arrayClases as $key => $type ) {
				$clase = $type;
				if ( ! class_exists($clase) ) {
					continue;
				}
				$item = new $clase();
				if ( $item->canView() ) {
					$types[$i]=$clase;
					$i=$i+1;
				}	
			}
				

		}
		return $types;
	}	
	
    public function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
 		$type = get_Class($item);

		if ( in_array($type,PluginRelationRelation::getTypes(true)) ){
			// template case
			$id = $item->getField('id');
			if ( $withtemplate || $id < 0 || $id == '' )
				return array();
			// Non template case
			else 
				return array(1 => __('Relation','relation'));
		}
		else
			return false;	
   }
   

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
		$type = get_Class($item);
		if ( in_array($type,PluginRelationRelation::getTypes(true)) )
		{
				self::showAssociated($item);
		}
		else
		{
				return false;
		}				
	    return true;
   }		
	
	
	static function showAssociated($item,$withtemplate=''){
		global $DB,$CFG_GLPI;

		$display_entity = Session::isMultiEntitiesMode();
		$numcols = 5;
		if ( $display_entity ) $numcols++;

		$ID = $item->getField('id');
		$itemtype = get_Class($item);

		$entity = $item->getEntityID();

		$showgroup=true;

		if ( ! class_exists($itemtype) ) {
			$datatable = "";
			$form="";
		} else {
			$objeto = new $itemtype();
			$datatable = $objeto->getTable();
			$form = Toolbox::getItemTypeFormURL($itemtype);
		}

			if( $showgroup ){
				$moreselect=", g.name AS grp";
				$morejoin=" LEFT JOIN glpi_groups AS g ON d.groups_id = g.id";
			}
			else {
				$numcols--;
				$moreselect="";
				$morejoin="";
			}

		if ( $withtemplate != 2 ) 

		//echo "<form method='post' action=\"".$CFG_GLPI["root_doc"]."/plugins/relation/front/relation.form.php\">";
		echo "<form method='post' name='relation_form' id='add_relation_form'  action=".PluginRelationRelation::getFormURL(true).">";
		
		echo "<div align='center'><table class='tab_cadre_fixe'>";


		// Children
		$query4="SELECT `name` FROM `$datatable` WHERE `id` = '$ID'";
		$result4 = $DB->query($query4);
// // 		$thisdata = $DB->fetch_array($result4);

		echo "<tr><th colspan='".$numcols."'>".__('Related items','Elementos relacionados')."</th></tr>";
		// CRI 26/11/2015 Cambio de orden de columna tipo de relacion
		echo "<tr>";
		
		if($showgroup)
			echo "<th>".__('Relation type','Tipo Relacion')."</th>";
		
		echo "<th>".__('Name','Nombre')."</th>";
		
		echo "<th>".__('State','Estado')."</th>"; // CRI 2.0 Añadir Estado del CI 11/12/2014
		
		if ($display_entity)
			echo "<th>".__('Entity','Entidad')."</th>";
		echo "<th>".__('Type','Tipo')."</th>";

		if(Session::haveRight("plugin_relation", CREATE))
			echo "<th>&nbsp;</th>";
		echo "</tr>";

		//imprimo las relaciones directas
		$queryAssociated = "select r.id, r.items_id, r.itemtype, tr.name from glpi_plugin_relation_relations r left join glpi_plugin_relation_typerelations tr on r.relation_type=tr.id  where r.parent_id=";
		$queryAssociated .= $ID. " and r.parent_type='".$itemtype."'order by 4,3,2";
		
        $resultAssociated = $DB->query($queryAssociated);
		if ($DB->numrows($resultAssociated)){
			while ($data=$DB->fetch_array($resultAssociated))
			{
				//print_r($data);
				//echo "<br>";
                $nombreClase = $data['itemtype'];
				$form = Toolbox::getItemTypeFormURL($nombreClase);
	            $objAsociado = new $nombreClase();
				$objAsociado->getFromDB($data['items_id']);
				
			
				echo '<tr class="tab_bg_1">';
				
				// CRI 2.0 Cambio de orden de columna tipo de relacion
				if($showgroup){
					echo '<td align="center">'.$data['name'].'</td>';
				}
				
				if ($data['itemtype']=="User"){
				$user = getUserName($data['items_id'],2);
				echo '<td align="center"><a href="'.$form.'?id='.$data['items_id'].'">'.getUserName($data['items_id']).' ('.$objAsociado->fields['name'].")&nbsp;".Html::showToolTip($user["comment"],
                                                                 array('link'    => $user["link"],
                                                                       'display' => false));
				//getUserName($data['items_id'])
				echo '</a></td>';				
				}
				else {

				echo '<td align="center"><a href="'.$form.'?id='.$data['items_id'].'">'.$objAsociado->fields['name'];
				if ($_SESSION["glpiis_ids_visible"]) echo " (".$data["items_id"].")";
				echo '</a></td>';
				}
				
				echo '<td align="center">'.PluginRelationRelation::getStatusItem($data['itemtype'],$data['items_id']).'</td>';//CRI 2.0 Añadir Estado del CI 11/12/2014
				
				if ($display_entity){
					if ($objAsociado->fields['entities_id']==0)
						echo "<td align='center'>".__('Entity root','Entidad Raiz')."</td>";
					else
						echo "<td align='center'>".Dropdown::getDropdownName("glpi_entities", $objAsociado->fields['entities_id'])."</td>";
						//echo "<td align='center'>".$objAsociado->fields['entities_id']."</td>";
				}
				echo '<td align="center">'.PluginRelationRelation::getViewNameClass($data['itemtype']).'</td>'; // Gobierno TI: [olb26s] uso de funcion getViewNameClass

				if(Session::haveRight('plugin_relation',CREATE))
					if ($withtemplate<2)
						echo "<td align='center' class='tab_bg_2'><a href='".$CFG_GLPI["root_doc"]."/plugins/relation/front/relation.form.php?deleterelation=deleterelation&amp;id=".$data['id']."'>".__('Delete','Eliminar')."</a></td>";
				echo '</tr>';
			}
		}

		//imprimo las relaciones inversas
		$queryAssociated = "select r.id, r.parent_id, r.parent_type, tr.invname from glpi_plugin_relation_relations r left join glpi_plugin_relation_typerelations tr on r.relation_type=tr.id  where r.items_id=";		
		$queryAssociated .= $ID. " and itemtype='".$itemtype."' order by 4,3,2";
		
        $resultAssociated = $DB->query($queryAssociated);
		if ( $DB->numrows($resultAssociated) >0 ){
			while ($data=$DB->fetch_array($resultAssociated))
			{

                $nombreClase = $data['parent_type'];
				$form = Toolbox::getItemTypeFormURL($nombreClase);
	            $objAsociado = new $nombreClase();
				$objAsociado->getFromDB($data['parent_id']);

				echo '<tr class="tab_bg_1">';
				
				// CRI 2.0 26/11/2015 Cambio de orden de columna tipo de relacion
				if($showgroup) {
					echo '<td align="center">'.$data['invname'].'</td>';
				}
				
				if ($data['parent_type']=="User"){
				$user = getUserName($data['parent_id'],2);
				echo '<td align="center"><a href="'.$form.'?id='.$data['parent_id'].'">'.getUserName($data['parent_id']).' ('.$objAsociado->fields['name'].")&nbsp;".Html::showToolTip($user["comment"],
                                                                 array('link'    => $user["link"],
                                                                       'display' => false));
				echo '</a></td>';				
				}
				else {
				echo '<td align="center"><a href="'.$form.'?id='.$data['parent_id'].'">'.$objAsociado->fields['name'];

				if ($_SESSION["glpiis_ids_visible"]) echo " (".$data["parent_id"].")";
				echo '</a></td>';
				}
				
				echo '<td align="center">'.PluginRelationRelation::getStatusItem($data['parent_type'],$data['parent_id']).'</td>';//CRI 2.0 Añadir Estado del CI 11/12/2014
				
				if ($display_entity){
					if ($objAsociado->fields['entities_id']==0)
						echo "<td align='center'>".__('Entity root','Entidad Raiz')."</td>";
					else
						echo "<td align='center'>".Dropdown::getDropdownName("glpi_entities", $objAsociado->fields['entities_id'])."</td>";
				}
				echo '<td align="center">'.PluginRelationRelation::getViewNameClass($data['parent_type']).'</td>'; // Gobierno TI: [olb26s] uso de funcion getViewNameClass

				if(Session::haveRight('plugin_relation',CREATE))
					if ($withtemplate<2)
						echo "<td align='center' class='tab_bg_2'><a href='".$CFG_GLPI["root_doc"]."/plugins/relation/front/relation.form.php?deleterelation=deleterelation&amp;id=".$data['id']."'>".__('Delete','Eliminar')."</a></td>";
				echo '</tr>';
			}
		}
		
		
		if ( Session::haveRight('plugin_relation',CREATE) ){
			echo '<tr class="tab_bg_1">';
			echo '<td align="center" colspan="'.($numcols-1).'">';
			echo '<input type="hidden" name="id" value="'.$ID.'">';
			echo '<input type="hidden" name="type" value="'.$itemtype.'">';
			PluginRelationRelation::dropdown(array('name'   => "childID",
								'itemtype' => $itemtype,
								'entity' => $entity));
			echo '</td>';
			echo '<td align="center"><input class="submit" type="submit" value="'._sx('button','Add').'" name="additem"/></td>';
			
			echo '</tr>';
		}

	
		if ( ! empty($withtemplate) )
			echo "<input type='hidden' name='is_template' value='1'>";

		echo "</table></div>";
		//echo "</form>";
        Html::closeForm();		
		
		// Imprimir Grafico
		
		if (PluginRelationRelation::command_exists('dot')==true) {
			  echo "<br><table class='tab_cadre' cellpadding='2'>";
			  echo "<tr><th>".__('Graphic of Relationship','Grafico de la Relacion')."</th></tr>";
			  echo "<tr class='tab_bg_1'><td>";
			  $im = PluginRelationPrototype::relationGraphviz($ID,$itemtype);
			  //echo '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJMAAAFYCAYAAABXvc7WAAAABmJLR0QA/wD/AP+gvaeTAAAgAElEQVR4nO2deXxU1fn/P3eZLZlkkkAWAiQhhEXcEBAURaggbq3SiiIERGyLdvnWbv6k1X6/drOb/db6besGLrVgbRCUzRAUQaCyK2JZZE+AJGxZZ73L8/vjZIYEJgkh986dGc6b133NcJdzn8l85jnnnuV5BCIicDjdZ6totQWc5IGLiWMYXEwcw5CtNqANmzYBlZVWW5E4jBoFFBRYbUWE+BLTc88BCxZYbUXiMH8+MG2a1VZEiL9qbto0gIhvnW1xSPyJiZOwcDFxDIOLiWMY8dUANxGv14f6hgboRNA0DbqmIzu7B9LcbqtNSxqSXkyapqH2xElU15xAQ1MTGpuacfpMHbxeH/r07oVRI4aid69eVpuZFCS1mDRdx8HDR3DqdB0CwRBOnq5HdU0tak+cQH19Az77fDeamprxldsnICsz02pzE56kFtPuPXvh9yuoq2vAho834ciRKjT7fAgEApAkCTeMvh4HD1di6/YdmDh+nNXmJjxJK6YzZ+rw5sL34E5PwwcVq3D40H6AgJSUVKS4UzH5a3fD4UrBx+9vRt/8POi6DlHkzyPdIWnFlJWViRXv/gtHKyuRmuaBIMjQBQEhVUWmQ0ZxSTEWL1mBZq8XgaCfi8kAklZMAPD8n3+Dqr2foLGxEbUnTuFYzSn4/UEU9y/C+o3b0djkhU6Aw+HiQjKApBZTUb9i5GQ4kerJgq+pAWdqj0EJ+PDJ53vw4ZrtSEtxQlcVuFO4mIwgqf+CdocT3sZ6aIoCm90BT2YPpKSlQ9AU9EoXcU1JDhSfF3k5Pa02NSlIajGlpLrh9/vg9zbC722EooQQCoXgcdtx59hrcKK2Gvl52bjyisutNjUpSGoxybIdqi7C23AGgeYmBP1eNDfUAZoKj9uJqqO1uO66UcjKsq6PSRCEdo+NGTMmhpZ0n6RuMwmiiMzsfJw5uhc2uwOapqKh7jR0XcPpE8cx5asTMHzcrXHbXlq3bp3VJnSJ+PwrGoQkSehdUIxAMAiftwnNTQ3wNjfA6/XC2xxAdpYHfm8DYrVAZ+HChfB4PJg1a9Z5HkkQBMyePRsejwdvv/12ZF8ikdRi0jQNvqAK3Z4Or7cRtcercOTAQZw81YDBV10DwZGGrZ/uwXsr34fP7zfdnp/85Cd48803MXny5KjHJ06ciPnz5+Oxxx4z3RYzSNpqbv/BQ6g9cQYffvQRjhzcjxuGDsCxymqQYMOoG29CSmYu4MhE4Phn+MuL81B19DhuGT8WRYUFplV7hw8fxoQJE2C326Menzx5MoLBICoTdB58Uorp5KnTeGd5BY4crUFNdTX69++HwcNuwOD+fQFBQFZeIWB3wx8IAoIIRSO89Mob2LL9U3z59okYd9NoZHg8httVWFiI8vJyaJoW9fjSpUsBACUlJYbfOxYkpZg2bdkGSXZAEARk9eiBQQMGIDurB+w5WUhJdUOQbFBVFSf2HcChymPo3acvGurqsXHLduzdfxCf7vwPvvWNmcjNyTbUrqeffhozZszA3XffHfX44sWLsWjRIrz++uuG3jdWJGWbqfJYNbw+P2pra0BECAW8WP95Jf7wxjo0BQjpaWlQNR0rV69Dj+xcZGRkoEePHuhfUoKU1DS8s2wF1m/42HC77rvvPjQ0NOChhx5CWloaALRp/L/yyiuor6+PiC3RVu4nnWcKhUJQVA21J0/g0MFDyM3Lww5osI/oh+3Hm7Hv4GFkeFLwh+deRP+SEsiyDcFgCFk9eqBnbi7Wf/QRjh87jn0HDhlu27hx47B27VpkZ2fj97//veHlW03Sicnr9eHQkSPYuHEzmpuboVZVwZ2ZATmzDzILGlBUVIQ5//MbpKS4cfDgYdjtdhw5dAjOVDf2ffEFvD4fZNmOXXu/gKZpkCTJMNvWrFnT7rFE80LRSDoxNTY14WjVUdSfqYMoilA1FZXNGpp37scQXx0+WL0ae/fuRSikwm63w9vUBJvDAbc7DXaHHT179kRDQz0qq47ieHUN+vTOT7j+HqtIOjE1NDai2euDqqqQZRk6EU5Wn4KibcNhbxWqt1SjobERTY1N0DUVnsxMqH4/ZEmCJyMDBw4chN1uh8/nw/4DB5GT3RMOh8Pqj5UQJF0DvKnZi0AgCE3XIckyNE2HVncGtqo90H218AV8CPgD0HUdaRkZsDscCAQCUFQVdXVnoOsaJElGMKTgi30H0NzstfojJQxJJ6bmpib4mptBug5Zllm7RwsihRogCwr8Ph8EUUBGViacTidCoRBUTWUNd0WB0+GEzWaDrhM++3wXak6csPojJQxJJ6YRw67Bt2fPRIYnDbquQ9M02B0CZDEAb3MTembnIicvDy6XC6qqQlVVaJoORVVbznXAbreDBBH7Dh7CsePVVn+khCHpxNSjRxam3jcZ99x1B1RFaRGUgmDQBwAIBPwIBYPMI6kqiAiqGvZMKux2GxwOB0RBQF19PY4dO4ZAIGDxp0oMkk5MAOBwOPDUz36CH37nG7BJAhQlBE3XAYH1QwUCASiKAlEUQUQQBQGqoiIUCkIQRLhcLjgddhT2zUdhQV/YbDarP1JCkHRPc615ZPbXMeXee1D29mKs+/cmnDh5Cl5/ECFFgUI6RFGAzSYjM8MDW4tHyvCkwW6TcP2IL2H2N2ZFeqo5nZPUYgKAzMwMzP7GLMyaOR1Hjx/H7t17ceDgYZw+cwYQBDjsNvTJz0fPnj3g8/mQlZWFK4ZchuxsPi+8qyS9mMLYbDb0KyxEv8JCq01JWpKyzcSxBi4mjmFwMXEMg4uJYxhcTBzD4GLiGAYXE8cwLhkxRVv0aCSJtpTbDC4ZMZlNoi3lNoNLXkxlZWXo1asXBEHAuHHjALT1WuH348aNgyAIGDJkCHbv3h05NmfOnDbnlZeXIy8vD3l5eVi1alUMP0kcQPHEtGlsM4FzP2r4/7m5uTRnzhzyer1Rzz33uj179lBRUVHk2KFDh9qcN3DgQFqxYgUtWbKEBg8ebPjnaGUY0fz55pXfdbZcMp5JkiR4vWwKblNTE2SZDUuuX78eVVVVuPzyy/HBBx+0e/3cuXMxePBgDB06FIcPH47sLyoqanPegQMHMH78eEycOBH79+83/HPEM5fMQO/w4cPx5z//GT/4wQ/wzDPPYPjw4QDYUux//OMfqKiowLRp01BbW4u8vDzs2LED27Zti1z//e9/HxUVFRgxYkSHCwyKi4uxcuVK6LqesMu8LxqrfWMbTKzmPvvsMxo6dCjJskxDhw6lzz77jIiIxo4dSwAoOzubysrKiIjo448/ptzcXPrpT38aqb6mTZtGoigSAAJA1dXVUavD5cuXU05ODuXm5lJFRYUpn6XlhnFXzQlEcbT6r7SUvc6fb60diYAgxFvyQp49nGMcXEwcw+Bi4hgGFxPHMLiYOIYRf/1MZWXApElWW8G5COJPTIoC3Hef1VZEWA/gOQD/stqQBCC++pnikAULFqC0tDQpgnGZDO9n4hgHFxPHMLiYOIbBxcQxDC4mjmFwMXEMg4uJYxhcTBzD4GLiGAYXE8cwuJg4hsHFxDEMLiaOYXAxcQyDi4ljGFxMHMPgYuIYBhcTxzC4mDiGwcXEMQwuJo5hcDFxDIOLiWMYXEwcw4i/Fb1xyrlxw+NtUaYgCJbbxMXUBWLxZcWDKC4WXs11g2gxxFsjCAJmz54Nj8eDt99+G0DH8cTD3i/8umrVKvTu3Rt5eXn46KOPzit/4cKF8Hg8ePDBByP7OrvGVCwLp5kgzJ8/PxIUtfVGFD2GeGsAUFlZGS1dupT69evX5lhn8cSJiAYNGkSrV6+mtWvX0rBhw84rv6SkhJYvX07Lli2LXNfZNSayhYupE1qL6Vz27dtHpaWlVFRURO+///55x8PXBAIBkiSJiIhefvllGjRoEDmdzshxtBPEXpKkyL1lWT6vfFmWKRgMtrmus2tMhIupMzoSU5iVK1dSTk7OefsB0JIlS2jJkiU0aNAgIiJKTU2lDRs2UDAYjComu91OgUCAiIgGDx5Ma9asafe+JSUl9O6779KiRYsiZXR2jYlwMXVGR9VctBjirQFAs2bNIo/HQ++88w4RdR5P/LXXXqPx48cTEdHq1aupsLCQANDYsWPPK3/hwoWUnp5ODzzwQKSMzq4xkTiLAx6HdCc+UyI/mV0EPD4Txzi4mEzkEvJKALiYOAbCxcQxDC4mjmFwMXEMg4uJYxhcTBzD4GLiGAYXE8cwuJg4hsFnWrbi+PHj6N27d9Rj507bvfHGG7Fu3bpYmJUwcM/Uivz8fBQXF1/QudPiJ9Fy3MDFdA6PP/44JEnq8BxJknDvvffGyKLEgYvpHCZPnnxeldYaSZLwpS99CT179oyhVYkBF9M5ZGVl4dZbb4UsR29OEhEeeOCBGFuVGHAxRaG0tBS6rkc9ZrPZMImnfY0KF1MU7rrrLtjt9vP2y7KMr3zlK0hLS7PAqviHiykKqampmDRpEmw2W5v9mqbxp7gO4GJqh+nTp0NRlDb7UlNTcccdd1hkUfzDxdQOEydORHp6euT/NpsN9913HxwOh4VWxTdcTO1gs9kwZcqUSFWnKApKS0sttiq+4WLqgNLS0khV16NHD4wdO9Zii+IbLqYOGDNmDHJzcwGwNlRnPeOXOnygtxWKAjQ2Ag0N7H1zs4iSkpGorV2Kq6+ehm3bgMxMQJaB9HQgI8Nqi+OLS25FbyAAfP45sG0bsH8/UFXFtiNHgOpq4Py+ym0A7gew77yynE6gsBDo0wcoKGDbkCHA8OFAcTHQwahMMrI16cV0+DCwciXw8cfAp58C//kPoKrMwwwYcFYIhYVA377M22RkMO+TlsYEs3TpP3DrrdOhqme9VlMTE19l5VlBVlYCX3zBjns8wDXXsG3sWGD8eMDttvqvYSrJJyafD1izBigvB1atAvbsAbKygFGjgGHD2HbNNUC/fubcPxRinm/7duCTT9jr1q2AKAKjRwMTJwK33QYMHZp0nis5xKTrwOrVwPz5wMKFgN8PjBwJ3H47cOutrNqxsu3c1MTsKy9n2+HDQP/+wIwZwPTp7H0SsDWhQ+ocPkz0+ONEffsSCQLR9dcTvfAC0alTVlvWMTt3Mrv79GF2jx5N9PLLRH6/1ZZ1i8SMz/Tpp0TTpxPJMlFhIdH//A/R/v1WW9V1NI1o1SqimTOJnE6inByiX/+a6PRpqy27KBJLTB9/THTrrezXfPXVRG+8QaQoVltlDDU1RD/9KVFWFlFaGtEPfkB08qTVVnWJxBDTkSNEU6cyEY0ZQ1ReTqTrVltlDo2NRH/6E1GvXkQZGUTPPEPUErYy3olvMTU1ET3xBJHLRVRSQrRokdUWxY4E/OzxK6b164n690+4X6fhtPbKkyfHddUXf2IKBonmzCGSJKIvf5m1JThEH3xAVFDAqr9ly6y2JirxJaYDB4iuuYbI7SZ66SWrrYk/6uuJZsxgXurb3yYKhay2qA1b4mbWwLp1rJdaltmwxze/abVF8YfHA/z970BZGeugvfVW4MwZq606S1yIad48YMIEtq1dmzQ9wqZxzz3Ahg1sLHDUKDZkFA9YLqZf/IJ5oSefBBYsAFwuqy1KDC6/HNi4EejVi435bdlitUWwNhHPL37BGtr//KeVViQ2wSDRlClEHg/R1q2WmmJdA/zpp5mQ3nrLKguSB1Uluvde1nu+fbtlZliT7uKFF4Dvfhd4/XXAqDn6uq4jGAwiEAigsbERDY2NaGxogK7ryMnJQWFhIVxJXIcqCjB1KvDRR2zulgXtzthPQVm/nk0U++tfgW98o/vl7dz5OSRZgqbpCIVCCIVCCAaDCAaD8Pn8CAQCUFUNqakuDLlsMPr373/e4spkIRQC7rqLTdr7+GMgJSWmt4+tmI4eBUaMAKZMAf78Z2PKfK98JTweDxwOBzRNh9/vRyDgRyDAvFQgwIQVCoWgqSqysjJx3XUjMWDAAGMMiDPq64Frr2WTAP/5z5hOwIudmFQVGDMGsNuB998HjHIO7y5ZBgDQNQ0OhwOyzQZN05iQgkEE/P4WUQWgaRqICKqiom/f3rj22hEoKCiAw+GAKFr+YGsYn38OXHcd8KtfAd//fsxuuzVmq1OefZb1i3z6qXFCAgBVVUFEUEIKmpqaAYEtoBRFEbqmQtd1aJoGTdOhqhp0XQPphCNHqnDkSCWKigpx5ZVXoqioEG63OylEdcUVwKuvAjNnAl/5SuzaTzHxTIcPsw84fz5w993Glv3Pt8qg6zqUkAJVVRFSQlBVFQIASZYAAkIhBcFgEKqmgnSCTjpI15nQdA0pLhdGj74eo0aNgtPpNNZAC5kyhfWQV1TEpLqLjWf69rfZRHqjhQSwZduaqkFRFKiqAkVVoSgKlJACTdMgSSKEsLchQNM1aJoKTdWghQWlaairq4emacYbaCHPPQdcdhnwxhtALOKTmS6md95hXf8tWdcNR1FCUEJqGzGpqsraRzpB0zSIooBAKAiv1wtdYwIiIhABmqrA6XKCZTlNLnJzgd/+Fvjxj4FJk9jCUTMxtYFABPz858B3vgPk55tzD0VRIl0BwWAIoSCr5nRdh05sU1UVDXX1OH3qNBobGhDw+0G6DrtNhtvtRmqqG4IgJmWywa9/na0D/NvfzL+XqZ5p+XJg3z62fs0sQkHWr6QoSiuP07LprNoiAE6nEw67Hc4UF+x2B2RJAiBAI4KuqcnomACwJV5z5gA/+Qnw6KPmjn2a6pl+/Wtg9mzAzMC0isIa3ZqmMW+k69B1DbquQicdmq5DEEXk5OQit1c+3O40SJIEVdOgBEPwNp7A0dBx+BEyz8guUl9fj6eeeqrDqL9dYcYMJqIXXzSkuHYxTUwbNwKbNrFfg5kw4YSrNI1trdpE4QZ2SAkhEAiwDsxgCEpIgaIqkAQneuoZkLX4WV5bVFSEUMg4cdtsrKnx3HOs6WEWponprbdYx1lhoVl3YBARE09ko5Z9dNZTaRo0VQMRi0ohiiJcLhfc6WlIz8pCTnYuSGdtKzMRBAGzZ8+Gx+PB22+/DQDYtm0brrzySoiiiD59+gBgnunpp5829N733su6aDZvNrTYNpgiJl1ny7Tvu8+M0ttCLX1GRHqLgFo2TW/z5OZKcSEjIwMpThdSUlKQ7kmDy+WCTZYhiiICgWDkXDOZOHEi5s+fj8ceewwA8OCDD2L27NkIhUI4evSoafctKmJL5t96y7RbmCOmf/8bOH6c/RrMJuyZ9IhH0tpUfeG2lCiKEAQBqqZCUUJs7M7vh9/vh8/ni/Skh1/NYvLkybjllltQWVkJANizZw++/vWvtxvE3kimTGE/crM+niliWrWKxSlqJ0GSoeg6oLf0J7VphJMeea+pGgL+APx+X2Sczt8ipPCmKKFWwoweUN4Ili5dioqKCpSUlAAAhgwZgnnz5plexQLALbew0D9mTfM1RUzbtrHII7GAVXOsg7L1Fm5467oOVVMRDAbg9/kjfVJhrxQWlqrqkXZWuGvBDBYvXowZM2bgd7/7HQBg3rx5eP7552G321FQUGDKPcNcdhmblmLWFF9TxLR1awzFpJ8VQbiTUtM1aKoKvUVMmqpFRBMMeyafD4FAi6BaPJPWamDYLDG98sorqK+vx90tY0sjRozArl27oOt6pOqLfDaDbZAkFhdq+3ZDi41geEV97BhQW8vmLcWC1iIKe5XI0x0QGdANBIIQRAGhQABEBEEUWdWoqqwtpZ4VkiiKkXZWsjFiBKs5zMBwMR06xF6LiowuOTpEFBmHi8wGaNMLrkPVNKiaCkCAEgpB1wmCKES6DZiw1Ei7S5KkSNvJSEHFw3BNURHQ0ithOIaLqa6OvcYqEq1O4TaS1qa9o7cIiQ3mqqwnvJUHkmQ58uUKOCvKs73o5ncTWEFm5tnvyGgM9+N1dSyoaKzm7lO4jdRKBLqug7Rw35MWqfbCogEASRQhiSJEUWTxvVv1lrcWVbIJKjOTxf0MBo0v23Ax1dfHNj52KBRiDe1zvIpOrRrmugZBECBJEgQg8j4sqHCw+LCQkl1MgDneKeFbmF/+8peRmZnZ0j6iNoIKi0LTWBUnSiIgnBWTKEsQJOadwh2cZ6/RklJMZmJ4mykjg3mnWHHDDaNx+eVD8OGHH2L7tu04c6aOzb5sebwPb4IgQBZZdSaIgCxL0HUBAth7AG1ECLBkhcmW4iLskcIeykgM90yZmSwLgN9vdMntk5GRgbvuuguzHpqFUdeNRM+ePSEIQpvhFUFgc8IFUWDCkmVIogRRECEKInRNjzzJpaSkIDU1NSnX19XVsY5LMzKdGe6Zwoqvr49tEApJklBcXIxevXrhi71fYPOWLdizew9qa2uhaRqEln+aooJACAVDEEQBNrsMlysFeb3ykJWVhR49esDhcBg2lyjeqKszxysBJoipuJi9HjrEInTEGpfLhauHXo3i/sXYu2cvNm7ahM937oQkisjMzEBubi4yszLQo2dPpKenw+l0wu12Iz8/Hzk5OUlXrZ3LoUPmZWcwXEz5+UBeHhtSGT3a6NIvnLS0NAwfMRzF/YtRXT0OpLNpKOEqzOl0wmazJa0Hao+tW1lMJzMwZd7D8OHmjf90BUEQkJWVhaysLKtNiQs0DdixA/jWt8wp35SugREj2C+AE1/s2sU6LM0aNzVFTLfcwtbJmThxkHMRrFrF0qENHmxO+aaIafRoNjGurMyM0jkXy1tvsdmvZjUTTRGTIDCjzZxvzOkahw6xSXFTpph3D9OGU6ZMYSshwlNSONby1lusS+Daa827h2liGjmSVXdGBfXiXDyhEFsebnasJlMHen/6U+Cll4ATJ8y8S+fMmzcP2dnZKCkpwdZuPmaOGTPmvH3x3lf1xhtMUEaEfewQgyOutkHXiYYNY7lQrCQ9PZ1WrVpFdXV19MMf/tDw8k3+M3YLRWFZoX77W9NvZX7o5nffJUpPJzp61Ow7tU9hYSHNnTuXNE2L7KuoqKD8/HzKzc2ltWvXEhETxeOPP06nT5+mgoIC8vl8RETk8/moqKiIzpw5ExFOWVkZpaen08yZMyP73nvvPcrNzaXc3FyqqKhoU6ZVvPgiUXY2y2NnMrGJA3777USTJsXiTtHZunUrDR8+nAYOHEgffvghERENGjSIVq9eTWvXrqVhw4YREfviDx06RERE3/rWt+iFF14gIqLnn3+eHn300cg5REQlJSW0fPlyWrZsWWTfwIEDacWKFbRkyRIaPHjweWXGmuPHWYq1v/89JreLjZgOH2aZmqxOwLdo0SLKzc0lIiJJkggskA7JskxEbaurvXv30mWXXUaqqtKQIUPo8OHDbc6RZZmCLUnwwvskSaJgMEiBQCBqmbFm8mSiW26J2e1ik9WpsPBs0K/a2ljcsS2PP/44GhoaWsI7s4lvAwYMwJo1a1hwVUU575qBAweif//+eOSRR3D11Vej8JwIHEVFRSgvL8fixYsj+4qLi7Fy5UqUl5dHVuxaxZtvAitWsAD+MSNWslVVlir+xhtjn9Vyw4YNVFRURD179qQ333yTiIhWr15NhYWFBIDGjh1LROd7kdWrVxMA2rZtW2Rf+JyFCxdSeno6PfDAA5F9y5cvp5ycnPPaTLFmxw6ilBSiP/85preNbbqLY8dYp9lXv8oyFHCM58wZ9jceNYplyYohW2O6oKB3bxaFY+5c4PnnY3nnS4NQiIUxSk9nf+NYE7Og8mFGjwb+8hc2pyYlhQU+53SfsJA++4wNY8U4bwoAC8QEsGSFDQ0sEqwsG5fZ6VJFUYBp01iI7A8+iN3S/HOxREwAi02tKGc9ExfUxREMAtOnAx9+yHLSXHWVdbZYJiaAhRPWdRYN9osvgKeeimkWooTnxAmWr3fXLpbS4pprrLXHUjEBwBNPAH36AA8/zGZnvv46z9N7IXz+OcstZ7OxyMbxkPEsLpaHz5zJXPTatSyN2P79VlsU35SVsQeZ4uL4ERIQJ2ICgBtvZH8YIhbd7IUXzI1ZnYjU1bG25ZQpwKxZwHvvmbeg8qKIaR/pBRAKET3xBJEsE912Gxus5BBVVBD17s22996z2pqoxGZsrivYbCyD47p1wIEDLGrvH//I+lEuRSor2WP/rbeyJsBnnwG33Wa1Ve1gtZw7wutlXsrlYhO8rJ51EEuamtp+9nfesdqiTonNFJTucuQI0dSpRILABoqXLWOzOJORhgaiP/yBqFcvNhfpmWdiPzB+kSSGmMJ8/DHRnXcyUV1+OdFrryXMH7pTjh0jevxxIo+HzUz98Y+JTp602qoukVhiCrNzJ9HMmUR2O1GfPkQ/+xnR7t1WW9V1VJWovJyotJTI4WDe6Le/Jaqrs9qyiyIxxRSmspIJqaCACGDzpf7v/4hOnbLaso7ZsYN5nvx85mVvuol52UDAasu6RWznM5mFrgPr17P064sXA83NLDjDxInA7bezNXxWhl1qaGCdsuXl7PXwYdbROGMG26wamDWYrUkhptb4/axbobycbbt3Ax4PE9Tw4cCwYWwrLjZnHDAYBHbuZFkAtm9nrzt2MDHfcAN7rJ84Ebj6auPvbTHJJ6ZzOXoUWLmS9a5v386+aEVhgVz79wf69mWRQQoL2RhhVhYTn83GJpk5HGxuUEMDu66xkcXsbG4GamqAI0dYX9DRo+z9gQPsvMxMJtrhw4GxY4Fx46yZYxRDkl9M5xIKsUHSbdvYGGBVFRNDZSXLkdey3uCCcLlYFdWnDxNkQQHrZB0+3LxQf3HMpSemjlBVoKmJBXdVFPZ+yZIF+MUvSrFpE8FmY17L4QDcbvaeE2Gr5VNQ4glZZtVT68HTvXvZ68iR1tiUSMTd2BwnceFi4hgGFxPHMLiYOIbBxcQxDC4mjmFwMXEMg4uJYxhcTBzD4GLiGAYXE8cwuJg4hsHFxDEMLiaOYXAxcQyDi4ljGFxMHMPgYuIYBhcTxzC4mDiGwcXEMQwuJo5hcDFxDIOvm/t7MeYAABZgSURBVLtAzs3DG29rVwVBsNwmLqYuEIsvKx5EcbHwaq4blJWVoVevXhAEAePGjTvvuCAImD17NjweD95++20AwLhx4yAIAoYMGYLdu3dHzpszZ07E+4VfV61ahd69eyMvLw8fffTReeUvXLgQHo8HDz74YGRfZ9eYihVRoRKJ+fPnR9Kvtt6IiHJzc2nOnDnk9XqjXguAysrKaOnSpdSvX782x/bs2UNFRUWR88J5fFt/JdHyCLcmWp7gzq4xkcSOHBcLWovpXPbt20elpaVUVFRE77///nnHw9cEAgGSJImIiF5++WUaNGgQOZ3OyPHWZbd+Hy2PcGvayxPc0TUmwsXUGR2JKczKlSspJyfnvP0AaMmSJbRkyRIaNGgQERGlpqbShg0bKBgMRhWT3W6nQEs8wsGDB9OaNWvavW9JSQm9++67tGjRokgZnV1jIlxMndFRNTd27FgCQNnZ2VRWVnbetQBo1qxZ5PF46J2WQN7Tpk0jURQj5VRXV7cR02uvvUbjx48nouh5hFsTLU9wZ9eYSHLEtDSTBQsWoLS09KKesBL5yewiiG2OXk5yw8VkIpeQVwLAxcQxEC4mjmFwMXEMg4uJYxhcTBzD4GLiGAYXE8cwuJg4hsHFxDEMPtOyFWfOnMEHH3zQZt+mTZsAsIlwrUlJScGdd94ZM9sSAT7Q24pAIACXy3VB506bNg3z58832aKEgg/0tsbpdOKBBx6AzWbr9Nxp06bFwKLEgovpHKZOnQpFUTo8Jz09HRMnToyRRYkDF9M5TJgwAZmtc4Sdg81mw5QpUy7Ie11qcDGdgyzLKC0thd1uj3pcURSUlpbG2KrEgIspCvfffz9CoVDUY3l5eRgzZkyMLUoMuJiiMHr0aOTn55+332azYfr06RBF/meLBv+rREEQhKhPdYqi4P7777fIqviH9zO1w86dO3HVVVe12devXz8cPHjQIoviHt7P1B5XXnklSkpKIv+32WxtlmFzzoeLqQMefPDBSFWnKAqmTp1qsUXxDRdTB0ydOhWqqgIArrrqKgwYMMBii+IbLqYOKC4uxhVXXAEAmDlzpsXWxD+X7KyB5maguho4cQI4eZJtgQDg8wFEQH09O08UbwGwE3v23I85cwCbDXC72bGMDMDhAHJygNxcIC8PyM4G2unvTHqSVkyBAPDFF2zbt+/s67FjQG0t4Pe3PT8jg4kgLJTwiIrL9RiysnZi5858KAoQCgFe71nBBYNMgK3JymLi6tsXGDgQGDwYGDCAvS8oAJK1myopugaamoAtW4BNm9j26adAVRWg64AsA0VFZ7/M3r2ZJ8nOBnr1Yu9zcpjHaY9du3ZhyJAh7R73ett6ufD7ykom4r172f8B5skGDwaGDweuuw4YORK4/HJmZ4KzNSHFVFMDlJcD69Yx8ezezYTTvz8wahQwdCgTzqBBQHFxfFQ79fVnveOuXcC2bcDmzUBdHZCaysQ1ahQwYQJw441ASorVFneZxBBTKASsX88EVF4OfP45kJ4OXH89+2WPGsVee/a02tKuQcQEtnnzWa/6ySfMS950E3DbbcDttzNPlgDEr5j8fmD5cuDNN4GKClaVDB3K/sC33QaMHp0UVcN5nDkDrFp19odTU8Oq6bvvBkpLgWuvtdrCdokvMWkasHo1sGABsHgxa9hOnAjcfz9z/3l5VlsYW4iAnTvZj2rBAuaRBw4Epk1jW5x1e22Ni8hxx44RPfEEUa9eRIJAdMMNRH/9K9HJk1ZbFl98+inRY48R9e1LBBBddx3R3/9O1BLW0mqsDUO4eTPRtGlENhtRXh7RU08RtQSd5XSAphGtWdP2b/eLXxDV1lpqljVieucdouuvZ7+u4cPj6teVcIS9es+eRA4H0axZRPv2WWJKbMW0Zg0TkSAQfe1rROvWxfLuyY3fTzR3LtFllxHJMtEjjxAdPx5TE2Ijpk8+Ibr9duaJJkwg2rIlFne9NFEUonnziAoKiFwuoscfJ6qri8mtzRVTfT3Rww8TiSLRtdcSRYm7zjEJv5/oj39k1V9WFtGrrxLpuqm3NE9MixcT5ecT5eQQ/eMfpn8QTjs0NBB9//vsBz1hAtGBA6bdaovhQ441NcA99wBf+xpwyy1sqKO0FDgnwxYnRqSnA3/6E/Dxx2yA+8orgWeeYX16hmOkNFevZp6ouJioosLIkjlGEAwS/fKX7Klv/HiiEycMLd4Yz0QE/OEPrLd6/Hhgxw7mlTjxhd0OPPkkGwM8fJgNLrcEeTGG7sqxsZE95ttsRM8+a4TAObGgro7oy19mXupvfzOkyC3dGio9fZp5o5oaNqZ2441GSZxjNhkZwJIlwK9/DfzXf7G5V7/5TffKvGgx1dSwqkxV2RSK3r27Zwgn9ggCq/Yuu4wNHPt8wLPPXvzD0kWJqaqKCcnhANauZTMVOYnLPfewWRqTJ7Ppzs8/f3FTi7ssppMngZtvZm5y5Uo239kM/vOfXfCfO1HbBPr06YO8vFzT7xPv3HEHsHQpmzclScDf/tb1Mrqkv1CIzS1yu4H33zdPSACwc+fnCAYD0HXdlPI1TUN1dTVqampMKT8RGT8eWLYMmDePeaeu0iXP9IMfsMla27cDHk/Xb9YViHQUFBTgx//v8Ug2bSPp168fbhk/wfByE51x44AXXgAefpjNob/55gu/9oLF9PLLwNy57KmtT5+LsLKL6DpBkiRUVVVjzPCvweFIgd2RArs9BbLshCw6AdhBmggoAgQFgEIQNB2irkMgDYAKiCpUQUFAD6Dq+AkcqjyOpuBp2G3Vl1w+uAtl1iw2q3PKFPZw1a/fhV13QWL64gvg0UeZ67vhhu6YeeHoug4igk22wZ2aBpfLDacrFS6XG3ZnCiTZCYFsQEiGHhKBIIFCOiRFg6gBkq4BggZVCMEb8OLMiSC0gA3pnkyozSok8TRIJxCZU40mOr//PVtF88gjrG18IXQqJiKm1LvuAh56qLsmXjgRMdlk2O0SXC4bUlMdSHE74HQ5IctOiIINFJSgegmaSBBEHYIkQtJ0SLoEXVXR3ByCUq/BJdvh8aRC9avw6w5IsgQi4t6pHSQJeOUVYMgQNv/8QoILd9oA//vfgQMHLq513x10XYeu67DJNjhtElKcMtypNqS77fCk2ZCRbkdGug3pbhFpqRLSXRLcTglpTgmpNgkODZCadDiDIjJTnchKdyHD7US604EUuwOyJEGPIzFdTGjD+vp6PPXUU6a0KQG2SPW3v2Vt5TNnOj+/QzE1NwM/+Qnwq1+Z++QWjbBnsttssIsiXHYRqS4RbpeEtBQZ6akS0hwi3HYRboeAVIcAt0OECyLsfoLNp8EtCshMsSErxY4Mhx3pNhtSRRkuQYYkySBdh67Hh5jWrVsHAF0SRlFRUbuxN43im98ESkqAOXM6P7dDMf3lL0BaGqvmYg2R1tJmkiGHdNh1wCkIcMqAUwJcogAHEZwgOEUBDlGApBKkkA4HAWlOCWkpzFOl2SSkCRJSNREuVYJdFSDLMgjd90yCIGDOnDnweDx46aWXMGvWLOTl5WHz5s0AgPLycuTl5SEvLw+rVq2KXPPkk08iNTUVVVVVkX1hIYVf27t2Tss3W19fj6effrpb9neGKLIpLK++ymqoDs9t74DPB/zv/zLPJElGm9g5uk7QdR2ybAM1qkCjCjTrEJsJko8geHWIPkAMAIJPh+7VgBAgiwIcThF2pwibQ4DNJsAmCLBpAmwBQArokELExKQbU80VFxdjw4YNePjhhzF27FhUVFRg0qRJAIBHH30Ur776Kl5++WV873vfa3PNunXrMGPGjMi+sC3h1/aufeSRR7ptc1cYORL40pdYldcR7S7CfPVV4LHHWNQQh8MMEzvm+edfxMSJE/DUE7/CSMcN6NkzC1k9M+HJTEeq2w2H3QUiCYpXRzAA6JoA0gGJCDZdh0QaoBM0RUXAF0RdQxNqz9TheN0ZVDVWw319I2686QZkZ/fAddeNumg7BUGIfPnR3suyDJ/PByKC2+2GoihRzzv3FUCn10azwSyWLmVdBceOnY0Qcw5b232ae+UVNkPSCiEBgK6zak4WJTSc8sOm+iEFnECTHZrbDodNghYCdIjQBBG6IIAEQJQAWSJIAkACQSeCX9HQHFTh8ynwNStQgypkSQbI/K6B4uJirFy5Erqut4mR2R52ux3BYBAOh6PL15rJ7bczEc2fD3z3u9HPiSqm48eBDRvY9ASr0HUduqZDkmScPuOFEHRA99sRapTRbBPgsGmwuRwgSYIui9BsAiAJEG2AZAcEiQDSoWoafCEV9QEF9cEQvMEggiEFkixC1zSY/TD37LPPYtasWRAEAW+88Uan57/00ku488478f7773f5WjORZTZut2hRF8W0YgVTYaw6KKOh6zp00iFKMk7VN0P1y/A3imh0ENwpOlJTAXsqINht0B0SyC5Ad4gQdQEiAEEmkK5DCanwhYJoDAZRHwyhWQkipKmQJCd00rtdPbS+Ptr7O+64A7W1tZ1eE36dOXNmJORhZ9d2tM8MvvIVNhJSVxe9qosqpg0bWCAqKxreYcL9TKIk4lRDA4IiwWcjeFyAPyDAGxDgCGqQXC7AZYPulNnQii5AJAGCrEPXFQT9AQSCfjSHfGhW/fApfgS1ICTJbVgD/FJh9GgWB2vjRlbtnUtUMW3axBpbVhLuZxJFCfXNp6FKgGqXoGoiQqoAn0pwqBqkoAJBcYJUO6BLIE2EoAogSYOmB6H4/Qj5ffAHffCHfPCrAShaEJIkQudi6hIeDxv83bz5AsWk66w/wepwLUSEUCiElDQ7rv5qT0iCBElQIAp1kMRGQJQQEiUIogiIAiCJrKNDEFo6PAgkEsilQ7ZrSM1U4VI1ZGgqiJyQZQmKonAxdZEBA1j0u2icJ6aaGjZvqbjYbLM6Rtd1eH1+TJl2L2yy8bndQqEQ9u3bxwd6u0i/fsDWrdGPnSem06fZazjqrFVIkowl7y7p8Bwigt/vb3cI4kLy7fbtG4P5NElEejrQ2Bj92HliCochTk8306TO+dGPftDpOfX19fjd736HnlGCWXq9Xvz3f/+3GaZd0qSkAA0N0Y+dJ6ZwlNcYTL++IDpq0wiCgOLiYvSLMnvryJEjnV7L6TqK0n4k4HbFdG6gdCvQdR3BYDDqPPCdO3ciIyMDffv2Re/evSNpUDVNg6IordpEhD7nTA0VBAF2ux1yMkZYNZmmJhZqOhrn/TV792YPRFVVwDXXmG1a+wSDQZw8eRKnT5+OJMNpTXl5Ofr06YO0NA+OHj0Oj8cDh8OBUCiEuro6NDQ0oqJiFVwuJ4YOHdrmWlEU4fF4kJubi5SUFO6lusDRoyzzQjTOE5PTCeTndz7dwEyICIFAINK4PteDNDQ0wOfzYf/+/RBFCbIsQ5ZliKIIIoKqKlAUBaqqoU+f3lBVFU6ns035oVAIfr8fTqcTkpW9swnGwYNAe/P4ovr5YcPYChSrEAQBqampKCgoiPy/Nc3NzejVq9cF9RE5HA5kZWXBcc6INesQFXm+3S6gqmx1UpfG5q6/nq2dspKwt4lGVlYWsmI99ZODTz5hbenRo6Mfj/qzvPVWVs3t32+maZxEo7ycpd5or0M7qpiuuYalWFi82ETLOAnHkiVs5kB7RBWTIAAzZ7KVKRwOwNpKW7d2vNyt3dbnQw8Be/aw6SgczgsvsKXjHWWY6jARz4MPAqdOsWAGnEuXmhrWTlq8mLWn26HjrE779gFXXMGSBI4caYqdnATgu99lXUX//neHp3WeIuzHP2ZV3YYNyZtbltM+mzez8JIbN7L+xw7Y2qk8nnySdRM895xh9llGZmYmvF4vANbxmZGR0aXrL2YJdyKjqsDs2SzmZSdCYlxIGNWyMpaHY+fOC468GpcAoIULFxIR0dy5c+kCP37Uci4FfvlLltuuqemCTr+wOOCTJwOTJgFf/SprkCcy69atg6qqWL9+fWSfmUu4E5Vly4Cf/5yFUbrgiZIXqlKvl+jqq4luvjlxc8MBoJ/97Gf00EMP0Y9+9KOIhxk4cCCtWLGClixZQoMHD46cO2/ePNq2bRuNHTs2sq/1a0fXHkrgLIyffUbk8RD97GdduqxriXgqK4mys4m+9a0u3SRuAECLFy8mAPSvf/0rIgpJkigYDFIgECBZliPntr4u2uuFXJtonDhBVFJCdM89XU6e1LWg8n37Au+8w4IYeDzdD0JuBcOHDwcAjGzV15EsS7i7y6lTrB/J7WajH12e5nUx6l20iMhuJ/re9xIr9de5Hzf8/+XLl1NOTg7l5uZSRUsGIXTgmV577TUaP378BV2bKFRXE11+OcukeezYRRWx5aJT0a9YwRrm06ezRhqfX5a4hJMEOJ1ARcVFJwnovJ+pPe64g7X4Fyxgq3+bmi62JI6VbN/OZk6mp7NIyt3JNtGtPu2bb2bB5TdsYMMtu3d3pzROrHnlFRac5LLLgFWruh9qstsDJNddx9Sdnc0E9a9/dbdEjtkEAixW5Te+wQK6LV9uUJIAoxpwoRDLBQuwroOGBqNK5hjJjh1Ew4YRZWQQLVtmaNHG5ei12VggzbIyYOFC4PLLWeg6TnwQCABPPMGyX6akANu2AXfeafBNDNVmCydPEk2fzrzU/fcT1dSYcRfOhbJ2LdGgQURpaUR/+QuRpplyG+OzhwNAz57AG28A773Hpi4MHswitcbLkvNLhf37WVzSceNYLO9du4DvfMfEqUSmaLQVTU1sjMftJurVi+j551n7imMex44RPfwwy5s8eDDR22/H5LZdG5vrDrW1rMfcbmdjP6+/zkVlNNXVRI89xqYLFRQQzZ1LpCgxu33sxBTm8GGimTPZryY/n+hXv2JtLM7Fs20b0QMPsB9qbi7Rn/5E5PfH3IzYiylMVRXRnDlEPXqwX9I3v5n4k+9iiaoSLVxIdNNN7EHn6quJXn2VKBCwzCTrxBTG6yV68UU2wAgQjRhB9Mc/XvRgY9KzcSNrLuTmEoki0aRJRB9+aLVVRNSdgV6jIWLDMgsWsF70ujr2FFJaCnztayzB9KXKnj3Am2+yv83+/cCVV7L8b9OmAS2xPeKBzlenWIGisOyLCxYA777LAraOHg3cdhubbzN0aHKvlPH5gDVrWNdKeTkTUGEhMHUqE9CVV1ptYVTiU0yt8XqBtWvb/mGzs4GJE5nnGjWKZWtM5CkwTU2sR3rjxrMD56p69gc0cSJbHRLnMcniX0znsn8/E1V5ObB+PQvW6XazYYKRI5m4hg5lgTfiUWBeL7B3L7BlC1uTtnkzm22haWzV7M03MwFNmGB+hnaDSTwxtYaIfTHhL2XTJmDHDlZN2u2s13fQIGDgwLNbfj6Qm9t+XEYjOHmSbZWVzL69e1nS7C++YBPRAJZ7ZOTIs9uoUczjJjCJLaZoBALAf/7DvrjwF7lvH/t/6wl8qalMVLm5bEJYdjYbrA6HrM7IYNWK2832e72s7RYMsjaNprF42IEAE87x42dFpChn79OrV1tBDxrE5g8VF8d9tdVVkk9MHVFdzb70mhr2pdfUALW1Z0UQDLK8xERAfT27prGRCcflYtNabTYmMEFggnM4mBjz8pgws7OZgHJy2JNWWpq1nzmGXFpi4pjKxc8B53DOhYuJYxhcTBzD4GLiGMb/B/UgWMdoanJHAAAAAElFTkSuQmCC"/>';
			  //echo $im;
			  echo "<div id=target></div>";			  
			  echo "<div align='center'>";
			  echo "<a onclick='view_relation_graphviz.dialog(\"open\");' href='#view_relation_graphviz' title='".
					   __('Relationship')."'>".__('View relationship', 'relation')."</a>";
			  echo "</div>";


			  Ajax::createModalWindow('view_relation_graphviz', 
									  $CFG_GLPI["root_doc"]."/plugins/relation/front/relation.test.php?item_id=".$ID."&item_type=".$itemtype,
									  array('title'        => __('View Relationships', 'relation')));	
			  
			  //echo '<img src="data:image/gif;base64,'.base64_encode($im).'"/>';
			  
			  //echo $CFG_GLPI["root_doc"].'/plugins/relation/front/relation.test.php?item_id='.$ID.'&item_type='.$itemtype;
			  //echo "<img src='".$CFG_GLPI["root_doc"]."/plugins/relation/front/relation.test.php?item_id=$ID&item_type=$itemtype' alt=''>";
			  //echo file_get_contents($CFG_GLPI["root_doc"]."/plugins/relation/front/relation.test.php?item_id=".$ID."&item_type=".$itemtype);
			  echo "</td></tr>";
			  echo "</table>";
			  Html::scriptStart();
				echo "$(document).ready(function(){
				$('#target').load('".$CFG_GLPI["root_doc"]."/plugins/relation/front/relation.test.php?item_id=".$ID."&item_type=".$itemtype."');
				});";
			
			  echo Html::scriptEnd(); 
											
		}	
				

	}

	public static function getStatusItem($itemtype,$id){
		global $DB;
		$estado = "";
		$array = array('Ticket', 'User', 'Group');
		if (!in_array($itemtype,$array))
		{
			$clase = new $itemtype();
			$clase->getFromDB($id);
			//print_r($clase);
			//echo Dropdown::getDropdownName("glpi_states", $clase->getField('status_id'));
			$estado = Dropdown::getDropdownName("glpi_states", $clase->getField('states_id'));

		}
		return $estado;
	}		

	public static function getViewNameClass($itemtype){
		global $DB,$CFG_GLPI;
		$query = "select id, viewname from glpi_plugin_relation_clases where name='".$itemtype."' ";
		$viewname = $itemtype;
		$result=$DB->query($query);
		
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				$viewname = $data[1];			
			}
		}
		return $viewname;
	}	
	
	public static function dropdown($options=array()){

		global $DB,$CFG_GLPI;
		
		$params['itemtype_name']       = 'itemtype';
		$params['items_id_name']       = 'items_id';
		$params['itemtypes']           = Array();
		$params['default_itemtype']    = 0;
		$params['entity_restrict']     = -1;
		$params['onlyglobal']          = false;
		$params['checkright']          = false;
		$params['showItemSpecificity'] = '';
		$params['emptylabel']          = '';		
		
		//$p['entity'] = '';

		if ( is_array($options) && count($options) ){
         		foreach ($options as $key => $val) {
            			$p[$key] = $val;
			}
		}

		$rand = mt_rand();
		$query = "select id, name from glpi_plugin_relation_typerelations order by 1";
		$result=$DB->query($query);
		$type_relations = "type_relations" . $p['name'];
		//Desplegable tipos de relaciones
		echo "<select name='_nombresrelaciones' id='nombresrelaciones'>\n";
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				echo "<option value='".$data[0]."'>".$data[1]."</option>\n";			
			}
		}
		echo "</select>\n";		
		
		//Desplegable de las clases
		$query = "select rc.id, rc.classlist, c.viewname FROM glpi_plugin_relation_relationclases rc, glpi_plugin_relation_clases c where rc.classlist=c.name and classname='".$p['itemtype']."' order by 1";
		//echo $query;
		$result=$DB->query($query);		
		$arraySelect = array();
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				$arraySelect[] = $data[1];
			}
		$params['itemtypes'] = $arraySelect;
		}

      		
	  $rand = Dropdown::showItemType($params['itemtypes'],
                                 array('checkright' => $params['checkright'],
                                       'name'       => $params['itemtype_name'],
                                       'emptylabel' => $params['emptylabel']));
		
		if ($rand) {
		 $p = array('idtable'             => '__VALUE__',
					'name'                => $params['items_id_name'],
					'entity_restrict'     => $params['entity_restrict'],
                    'width'      => '120',		
					'display_emptychoice' => true,
					'showItemSpecificity' => $params['showItemSpecificity']);

		 if ($params['onlyglobal']) {
			$p['condition'] = "`is_global` = 1";
		 }

		 $field_id = Html::cleanId("dropdown_".$params['itemtype_name'].$rand);
		 $show_id  = Html::cleanId("show_".$params['items_id_name'].$rand);

		 Ajax::updateItemOnSelectEvent($field_id, $show_id,
									   $CFG_GLPI["root_doc"]."/plugins/relation/ajax/dropdownAllItems.php", $p);

		 echo "<br><span id='$show_id'>&nbsp;</span>\n";

		 // We check $options as the caller will set $options['default_itemtype'] only if it needs a
		 // default itemtype and the default value can be '' thus empty won't be valid !
		 if (array_key_exists ('default_itemtype', $options)) {
			echo "<script type='text/javascript' >\n";
			echo Html::jsSetDropdownValue($field_id, $params['default_itemtype']);
			echo "</script>\n";

			$p["idtable"] = $params['default_itemtype'];
			Ajax::updateItem($show_id, $CFG_GLPI["root_doc"]. "/plugins/relation/ajax/dropdownAllItems.php", $p);
		 }
		}		
			
		return $rand;
	}
	
	
	//Obtiene el nombre de la clase seleccionada
	public static function getNombreClaseRelacionada($idClaseRelacionada){

		global $DB;

		$query = "select rc.id, rc.classlist FROM glpi_plugin_relation_relationclases rc where id=".$idClaseRelacionada;
		$result = $DB->query($query);

		if ( $data = $DB->fetch_assoc($result) ){
			return $data['classlist'];
		} 
		else {
			return '';
		}
		
	}	
	
	
	public static function isAncestor($testDevice, $currentDevice, $device_type){
		return false;
	}
	

	public static function addParent($parentID,$ID,$type, $parentType, $relationType){

		global $DB;
		if ( $parentID > 0 && $ID > 0 && isset($type) ){

			$query = "INSERT INTO glpi_plugin_relation_relations
				(parent_id,items_id,itemtype,parent_type,relation_type)
				VALUES ('$parentID','$ID','$type','$parentType','$relationType');";
               
			$result = $DB->query($query);
		}
	}


	public static function getDeviceName($itemtype, $id){
		global $DB;

		$objeto = new $itemtype();
		$datatable = $objeto->getTable();
				
		$name = "";
		$query = "SELECT `name` FROM `$datatable` WHERE `id` =" . $id;
		$result = mysql_query($query);
       
		if ( $result ){
			$row = mysql_fetch_row($result);
			$name = $row[0];
		}
		return $name;
	}

	/**
	* Log event into the history
	* @param device_type the type of the item to inject
	* @param device_id the id of the inserted item
	* @param the action_type the type of action(add or update)
	*/
	public static function logChange($device_type, $device_id, $parent_id, $child, $parent, $action_type, $child_type, $relation_type){
	
		$parentchanges[0] = 0;
		$parentchanges[1] = "";
		$childchanges[0] = 0;
		$childchanges[1] = "";

		if ( $child ) $child = "(" . $child . ")";
		if ( $parent ) $parent = "(" . $parent . ")";

		if ( $action_type == RELATIONS_LINK ){
			$child_name = "";
			$childClase = $child_type;
			$objAsociado = new $childClase();
			$objAsociado->getFromDB($device_id);			
			switch ($child_type){
				case "User" :
						$child_name = getUserName($device_id);
					break;
				default:
						$child_name = $objAsociado->fields['name'];
					break;
			}

			$parentchanges[2] = __('Linked with ','Enlazado con ')
					. ' ' . $child_type
					. ' ' . $child_name //$device_id
					. ' ' . $child
					. ' ' . __('Relation type','Tipo Relacion')
					. ' (' . PluginRelationRelation::getNombreTiporelacion($relation_type, 0)
					. ')';

			$parent_name = "";
			$parentClase = $device_type;
			$objAsociado = new $parentClase();
			$objAsociado->getFromDB($parent_id);			
			switch ($device_type){
				case "User" :
						$parent_name = getUserName($parent_id);
					break;
				default:
						$parent_name = $objAsociado->fields['name'];
					break;
			}					
					
			$childchanges[2] = __('Laced with ','Laced with ')
					. ' ' . $device_type
					. ' ' . $parent_name //parent_id
					. ' ' . $parent
					. ' ' . __('Relation type','Tipo Relacion')
					. ' (' . PluginRelationRelation::getNombreTiporelacion($relation_type, 1)					
					. ')';		
		
		}
		else if ( $action_type == RELATIONS_UNLINK ){
		
			$child_name = "";
			$childClase = $child_type;
			$objAsociado = new $childClase();
			$objAsociado->getFromDB($device_id);			
			switch ($child_type){
				case "User" :
						$child_name = getUserName($device_id);
					break;
				default:
						$child_name = $objAsociado->fields['name'];
					break;
			}
		
			$parentchanges[2] = __('Unlinked from ','Desenlazado de ')
					. ' ' . $child_type
					. ' ' . $child_name //device_id
					. ' ' . $child
					. ' ' . __('Type Relation','Tipo Relacion')
					. ' (' . PluginRelationRelation::getNombreTiporelacion($relation_type, 0)					
					. ')';
					
			$parent_name = "";
			$parentClase = $device_type;
			$objAsociado = new $parentClase();
			$objAsociado->getFromDB($parent_id);			
			switch ($device_type){
				case "User" :
						$parent_name = getUserName($parent_id);
					break;
				default:
						$parent_name = $objAsociado->fields['name'];
					break;
			}						
			$childchanges[2] = __('Unlinked from ','Desenlazado de ')
					. ' ' . $device_type
					. ' ' . $parent_name //parent_id
					. ' ' . $parent
					. ' ' . __('Type Relation','Tipo Relacion')
					. ' (' . PluginRelationRelation::getNombreTiporelacion($relation_type, 1)							
					. ')';					
		
		}
		//echo "<br>parentchanges2=".$parentchanges[2];
		//echo "<br>childchanges2=".$childchanges[2];		
		//echo "<br>";
		Log::history($parent_id, $device_type, $parentchanges, 0,
				Log::HISTORY_LOG_SIMPLE_MESSAGE);
		Log::history($device_id, $child_type, $childchanges, 0,
				Log::HISTORY_LOG_SIMPLE_MESSAGE);
	}
	
	//Obtiene el nombre de un tipo de relación dado su id y el sentido (0=directo, 1 = inverso)
	public static function getNombreTiporelacion($idTiporelacion, $sentidoRelacion){
		global $DB;

		$query = "SELECT name, invname FROM glpi_plugin_relation_typerelations where id=".$idTiporelacion;
		$result = $DB->query($query);

		if ( $data = $DB->fetch_assoc($result) ){
			
			if ( $sentidoRelacion ) {
				return $data['invname'];
			} 
			else{
				return $data['name'];
			}
		}
		else {
			return false;
		}
	}	
	
	/* Clases Relacionadas con Graphvi
	*/
	public static function command_exists($cmd) {
		if (\strtolower(\substr(PHP_OS, 0, 3)) === 'win')
		{
			$fp = \popen("where $cmd", "r");
			$result = \fgets($fp, 255);
			$exists = ! \preg_match('#Could not find files#', $result);
			\pclose($fp);   
		}
		else # non-Windows
		{
			$fp = \popen("which $cmd", "r");
			$result = \fgets($fp, 255);
			$exists = ! empty($result);
			\pclose($fp);
		}

		return $exists;
	}


	public static function mostrarArbol($itemtype,$id){
		global $DB;
		$nombre ="";
		 if ($id==0 || $itemtype=="")
		 {
		 return "";
		 }
		
		$clase = new $itemtype();
		$clase->getFromDB($id);
		$nombre=$clase->getName();
		$pivot = 0;
		$struct = "struct".$pivot." [margin=0 shape=box, style=filled, fillcolor=white, color=red, label=<<TABLE border=\"0\" cellborder=\"0\">
							<TR><TD width=\"35\" height=\"25\" fixedsize=\"true\"><IMG SRC='".PluginRelationRelation::getIconItem($itemtype,$id)."' scale=\"true\"/></TD><td><font face=\"Arial\" point-size=\"7\">".$nombre."</font></td></TR>
							<TR><TD colspan=\"2\" fixedsize=\"true\"><font face=\"Arial\" point-size=\"7\">".PluginRelationRelation::getViewNameClass($itemtype)."</font></TD></TR>
				   </TABLE>>];
				   ";
		//$inicioarray = array('0','0','');
		$_SESSION['glpi_expandeabajo'] = array();
		$_SESSION['glpi_expandearriba'] = array();
		
		$struct .= PluginRelationRelation::expandirabajo($id,$itemtype,$pivot).PluginRelationRelation::expandirarriba($id,$itemtype,$pivot);
		
		return $struct;
	}

	// Añadir Metodos para las representación Grafica de Relaciones
	public static function getIconItem($itemtype,$id){
		global $DB;
		$clase = new $itemtype();
		$clase->getFromDB($id);
		/*$item = $clase->find("id = $id");
		*/
		$icon = "../pics/nothing.png";
		//$icon = $_SESSION["glpiroot"]."/plugins/relation/pics/nothing.png"
		//$query = "SELECT img FROM glpi_plugin_archires_imageitems where itemtype='".$itemtype."' and type= 1";
		$query = "SELECT img FROM glpi_plugin_relation_clases where name='".$itemtype."'";
		$result = $DB->query($query);
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
			$icon ="../pics/".$data[0];
			//$icon = $_SESSION["glpiroot"]."/plugins/relation/pics/".$data[0];
			}
		}			
		return $icon;
	}


	public static function expandirabajo($id,$itemtype,$pivot,$oldpivot=0){
		$cadenaabajo="";
		
		$a = PluginRelationRelation::getDescendentsItem($itemtype,$id);
		
		$resuldown = PluginRelationRelation::checkArrayExpande($_SESSION['glpi_expandeabajo'],$id,$itemtype);
		if ($resuldown == true)
		{
			$registrodown = array('source' => $oldpivot, 'target' => $pivot, 'id' => $id, 'itemtype' => $itemtype);
			array_push($_SESSION['glpi_expandeabajo'],$registrodown);	
		}
		
		
		$npivot = 0;
		$i = 1; /* sólo para efectos ilustrativos */
		$struct = "";
		$direction = "";
		$ramas = "";
		foreach ($a as $v) {
			
			//echo $v["id"]."/".$v["name"]."/".$v["name_relation"]."/".$v["itemtype"];
			$rst = PluginRelationRelation::checkArrayExpande($_SESSION['glpi_expandeabajo'],$v['id'],$v['itemtype']);
			if ($rst==true)
			{
				$struct .= "struct".$i.$pivot." [margin=0 shape=ellipse, style=filled, fillcolor=white, color=blue, label=<<TABLE border=\"0\" cellborder=\"0\">
									<TR><TD width=\"35\" height=\"25\" fixedsize=\"true\"><IMG SRC='".PluginRelationRelation::getIconItem($v["itemtype"],$v["id"])."' scale=\"true\"/></TD><td><font face=\"Arial\" point-size=\"7\">".$v["name"]."</font></td></TR>
									<TR><TD colspan=\"2\" fixedsize=\"true\"><font face=\"Arial\" point-size=\"7\">".PluginRelationRelation::getViewNameClass($v["itemtype"])."</font></TD></TR>
						   </TABLE>>];
							";
		   
				$direction .= "struct".$pivot." -> struct".$i.$pivot." [ label = \"".$v["name_relation"]."\" penwidth = 1 fontname=Arial fontsize = 7 fontcolor = \"black\"];
								";
			} else
			{
				$existe = PluginRelationRelation::checkDownExists($pivot);
				//$rstpivot = "[".$pivot."-".$v['id']."-".$v['itemtype']."] >>".$existe;
				if ($existe==1)
				{
					$recurpivot = PluginRelationRelation::getTargetStructDown($_SESSION['glpi_expandeabajo'],$v['id'],$v['itemtype']);
					$direction .= "struct".$pivot." -> struct".$recurpivot." [ label = \"".$v["name_relation"]."\" penwidth = 1 fontname=Arial fontsize = 7 fontcolor = \"black\"];
						";
				}

			}
			
			$npivot = $i.$pivot;
			
			$ramas .= PluginRelationRelation::expandirabajo($v["id"],$v["itemtype"],$npivot,$pivot);
			
			$i++;
		}
	
		$cadenaabajo = $struct.$direction.$ramas;

		return $cadenaabajo;
	}
	
	public static function expandirarriba($id,$itemtype,$pivot,$oldpivot=0){
		$cadenaarriba="";
		
		$a = PluginRelationRelation::getParentsItem($itemtype,$id);
		
		$resulup = PluginRelationRelation::checkArrayExpande($_SESSION['glpi_expandearriba'],$id,$itemtype);
		if ($resulup == true)
		{		
			$registroup = array('source' => $pivot, 'target' => $oldpivot, 'id' => $id, 'itemtype' => $itemtype);
			array_push($_SESSION['glpi_expandearriba'],$registroup);
		}
		
	
		$npivot = 0;
		$i = 1; /* sólo para efectos ilustrativos */
		$struct = "";
		$direction = "";
		$ramas = "";
		foreach ($a as $v) {
			
			//echo $v["id"]."/".$v["name"]."/".$v["name_relation"]."/".$v["itemtype"];
			$rst = PluginRelationRelation::checkArrayExpande($_SESSION['glpi_expandearriba'],$v['id'],$v['itemtype']);
			if ($rst==true)
			{			
				$struct .= "struct".$pivot.$i." [margin=0 shape=ellipse, style=filled, fillcolor=white, color=blue, label=<<TABLE border=\"0\" cellborder=\"0\">
										<TR><TD width=\"35\" height=\"25\" fixedsize=\"true\"><IMG SRC='".PluginRelationRelation::getIconItem($v["itemtype"],$v["id"])."' scale=\"true\"/></TD><td><font face=\"Arial\" point-size=\"7\">".$v["name"]."</font></td></TR>
										<TR><TD colspan=\"2\" fixedsize=\"true\"><font face=\"Arial\" point-size=\"7\">".PluginRelationRelation::getViewNameClass($v["itemtype"])."</font></TD></TR>
							   </TABLE>>];
								";
						   
				$direction .= "struct".$pivot.$i." -> struct".$pivot." [ label = \"".$v["name_relation"]."\" penwidth = 1 fontname=Arial fontsize = 7 fontcolor = \"black\"];
								";		
			}
			else
			{
				$existe = PluginRelationRelation::checkUpExists($pivot);
				if ($existe==1)
				{
					$recurpivot = PluginRelationRelation::getTargetStructUp($_SESSION['glpi_expandearriba'],$v['id'],$v['itemtype']);
					$direction .= "struct".$recurpivot." -> struct".$pivot." [ label = \"".$v["name_relation"]."\" penwidth = 1 fontname=Arial fontsize = 7 fontcolor = \"black\"];
						";
				}
			}
							
			$npivot = $pivot.$i;
			
			$ramas .= PluginRelationRelation::expandirarriba($v["id"],$v["itemtype"],$npivot,$pivot);
			
			$i++;
		}
	
		$cadenaarriba = $struct.$direction.$ramas;

		return $cadenaarriba;
	}	
	
	/*Obtiene de la relacion los padres de un item*/	
	public static function getParentsItem($itemtype,$id){
		global $DB;

		$query = "select p.parent_id, p.parent_type, p.relation_type, t.name 
		from glpi_plugin_relation_relations p, glpi_plugin_relation_typerelations t 
		where p.items_id=$id and p.itemtype='$itemtype' and p.relation_type = t.id
		and p.parent_type not in (select name from glpi_plugin_relation_clases where is_visible=0)";  //CRI2.0 Modificado olb26s
		$parents = array();
		$result = $DB->query($query);
		$i=0;
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				$parent_id = $data[0];
				$parent_type = $data[1];
				$name_relation = $data[3];
				if ( ! class_exists($parent_type) ) {
					continue;
				}

				$item = new $parent_type();
				$table = $item->getTable();
				if ( $item->canView() ) {
					$query2 = "select  id,  name  from $table where id= $parent_id";
					$result2 = $DB->query($query2);
					while ($data2=$DB->fetch_array($result2)){
						$parent_name = $data2[1];
					}
					
				}	
				$parents[$i] = array('id' => $parent_id, 'name' => $parent_name, 'name_relation'=>$name_relation, 'itemtype' => $parent_type);
			$i++;
			}
		}
		return $parents;
	}		
	/*Obtiene de la relacion los hijos de un item*/	
	public static function getDescendentsItem($itemtype,$id){
		global $DB;

		$query = "select p.items_id, p.itemtype, p.relation_type, t.name
		from glpi_plugin_relation_relations p, glpi_plugin_relation_typerelations t 
		where p.parent_id =$id and p.parent_type='$itemtype' and p.relation_type = t.id
		and p.itemtype not in (select name from glpi_plugin_relation_clases where is_visible=0)"; //CRI2.0 Modificado olb26s
		$parents = array();
		$result = $DB->query($query);
		$i=0;
		if ($DB->numrows($result)){
			while ($data=$DB->fetch_array($result)){
				$items_id = $data[0];
				$itemtype = $data[1];
				$name_relation = $data[3];
				if ( ! class_exists($itemtype) ) {
					continue;
				}

				$item = new $itemtype();
				$table = $item->getTable();
				if ( $item->canView() ) {
					$query2 = "select  id,  name  from $table where id= $items_id";
					$result2 = $DB->query($query2);
					while ($data2=$DB->fetch_array($result2)){
						$item_name = $data2[1];
					}
					
				}	
				$parents[$i] = array('id' => $items_id, 'name' => $item_name, 'name_relation'=>$name_relation, 'itemtype' => $itemtype);
			$i++;
			}
		}
		return $parents;
	}

	public static function checkArrayExpande($array,$id,$itemtype)
	{
		$resultado = true;
		foreach ($array as $key2 => $val2) {
			//$_SESSION['glpi_borrar'] .= "[".$val2['id']."-".$val2['itemtype'] ."] [".$id."-".$itemtype."] / ";
			if ($id == $val2['id'] && $itemtype == $val2['itemtype'])
			{
				$resultado = false;
				//$resultado = $val2['target'];
			}
		}
		//$_SESSION['glpi_borrar'] .= $resultado."\n";
		return $resultado;
	}
	
	public static function checkDownExists($pivot)
	{
		$resultado = 0;
		$array = $_SESSION['glpi_expandeabajo'];
		foreach ($array as $key2 => $val2) {
			if ($val2['source'] == $pivot || $val2['target'] == $pivot)
			{
				$resultado = 1;
			}
		
		}
		return $resultado;
	}
	
	public static function checkUpExists($pivot)
	{
		$resultado = 0;
		$array = $_SESSION['glpi_expandearriba'];
		foreach ($array as $key2 => $val2) {
			if ($val2['source'] == $pivot || $val2['target'] == $pivot)
			{
				$resultado = 1;
			}
		
		}
		return $resultado;
	}		

	public static function getTargetStructDown($array,$id,$itemtype)
	{
		$resultado = 0;
		foreach ($array as $key2 => $val2) {
			//$_SESSION['glpi_borrar'] .= "[".$val2['id']."-".$val2['itemtype'] ."] [".$id."-".$itemtype."] / ";
			if ($id == $val2['id'] && $itemtype == $val2['itemtype'])
			{
				//$resultado = false;
				$resultado = $val2['target'];
			}
		}
		//$_SESSION['glpi_borrar'] .= $resultado."\n";
		return $resultado;
	}

	public static function getTargetStructUp($array,$id,$itemtype)
	{
		$resultado = 0;
		foreach ($array as $key2 => $val2) {
			//$_SESSION['glpi_borrar'] .= "[".$val2['id']."-".$val2['itemtype'] ."] [".$id."-".$itemtype."] / ";
			if ($id == $val2['id'] && $itemtype == $val2['itemtype'])
			{
				//$resultado = false;
				$resultado = $val2['source'];
			}
		}
		//$_SESSION['glpi_borrar'] .= $resultado."\n";
		return $resultado;
	}		
	
}

?>
