<?php
/*
 * @version $Id: prototype.class.php 165 2011-11-08 11:31:11Z remi $
 -------------------------------------------------------------------------
 Relations plugin for GLPI
 Copyright (C) 2003-2011 by the relations Development Team.

 https://forge.indepnet.net/projects/relations
 -------------------------------------------------------------------------

 LICENSE

 This file is part of relations.

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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginRelationPrototype extends CommonDBTM {


   public static function dotIt($engine, $graph, $format) {
	global $DB,$CFG_GLPI;
      $out         = '';
      $Path        = realpath(GLPI_PLUGIN_DOC_DIR."/relation");
      $graph_name  = tempnam($Path, "txt");
      $out_name    = tempnam($Path, $format);

      if (file_put_contents($graph_name, $graph)) {
         $command = "$engine -T$format -o\"$out_name\" \"$graph_name\" ";
         $out = shell_exec($command);
         $out = file_get_contents($out_name);
		 //$imgbinary = fread(fopen($out_name, "r"), filesize($out_name));
		 //$out = $imgbinary;
		 //$out = base64_encode($imgbinary);
		 //$out = 'data:image/png;base64,'.base64_encode($im);
         unlink($graph_name);
         unlink($out_name);
         //logDebug("command:", $command, "in:", $graph_name, "out:", $out_name, "Res:", strlen($out));
      }
      return $out;
   }


   public static function testGraphviz() {
      $graph = "graph G {
                  a;
                  b;
                  c -- d;
                  a -- c;}";
     return self::dotIt('dot', $graph, 'png');
   }

   public static function relationGraphviz($id,$itemtype) {
      /*
	$graph = "digraph structs {
		size=\"8,5\" ". PluginRelationsRelation::mostrarArbol($itemtype,$id). "}";
		*/
	$graph = "digraph structs {
		". PluginRelationRelation::mostrarArbol($itemtype,$id). "}";		
	
      return self::dotIt('dot', $graph, 'png');
   }      



}
?>