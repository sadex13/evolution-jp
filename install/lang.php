<?php

/**
 * Multilanguage functions for MODx Installer
 *
 * @author davaeron
 * @package MODx
 * @version 1.0
 * 
 * Filename:       /install/lang.php
 */

$default_language = $default_config['manager_language'];

if    (isset($_POST['language'])) $install_language = $_POST['language'];
elseif(isset($_GET['language']))  $install_language = $_GET['language'];
else                              $install_language = $default_language;

if    (isset($_POST['managerlanguage'])) $manager_language = $_POST['managerlanguage'];
elseif(isset($_GET['managerlanguage']))  $manager_language = $_GET['managerlanguage'];
else                                     $manager_language = $default_language;

# load language file
$_lang = array ();
if($install_language!==$default_language && file_exists("lang/{$install_language}.inc.php"))
{
	 require_once("lang/{$default_language}.inc.php");
	 require_once("lang/{$install_language}.inc.php");
}
else require_once("lang/{$default_language}.inc.php");
