<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();

if(!$modx->hasPermission('delete_plugin')) {	
	$e->setError(3);
	$e->dumpError();	
}

$id=intval($_GET['id']);

// invoke OnBeforePluginFormDelete event
$modx->invokeEvent("OnBeforePluginFormDelete",
						array(
							"id"	=> $id
						));

// delete the plugin.
$sql = "DELETE FROM $dbase.`".$table_prefix."site_plugins` WHERE $dbase.`".$table_prefix."site_plugins`.id=".$id.";";
$rs = $modx->db->query($sql);
if(!$rs) {
	echo "Something went wrong while trying to delete the plugin...";
	exit;
} else {		
	// delete the plugin events.
	$sql = "DELETE FROM $dbase.`".$table_prefix."site_plugin_events` WHERE $dbase.`".$table_prefix."site_plugin_events`.pluginid=".$id.";";
	$rs = $modx->db->query($sql);
	if(!$rs) {
		echo "Something went wrong while trying to delete the plugin events...";
		exit;
	} else {		
		// invoke OnPluginFormDelete event
		$modx->invokeEvent("OnPluginFormDelete",
								array(
									"id"	=> $id
								));

		// empty cache
		$modx->clearCache(); // first empty the cache		
		// finished emptying cache - redirect
		header("Location: index.php?a=76");
	}
}
