<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();
if(!$modx->hasPermission('delete_snippet')) {
	$e->setError(3);
	$e->dumpError();
}
$id=intval($_GET['id']);

// invoke OnBeforeChunkFormDelete event
$modx->invokeEvent("OnBeforeChunkFormDelete",
						array(
							"id"	=> $id
						));

//ok, delete the chunk.
$sql = "DELETE FROM $dbase.`".$table_prefix."site_htmlsnippets` WHERE $dbase.`".$table_prefix."site_htmlsnippets`.id=".$id.";";
$rs = $modx->db->query($sql);
if(!$rs) {
	echo "Something went wrong while trying to delete the htmlsnippet...";
	exit;
} else {
	// invoke OnChunkFormDelete event
	$modx->invokeEvent("OnChunkFormDelete",
							array(
								"id"	=> $id
							));

	// empty cache
	$modx->clearCache(); // first empty the cache
	// finished emptying cache - redirect
	header("Location: index.php?a=76");
}
