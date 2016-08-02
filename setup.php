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

define ("PLUGIN_RELATIONS_VERSION", "0.85+1.0");
define('RELATIONS_LINK', 0);
define('RELATIONS_UNLINK', 1);

// Init the hooks of the plugins -Needed
function plugin_init_relation() {
   global $PLUGIN_HOOKS,$LANG,$CFG_GLPI,
			$GO_CLASS_RELATIONS; //CRI

   $PLUGIN_HOOKS['csrf_compliant']['relation'] = true;
   //CRI 
   $GO_CLASS_RELATIONS = array('CommonDBConnexity', 'CommonDBRelation', 'PluginWebapplicationsWebapplication_Item', 'PluginAccountsAccount', 'PluginCertificatesCertificate', 'PluginServiciosServicio_Item', 'PluginMreportingCommon', 'PluginMreportingBaseclass', 'PluginMreportingHelpdesk', 'PluginMreportingHelpdeskplus', 'PluginMreportingInventory', 'PluginMreportingOther', 'PluginTalkTicket', 'PluginAccountsAccount_Item', 'PluginEscaladeConfig', 'CommonDropdown', 'CommonDevice', 'PluginOcsinventoryngDeviceBiosdata', 'Item_Devices', 'PluginOcsinventoryngItem_DeviceBiosdata', 'Profile', 'PluginGenericobjectProfile', 'CommonDBChild', 'ProfileRight', 'Search', 'Dropdown', 'CommonTreeDropdown', 'Entity', 'PluginWebapplicationsProfile', 'PluginServiciosProfile', 'PluginMreportingProfile', 'PluginTalkProfile', 'PluginTalkUserpref', 'PluginCertificatesProfile', 'PluginAccountsProfile', 'PluginReportsProfile', 'PluginOcsinventoryngProfile', 'PluginAdditionalalertsProfile', 'PluginMobileProfile', 'PluginDatabasesDatabase_Item', 'PluginRelationRelation', 'Computer', 'Monitor', 'PluginFormcreatorForm', 'Software', 'User', 'PluginWebapplicationsWebapplication', 'PluginServiciosServicio', 'PluginBehaviorsCommon', 'PluginBehaviorsTicket', 'PluginCustomfieldsField', 'PluginOcsinventoryngOcsServer', 'PluginGenericobjectType', 'PluginGenericobjectObject', 'PluginGenericobjectSingletonObjectField', 'PluginGenericobjectField', 'PluginAddressingPing_Equipment', 'PluginFinancialreportsDisposalItem', 'PluginRacksItemSpecification', 'ComputerModel', 'NetworkEquipmentModel', 'PeripheralModel', 'PluginRacksOtherModel', 'PluginDatabasesDatabase', 'PluginBondsBond', 'Preference', 'NetworkEquipment', 'NetworkPort', 'Peripheral', 'Printer', 'CartridgeItem', 'Cartridge', 'ConsumableItem', 'Consumable', 'Phone', 'PluginWebapplicationsMenu', 'PluginServiciosMenu', 'PluginCertificatesMenu', 'PluginRacksMenu', 'PluginRacksConfig', 'PluginRacksRackModel', 'PluginDatabasesMenu', 'PluginVehiculeVehicule', 'CommonITILObject', 'Ticket', 'TicketTemplate', 'CommonITILValidation', 'TicketValidation', 'Problem', 'Change', 'Planning', 'Stat', 'TicketRecurrent', 'PluginFormcreatorFormlist', 'PluginFormcreatorFormanswer', 'Budget', 'Supplier', 'Contact', 'Contract', 'Document', 'Project', 'ProjectTask', 'Reminder', 'RSSFeed', 'KnowbaseItem', 'ReservationItem', 'Log', 'Reservation', 'Report', 'MigrationCleaner', 'PluginMreportingDashboard', 'PluginMreportingConfig', 'PluginOcsinventoryngMenu', 'PluginOcsinventoryngConfig', 'PluginAddressingMenu', 'PluginAddressingAddressing', 'PluginDatainjectionMenu', 'PluginDatainjectionModel', 'PluginCustomfieldsConfig', 'AuthLDAP', 'Group', 'Rule', 'RuleCollection', 'RuleImportEntityCollection', 'RuleImportEntity', 'RuleImportComputerCollection', 'RuleImportComputer', 'RuleMailCollectorCollection', 'MailCollector', 'RuleRightCollection', 'RuleRight', 'RuleSoftwareCategoryCollection', 'RuleSoftwareCategory', 'RuleTicketCollection', 'RuleTicket', 'Transfer', 'RuleDictionnaryDropdown', 'RuleDictionnarySoftware', 'RuleDictionnaryPrinter', 'QueuedMail', 'Backup', 'Event', 'PluginAccountsMenu', 'PluginAccountsHash', 'PluginRenamerMenu', 'PluginWebapplicationsWebapplicationType', 'PluginWebapplicationsWebapplicationServerType', 'PluginWebapplicationsWebapplicationTechnic', 'PluginServiciosServicioType', 'PluginServiciosServicioServerType', 'PluginServiciosServicioTechnic', 'PluginServiciosOrientado', 'PluginServiciosEnsnivel', 'PluginServiciosCriticidad', 'PluginTagTag', 'PluginCertificatesCertificateType', 'PluginCertificatesCertificateState', 'PluginAccountsAccountType', 'PluginAccountsAccountState', 'PluginOcsinventoryngNetworkPortType', 'NetworkPortInstantiation', 'PluginOcsinventoryngNetworkPort', 'PluginDatabasesDatabaseType', 'PluginDatabasesDatabaseCategory', 'PluginDatabasesServerType', 'PluginDatabasesScriptType', 'Location', 'State', 'Manufacturer', 'Blacklist', 'BlacklistedMailContent', 'ITILCategory', 'TaskCategory', 'SolutionType', 'RequestType', 'SolutionTemplate', 'ProjectState', 'ProjectType', 'ProjectTaskType', 'ComputerType', 'NetworkEquipmentType', 'PrinterType', 'MonitorType', 'PeripheralType', 'PhoneType', 'SoftwareLicenseType', 'CartridgeItemType', 'ConsumableItemType', 'ContractType', 'ContactType', 'DeviceMemoryType', 'SupplierType', 'InterfaceType', 'DeviceCaseType', 'PhonePowerSupply', 'Filesystem', 'PrinterModel', 'MonitorModel', 'PhoneModel', 'VirtualMachineType', 'VirtualMachineSystem', 'VirtualMachineState', 'DocumentCategory', 'DocumentType', 'KnowbaseItemCategory', 'Calendar', 'Holiday', 'OperatingSystem', 'OperatingSystemVersion', 'OperatingSystemServicePack', 'AutoUpdateSystem', 'NetworkInterface', 'NetworkEquipmentFirmware', 'Netpoint', 'Domain', 'Network', 'Vlan', 'CommonImplicitTreeDropdown', 'IPNetwork', 'FQDN', 'WifiNetwork', 'FQDNLabel', 'NetworkName', 'SoftwareCategory', 'UserTitle', 'UserCategory', 'RuleRightParameter', 'Fieldblacklist', 'SsoVariable', 'PluginFormcreatorHeader', 'PluginFormcreatorCategory', 'PluginRacksRoomLocation', 'PluginRacksConnection', 'PluginRacksRackType', 'PluginRacksRackState', 'DeviceMotherboard', 'DeviceProcessor', 'DeviceMemory', 'DeviceHardDrive', 'DeviceNetworkCard', 'DeviceDrive', 'DeviceControl', 'DeviceGraphicCard', 'DeviceSoundCard', 'DevicePci', 'DeviceCase', 'DevicePowerSupply', 'Notification', 'SLA', 'Control', 'Crontask', 'Auth', 'AuthMail', 'Link', 'PluginCustomConfig', 'PluginCustomTab', 'PluginCustomDefaulttab', 'PluginAdditionalalertsMenu', 'PluginAdditionalalertsAdditionalalert', 'PluginWebservicesClient', 'PluginRelationConfig', 'Ajax', 'DisplayPreference', 'PluginMreportingPreference', 'InfoCom');   
   // Params : plugin name - string type - ID - Array of attributes
   Plugin::registerClass('PluginRelationProfile',
              array('addtabon' => array('Profile')));
   Plugin::registerClass('PluginRelationRelation');
   Plugin::registerClass('PluginRelationTyperelation');	
   Plugin::registerClass('PluginRelationRelationclase');   

   // Display a menu entry ?
	// plugin enabled
	if ( class_exists('PluginRelationRelation') ){
	
      $PLUGIN_HOOKS['pre_item_purge']['relation']
                                       = array('Profile' => array('PluginRelationProfile',
                                                                  'cleanProfiles'));	
		$PLUGIN_HOOKS['item_purge']['relation'] = array();
		foreach ( PluginRelationRelation::getTypes(true) as $type ){
			$PLUGIN_HOOKS['item_purge']['relation'][$type] =
						'plugin_item_purge_anything';
		}
	}	   
      //$PLUGIN_HOOKS['pre_item_purge']['relation'] = array('Profile' => array('PluginRelationProfile','cleanProfiles'));

      // Massive Action definition
      $PLUGIN_HOOKS['use_massive_action']['relation'] = 1;

   // Config page
   if (Session::haveRight('config', UPDATE)) {
      $PLUGIN_HOOKS['config_page']['relation'] = 'front/config.php';
   }

   $PLUGIN_HOOKS['submenu_entry']['relation']['options']['typerelation'] = array(
      'title' => __('Relations types', 'relation'),
      'page'  =>'/plugins/relation/front/typerelation.php',
      'links' => array(
         'search' => '/plugins/relation/front/typerelation.php',
         'add'    =>'/plugins/relation/front/typerelation.form.php'
   ));
   $PLUGIN_HOOKS['submenu_entry']['relation']['options']['clase'] = array(
      'title' => __('Relations class', 'relation'),
      'page'  =>'/plugins/relation/front/clase.php',
      'links' => array(
         'search' => '/plugins/relation/front/clase.php',
         'add'    =>'/plugins/relation/front/clase.form.php'
   ));
   $PLUGIN_HOOKS['submenu_entry']['relation']['options']['relationclase'] = array(
      'title' => __('Relations class', 'relation'),
      'page'  =>'/plugins/relation/front/relationclase.form.php',
      'links' => array(
         'search' => '/plugins/relation/front/relationclase.php',
         'add'    =>'/plugins/relation/front/relationclase.form.php'
   ));
   
   
   //redirect appel http://localhost/glpi/index.php?redirect=plugin_example_2 (ID 2 du form)
   $PLUGIN_HOOKS['redirect_page']['relation'] = 'relation.form.php';
   
   $PLUGIN_HOOKS['menu_toadd']['relation'] = array('config' => 'PluginRelationConfig');
   
   	$PLUGIN_HOOKS['add_css']['relation']=array('dd.css');
	$PLUGIN_HOOKS['add_javascript']['relation']=array('jquery.dd.min.js');  
	
    $PLUGIN_HOOKS['post_init']['relation'] = 'plugin_relation_postinit';	//Iniciar los items de para registrar los tabs.

}


// Get the name and the version of the plugin - Needed
function plugin_version_relation() {

   return array('name'           => 'Relation',
                'shortname'      => 'relation',
                'version'        => PLUGIN_RELATIONS_VERSION,
                'license'        => 'AGPLv3+',
                'author'         => 'Oscar Loayza B. - <a href="http://www.carm.es">CARM</a>',
                'homepage'       => 'https://forge.indepnet.net/projects/relation',
                'minGlpiVersion' => '0.85');// For compatibility / no install in version < minGlpiVersion
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_relation_check_prerequisites() {
   

   if (version_compare(GLPI_VERSION,'0.85','lt') || version_compare(GLPI_VERSION,'0.86','ge')) {
      echo __('GLPI version not compatible need 0.85.x', 'relation');
      return false;
   }
   return true;
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_relation_check_config($verbose=false) {
   global $LANG;

   if (true) { // Your configuration check
      return true;
   }
   return false;
}


?>
