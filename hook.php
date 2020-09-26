<?php
/*
 -------------------------------------------------------------------------
 Costs plugin for GLPI
 Copyright (C) 2018 by the TICgal Team.

 https://github.com/ticgal/costs
 -------------------------------------------------------------------------

 LICENSE

 This file is part of the Costs plugin.

 Costs plugin is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 3 of the License, or
 (at your option) any later version.

 Costs plugin is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Costs. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 @package   Costs
 @author    the TICgal team
 @copyright Copyright (c) 2018 TICgal team
 @license   AGPL License 3.0 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://tic.gal
 @since     2018
 ---------------------------------------------------------------------- */

function plugin_costs_install() {

   $migration = new Migration(PLUGIN_COSTS_VERSION);

   // Parse inc directory
   foreach (glob(dirname(__FILE__).'/inc/*') as $filepath) {
      // Load *.class.php files and get the class name
      if (preg_match("/inc.(.+)\.class.php/", $filepath, $matches)) {
         $classname = 'PluginCosts' . ucfirst($matches[1]);
         include_once($filepath);
         // If the install method exists, load it
         if (method_exists($classname, 'install')) {
            $classname::install($migration);
         }
      }
   }
   return true;
}

function plugin_costs_uninstall() {

   $migration = new Migration(PLUGIN_COSTS_VERSION);

   // Parse inc directory
   foreach (glob(dirname(__FILE__).'/inc/*') as $filepath) {
      // Load *.class.php files and get the class name
      if (preg_match("/inc.(.+)\.class.php/", $filepath, $matches)) {
         $classname = 'PluginCosts' . ucfirst($matches[1]);
         include_once($filepath);
         // If the install method exists, load it
         if (method_exists($classname, 'uninstall')) {
            $classname::uninstall($migration);
         }
      }
   }
   return true;
}

// ITMur - Añadida función para poder buscar por los campos del plugin
function plugin_costs_getAddSearchOptionsNew($itemtype) {
   global $LANG, $CFG_GLPI;
   
   $tab = [];

   if ($itemtype == 'Entity'){ //} && Session::haveRight("Entity",READ)) {
	   $tab[] = [
		  'id'                 => '1122',
		  'table'              => PluginCostsEntity::getTable(),
		  'field'              => 'fixed_cost',
		  'name'               => __('Fixed cost'),
		  'datatype'           => 'decimal',
		  'massiveaction'      => false,
		  'joinparams'     => array('jointype'   => 'child')
	   ];			
	   
	   $tab[] = [
		  'id'                 => '1123',
		  'table'              => PluginCostsEntity::getTable(),
		  'field'              => 'time_cost',
		  'name'               => __('Time cost'),
		  'datatype'           => 'decimal',
		  'massiveaction'      => false,
		  'joinparams'     => array('jointype'   => 'child')
	   ];	
	   
	   $tab[] = [
		  'id'                 => '1124',
		  'table'              => PluginCostsEntity::getTable(),
		  'field'              => 'travel_cost',
		  'name'               => __('Travel cost'),
		  'datatype'           => 'decimal',
		  'massiveaction'      => false,
		  'joinparams'     => array('jointype'   => 'child')
	   ];	
	   
	   $tab[] = [
		  'id'                 => '1125',
		  'table'              => PluginCostsEntity::getTable(),
		  'field'              => 'cost_private',
		  'name'               => __('Private task'),
		  'datatype'           => 'bool',
		  'linkfield'		=> 'entities_id',
		  'massiveaction'      => false,
		  'joinparams'     => array('jointype'   => 'child')
	   ];	
	   
   }  
   return $tab;
}
//ITMUR - Añadido para ver si se resuelve el problema de actualización masiva
function plugin_costs_getDatabaseRelations() {                           
			return array("glpi_entities" => array("glpi_plugin_costs_entities" => "entities_id"));	
}
function plugin_costs_MassiveActions($type) {
   $actions = [];
   $action1=array();
   $action2=array();
   $action3=array();
   $action4=array();
   switch ($type) {
      case 'Entity' :
         $myclass      = "PluginCostsEntity";
         $action_key   = 'UpdatePrivateTask';
         $action_label = "Actualiza Tarea Privada";
         $action1[$myclass.MassiveAction::CLASS_ACTION_SEPARATOR.$action_key]
            = $action_label;
			
         $myclass      = "PluginCostsEntity";
         $action_key   = 'UpdateTravelCost';
         $action_label = "Actualizar Coste desplazamiento";
         $action2[$myclass.MassiveAction::CLASS_ACTION_SEPARATOR.$action_key]
            = $action_label;

         $myclass      = "PluginCostsEntity";
         $action_key   = 'UpdateFixedCost';
         $action_label = "Actualizar Coste fijo";
         $action3[$myclass.MassiveAction::CLASS_ACTION_SEPARATOR.$action_key]
            = $action_label;
			
         $myclass      = "PluginCostsEntity";
         $action_key   = 'UpdateTimeCost';
         $action_label = "Actualizar ".__('Time cost');
         $action4[$myclass.MassiveAction::CLASS_ACTION_SEPARATOR.$action_key]
            = $action_label;
			
		 $actions =  array_merge($action1, $action2, $action3, $action4);

         break;
   }
   return $actions;
}