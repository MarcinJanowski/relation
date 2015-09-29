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

// Non menu entry case
//header("Location:../../central.php");

include ("../../../inc/includes.php");

Html::header(__('Relation', 'relation'), $_SERVER['PHP_SELF'] ,"config", "PluginRelationConfig");
PluginRelationConfig::showConfigPage();
Html::footer();

?>
