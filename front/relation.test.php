<?php
/*
 * @version $Id: archires.test.php 164 2011-11-08 11:30:52Z remi $
 -------------------------------------------------------------------------
 Relations plugin for GLPI
 Copyright (C) 2003-2011 by the archires Development Team.

 https://forge.indepnet.net/projects/archires
 -------------------------------------------------------------------------

 LICENSE

 This file is part of archires.

 Relations is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Relations is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Relations. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
*/

include ("../../../inc/includes.php");

$PluginRelationPrototype = new PluginRelationPrototype();
if (isset($_GET["item_type"])) {
   $item_type = $_GET["item_type"];
}

if (isset($_GET["item_id"])) {
   $item_id = $_GET["item_id"];
} else {
   $item_id = 0;
}

//echo $item_type."--".$item_id,
$im             = PluginRelationPrototype::relationGraphviz($item_id,$item_type);
//$im             = PluginRelationPrototype::testGraphviz();
//echo $im;
//header("Content-Type: text/plain");
echo '<img src="data:image/png;base64,'.base64_encode($im).'"/>';
//header("Content-type: image/png");
//$imageinfo = getimagesize($im);
//print_r($imageinfo);


//header("Content-Type: image/svg+xml");
//header('Content-Type: image/png');
//header("Content-Length: " . strlen($im));
//echo $im; exit();


?>