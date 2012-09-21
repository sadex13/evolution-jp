<?php
setlocale (LC_TIME, 'ru_RU.UTF-8');
if(function_exists('date_default_timezone_set')) date_default_timezone_set('Europe/Kiev');

// check PHP version. MODX Evolution is compatible with php 4 (4.4.2+)
if(version_compare(phpversion(), '4.4.2') < 0)
{
	echo 'MODX is compatible with PHP 4.4.2 and higher. Please upgrade your PHP installation!';
	exit;
}

// automatically assign base_path and base_url
if(!isset($base_path)) $base_path = assign_base_path();
if(!isset($base_url))  $base_url  = assign_base_url($base_path);
if(!isset($site_url))  $site_url  = assign_site_url($base_url);

if (!defined('MODX_BASE_PATH'))    define('MODX_BASE_PATH', $base_path);
if (!defined('MODX_BASE_URL'))     define('MODX_BASE_URL', $base_url);
if (!defined('MODX_SITE_URL'))     define('MODX_SITE_URL', $site_url);
if (!defined('MODX_MANAGER_PATH')) define('MODX_MANAGER_PATH', "{$base_path}manager/");
if (!defined('MODX_MANAGER_URL'))  define('MODX_MANAGER_URL', "{$site_url}manager/");

if (defined('IN_MANAGER_MODE')) init_mgr();

if (version_compare(PHP_VERSION, '5.3.0') < 0) @set_magic_quotes_runtime(0);

// include_once the magic_quotes_gpc workaround
if (get_magic_quotes_gpc()) include_once "{$core_path}quotes_stripper.inc.php";

if (!defined('ENT_COMPAT'))   define('ENT_COMPAT', 2);
if (!defined('ENT_NOQUOTES')) define('ENT_NOQUOTES', 0);
if (!defined('ENT_QUOTES'))   define('ENT_QUOTES', 3);

// set the document_root :|
if (!isset($_SERVER["DOCUMENT_ROOT"]) || empty($_SERVER["DOCUMENT_ROOT"]))
{
    $_SERVER["DOCUMENT_ROOT"] = str_replace($_SERVER["PATH_INFO"], "", preg_replace("/\\\\/", "/", $_SERVER["PATH_TRANSLATED"]))."/";
}



function init_mgr()
{
	// send anti caching headers
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("X-UA-Compatible: IE=edge;FF=3;OtherUA=4");
}

// start cms session
if(!function_exists('startCMSSession'))
{
	function startCMSSession()
	{
		global $site_sessionname;
		session_name($site_sessionname);
		session_start();
		$cookieExpiration= 0;
		if (isset ($_SESSION['mgrValidated']) || isset ($_SESSION['webValidated']))
		{
			$contextKey= isset ($_SESSION['mgrValidated']) ? 'mgr' : 'web';
			if (isset ($_SESSION['modx.' . $contextKey . '.session.cookie.lifetime']) && is_numeric($_SESSION['modx.' . $contextKey . '.session.cookie.lifetime']))
			{
				$cookieLifetime= intval($_SESSION['modx.' . $contextKey . '.session.cookie.lifetime']);
			}
			if ($cookieLifetime)
			{
				$cookieExpiration= time() + $cookieLifetime;
			}
			if (!isset($_SESSION['modx.session.created.time']))
			{
				$_SESSION['modx.session.created.time'] = time();
			}
		}
		setcookie(session_name(), session_id(), $cookieExpiration, MODX_BASE_URL);
	}
}

function assign_base_path()
{
	$conf_dir = str_replace("\\", '/', realpath(dirname(__FILE__)));
	$mgr_pos = strlen($conf_dir) - strlen('manager/includes');
	$base_path = substr($conf_dir,0,$mgr_pos);
	return rtrim($base_path,'/') . '/';
}

function assign_base_url($base_path)
{
	if(defined('IN_MANAGER_MODE'))
	{
		if(strpos($_SERVER['REQUEST_URI'],'/manager/')!==false)
			return substr($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'],'/manager/')) . '/';
		elseif(strpos($_SERVER['REQUEST_URI'],'/assets/')!==false)
			return substr($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'],'/assets/')) . '/';
	}
	if(strpos(str_replace('\\','/',$_SERVER['SCRIPT_FILENAME']),$_SERVER['SCRIPT_NAME'])===false)
	{
		if(strpos(str_replace('\\','/',$_SERVER['SCRIPT_FILENAME']),'/install/index.php')!==false)
			return substr($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'],'/install/')) . '/';
		else {
			echo 'Missing base_url';
			exit;
		}
	}
	$pos = strlen($_SERVER['SCRIPT_FILENAME']) - strlen($_SERVER['SCRIPT_NAME']);
	$dir = substr($_SERVER['SCRIPT_FILENAME'],$pos);
	$dir = str_replace('\\', '/', $dir);
	$dir = substr($dir,0,strrpos($dir,'/')) . '/';
	$dir = preg_replace('@(.*?)/manager/.*$@', '$1', $dir);
	$dir = preg_replace('@(.*?)/assets/.*$@', '$1', $dir);
	if(substr($_SERVER['REQUEST_URI'],0,2)==='/~') $dir = '/~' . substr($dir,1);
	$dir = rtrim($dir, '/') . '/';
	return $dir;
}

function assign_site_url($base_url)
{
	if(is_https()) $scheme = 'https://';
	else           $scheme = 'http://';
	
	$host = $_SERVER['HTTP_HOST'];
	
	$pos = strpos($host,':');
	if($pos!==false && ($_SERVER['SERVER_PORT'] == 80 || is_https()))
	{
		$host= substr($host,0,$pos);
	}
	$site_url = $scheme . $host . $base_url;
	return rtrim($site_url,'/') . '/';
}

function is_https()
{
	global $https_port;
	if((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || $_SERVER['SERVER_PORT'] == $https_port)
	{
		return true;
	}
	else return false;
}

function set_parser_mode()
{
	define('IN_ETOMITE_PARSER', 'true'); // provides compatibility with etomite 0.6 and maybe later versions
	define('IN_PARSER_MODE', 'true');
	define('IN_MANAGER_MODE', 'false');
	
	if (!defined('MODX_API_MODE')) define('MODX_API_MODE', false);
	// set some settings, and address some IE issues
	@ini_set('url_rewriter.tags', '');
	@ini_set('session.use_trans_sid', 0);
	@ini_set('session.use_only_cookies',1);
	session_cache_limiter('');
	header('P3P: CP="NOI NID ADMa OUR IND UNI COM NAV"'); // header for weird cookie stuff. Blame IE.
	header('Cache-Control: private, must-revalidate');
}

function show_install()
{
	header('Content-Type: text/html; charset=UTF-8');
	echo <<< EOT
<html><head><meta name="robots" content="noindex, nofollow">
<style type="text/css">
*{margin:0;padding:0}
html {font-size:100.01%;}
body{text-align:center;background:#eef0ee;font-size:92.5%;}
.install{width:530px;padding:10px;border:1px solid #b3c3af;background:#f6ffe0;margin:50px auto;font-family:Helvetica,Arial,sans-serif;text-align:center;}
p{ margin:20px 0; }
a{font-size:180%;color:#39b933;text-decoration:underline;margin-top: 30px;padding: 5px;}
</style></head>
<body>

<div class="install">
<p> <img src="install/img/install_begin.png" /> </ P>
<p> Конфигурационный MODX файл не найден или не установлен. </ P>
<p> Вы хотите, чтобы установить его сейчас ? </ P>
<p> <a href="install/index.php?action=mode">Да </ a> / <a href="http://modx.jp/"> Нет  </ a> </ P>
</ div> </ body> </ html>
EOT;
	exit;
}
