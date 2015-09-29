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
include ('../../../inc/includes.php');

if ( ! isset($_GET['id']) ) $_GET['id'] = '';

$PluginRelation = new PluginRelationRelation();

if ( isset($_POST['additem']) && isset($_POST['type']) && isset($_POST['items_id']) > 0 ){

	if ( Session::haveRight('plugin_relation',CREATE) ){

		if ( PluginRelationRelation::isAncestor($_POST['items_id'],
							$_POST['id'],$_POST['type']) )
			Session::addMessageAfterRedirect(__('Sorry. You cannot add an ancestor as a child!','Sorry. You cannot add an ancestor as a child!'),true);
		else {
			PluginRelationRelation::addParent(
							$_POST['id'],
							$_POST['items_id'],
							$_POST['itemtype'],
							$_POST['type'],
							$_POST['_nombresrelaciones']
							);
			// Logging here
			$child = PluginRelationRelation::getDeviceName($_POST['itemtype'],
									$_POST['items_id']);
			$parent = PluginRelationRelation::getDeviceName($_POST['type'],
									$_POST['id']);
			

			PluginRelationRelation::logChange(
							$_POST['type'],
							$_POST['items_id'],
							$_POST['id'],
							$child,
							$parent,
							RELATIONS_LINK,
							$_POST['itemtype'],
							$_POST['_nombresrelaciones']
							);

		}
	}
	Html::redirect($_SERVER['HTTP_REFERER']);
}
// Deletion link
else if ( isset($_GET['deleterelation']) ){

	//$PluginRelations->check($_GET['id'],'w');

	$DB = new DB;
	$query = "SELECT * FROM glpi_plugin_relation_relations WHERE id='"
		. $_GET['id'] . "'";
	$result = $DB->query($query);

	if ( $data = $DB->fetch_array($result) ){

		$PluginRelation->delete($_GET);

		// Logging
		$child = PluginRelationRelation::getDeviceName($data['itemtype'],
								$data['items_id']);
		$parent = PluginRelationRelation::getDeviceName($data['parent_type'],
								$data['parent_id']);

		PluginRelationRelation::logChange(
						$data['parent_type'],
						$data['items_id'],
						$data['parent_id'],
						$child,
						$parent,
						RELATIONS_UNLINK,
						$data['itemtype'],
						$data['relation_type']);

	}

	Html::back();

} else {
	Html::back();
}

//Html::back();
?>
