<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();
if(!$modx->hasPermission('save_password')) {
	$e->setError(3);
	$e->dumpError();
}
$id = $_POST['id'];
$pass1 = $_POST['pass1'];
$pass2 = $_POST['pass2'];

if($pass1!=$pass2){
	echo "passwords don't match!";
	exit;
}

if(strlen($pass1)<6){
	echo "Password is too short. Please specify a password of at least 6 characters.";
	exit;
}
elseif(32<strlen($pass1)){
	echo "Password is too long. Please specify a password of less than 32 characters.";
	exit;
}

$sql = "UPDATE $dbase.`".$table_prefix."manager_users` SET password=md5('".$pass1."') where id=".$modx->getLoginUserID().";";
$rs = $modx->db->query($sql);
if(!$rs){
	echo "An error occured while attempting to save the new password.";
	exit;
}
if($_SESSION['mgrForgetPassword']) unset($_SESSION['mgrForgetPassword']);
header("Location: index.php?a=7");
