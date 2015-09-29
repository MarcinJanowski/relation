<?php
/*
 * @version $Id: HEADER 1 2010-03-03 21:49 Tsmr $
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
// ----------------------------------------------------------------------
// Original Author of file: CAILLAUD Xavier
// Purpose of file: plugin Example  v1.6.0 - GLPI 0.78
// ----------------------------------------------------------------------
 */

include ("../../../inc/includes.php");
                       

if (!isset($_GET["id"])) $_GET["id"] = "";
if (!isset($_GET["withtemplate"])) $_GET["withtemplate"] = "";


//echo realpath (dirname(__FILE__));


$tipoRelaciones = new PluginRelationTyperelation();


//print_r($_POST);
if (isset($_POST["add"])) {
	$newID=$tipoRelaciones->add($_POST);
    Html::redirect($_SERVER['HTTP_REFERER']);
	
} else if (isset($_POST["delete"])) {

	$tipoRelaciones->delete($_POST);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginRelationTyperelation'));
	
} else if (isset($_POST["restore"])) {

	$tipoRelaciones->check($_POST['id'],'w');
	$tipoRelaciones->restore($_POST);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginRelationTyperelation'));
	
} else if (isset($_POST["purge"])) {

	$tipoRelaciones->delete($_POST,1);
	Html::redirect(Toolbox::getItemTypeSearchURL('PluginRelationTyperelation'));
	
} else if (isset($_POST["update"])) {
	$tipoRelaciones->update($_POST);
	Html::redirect($_SERVER['HTTP_REFERER']);
 }                                          
  else {
	  
   Html::header(__('Relation', 'relation'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginRelationConfig",
      "typerelation"
   );
   $tipoRelaciones->display(array('id' => $_GET["id"]));
   Html::footer();

   

}
?>
