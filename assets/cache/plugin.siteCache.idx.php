a:11:{s:18:"dashboard-yourinfo";s:2601:"global $_lang;

if(!empty($_SESSION['mgrLastlogin']))
{
     $Lastlogin = $modx->toDateFormat($_SESSION['mgrLastlogin']+$server_offset_time);
}
else $Lastlogin = '-';

$user_info = '
    <p>'.$_lang["yourinfo_message"].'</p>
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="150">'.$_lang["yourinfo_username"].'</td>
        <td width="20">&nbsp;</td>
        <td><b>'.$modx->getLoginUserName().'</b></td>
      </tr>
      <tr>
        <td>'.$_lang["yourinfo_role"].'</td>
        <td>&nbsp;</td>
        <td><b>'.$_SESSION['mgrPermissions']['name'].'</b></td>
      </tr>
      <tr>
        <td>'.$_lang["yourinfo_previous_login"].'</td>
        <td>&nbsp;</td>
        <td><b>' . $Lastlogin . '</b></td>
      </tr>
      <tr>
        <td>'.$_lang["yourinfo_total_logins"].'</td>
        <td>&nbsp;</td>
        <td><b>'.($_SESSION['mgrLogincount']+1).'</b></td>
      </tr>
    </table>
';

// recent document info
$uid = $modx->getLoginUserID();
$field = 'id, pagetitle, description, editedon, editedby';
$tbl_site_content = $modx->getFullTableName('site_content');
$where = "deleted=0 AND editedby='{$uid}'";
$rs = $modx->db->select($field,$tbl_site_content,$where,'editedon DESC',10);

$recent_info = $_lang["activity_message"].'<br /><br /><ul>';

if($modx->db->getRecordCount($rs) < 1) $recent_info .= '<li>'.$_lang['no_activity_message'].'</li>';
else
{
	$tpl = '<li><b>[+editedon+]</b> - [[+id+]] <a href="index.php?a=3&amp;id=[+id+]">[+pagetitle+]</a>[+description+]</li>';
	while($ph = $modx->db->getRow($rs))
	{
		$ph['editedon'] = $modx->toDateFormat($ph['editedon']);
		$ph['description'] = $ph['description']!='' ? ' - '.$ph['description'] : '';
		$recent_info .= $modx->parsePlaceholder($tpl,$ph);
	}
}
$recent_info.='</ul>';

$modx->setPlaceholder('recent_docs',$_lang['recent_docs']);
$ph = array();
$ph['UserInfo']       = $user_info;
$ph['info']           = $_lang['info'];
$ph['yourinfo_title'] = $_lang['yourinfo_title'];
$ph['RecentInfo']     = $recent_info;
$ph['activity_title'] = $_lang['activity_title'];

$block = <<< EOT
<div class="tab-page" id="tabYour" style="padding-left:0; padding-right:0">
	<h2 class="tab">[+yourinfo_title+]</h2>
	<script type="text/javascript">
		tpPane.addTabPage(document.getElementById("tabYour"));
	</script>
	<div class="sectionHeader">[+activity_title+]</div>
	<div class="sectionBody">
		[+RecentInfo+]
	</div>
	<div class="sectionHeader">[+yourinfo_title+]</div>
	<div class="sectionBody">
		[+UserInfo+]
	</div>
</div>
EOT;
$block = $modx->parsePlaceholder($block,$ph);
$modx->event->output($block);
";s:17:"dashboard3-online";s:2018:"global $_lang;

$ph['online'] = $_lang['online'];
$ph['onlineusers_title'] = $_lang['onlineusers_title'];
$timetocheck = (time()-(60*20));//+$server_offset_time;

include_once($modx->config['base_path'] . 'manager/includes/actionlist.inc.php');
$tbl_active_users = $modx->getFullTableName('active_users');
$rs = $modx->db->select('*',$tbl_active_users,"lasthit>'{$timetocheck}'",'username ASC');
$limit = $modx->db->getRecordCount($rs);
if($limit<2)
{
	$html = "<p>".$_lang['no_active_users_found']."</p>";
}
else
{
	$html = '<p>' . $_lang["onlineusers_message"].'<b>'.strftime('%H:%M:%S', time()+$server_offset_time).'</b>)</p>';
	$html .= '
	<table border="0" cellpadding="1" cellspacing="1" width="100%" bgcolor="#ccc">
	<thead>
	<tr>
	<td><b>'.$_lang["onlineusers_user"].'</b></td>
	<td><b>'.$_lang["onlineusers_userid"].'</b></td>
	<td><b>'.$_lang["onlineusers_ipaddress"].'</b></td>
	<td><b>'.$_lang["onlineusers_lasthit"].'</b></td>
	<td><b>'.$_lang["onlineusers_action"].'</b></td>
	</tr>
	</thead>
	<tbody>
	';
	while ($row = $modx->db->getRow($rs))
	{
		$currentaction = getAction($row['action'], $row['id']);
		$webicon = ($row['internalKey']<0)? '<img src="' . $style_path . 'tree/globe.gif" alt="Web user" />':'';
		$html.= "<tr bgcolor='#FFFFFF'><td><b>".$row['username']."</b></td><td>{$webicon}&nbsp;".abs($row['internalKey'])."</td><td>".$row['ip']."</td><td>".strftime('%H:%M:%S', $row['lasthit']+$server_offset_time)."</td><td>{$currentaction}</td></tr>";
	}
        $html.= '
                </tbody>
                </table>
        ';
    }
$ph['OnlineInfo'] = $html;



$block = <<< EOT
<div class="tab-page" id="tabOnline" style="padding-left:0; padding-right:0">
	<h2 class="tab">[+online+]</h2>
	<script type="text/javascript">tpPane.addTabPage( document.getElementById( "tabOnline" ) );</script>
	<div class="sectionHeader">[+onlineusers_title+]</div><div class="sectionBody">
		[+OnlineInfo+]
	</div>
</div>
EOT;

$block = $modx->parsePlaceholder($block,$ph);
$modx->event->output($block);
";s:29:"Bindings機能の有効無効";s:1523:"$e = &$modx->event; 
global $settings;
$action = $modx->manager->action;
if($action!==17) return;
$enable_bindings = (is_null($settings['enable_bindings'])) ? '1' : $settings['enable_bindings'];
$html = render_html($enable_bindings);
$e->output($html);

function render_html($enable_bindings)
{
	global $_lang;
	$str = '<h4 style="padding:5px;background-color:#eeeeee;">@Bindingsの設定</h4><table id="enable_bindings" class="settings">' . "\n";
	$str .= '  <tr>' . "\n";
	$str .= '    <th>@Bindingsを有効にする</th>' . "\n";
	$str .= '    <td><input onchange="documentDirty=true;" type="radio" name="enable_bindings" value="1" ' . ($enable_bindings=='1' ? 'checked="checked"' : "") . ' />' . "\n";
	$str .=       $_lang["yes"] . '<br />' . "\n";
	$str .= '      <input onchange="documentDirty=true;" type="radio" name="enable_bindings" value="0" ' . (($enable_bindings=='0' || !isset($enable_bindings)) ? 'checked="checked"' : "" ) . ' />' . "\n";
	$str .=       $_lang["no"] . '<div><a href="http://www.google.com/cse?cx=007286147079563201032%3Aigbcdgg0jyo&q=Bindings" target="_blank">@Bindings機能</a>を有効にします。この機能は、投稿画面上の入力フィールド(テンプレート変数)に任意のコマンドを記述し、実行するものです。PHP文の実行などが可能なため、複数メンバーでサイトを運用する場合、当機能の運用には注意が必要です。</div></td>' . "\n";
	$str .= '  </tr>' . "\n";
	$str .= '</table>' . "\n";
	return $str;
}
";s:20:"Forgot Manager Login";s:6667:"if(!class_exists('ForgotManagerPassword'))
{
	class ForgotManagerPassword
	{
		function ForgotManagerPassword()
		{
			$this->errors = array();
			$this->checkLang();
		}
	
		function getLink()
		{
			global $_lang;
			
			$link = <<<EOD
<a id="ForgotManagerPassword-show_form" href="index.php?action=show_form">{$_lang['forgot_your_password']}</a>
EOD;
			return $link;
		}
			
		function getForm()
		{
			global $_lang;
			
			$form = <<< EOD
<label id="FMP-email_label" for="FMP_email">{$_lang['account_email']}:</label>
<input id="FMP-email" type="text" />
<button id="FMP-email_button" type="button" onclick="window.location = 'index.php?action=send_email&email='+document.getElementById('FMP-email').value;">{$_lang['send']}</button>
EOD;
			return $form;
		}
		
		/* Get user info including a hash unique to this user, password, and day */
		function getUser($user_id=false, $username='', $email='', $hash='')
		{
			global $modx, $_lang;
			
			if($user_id !== false) $user_id = $modx->db->escape($user_id);
			$username = $modx->db->escape($username);
			$email    = $modx->db->escape($email);
			$hash     = $modx->db->escape($hash);
			
			$tbl_manager_users   = $modx->getFullTableName('manager_users');
			$tbl_user_attributes = $modx->getFullTableName('user_attributes');
			$site_id = $modx->config['site_id'];
			$today = date('Yz'); // Year and day of the year
			$wheres = array();
			$where = '';
			$user = null;
			
			$user_id  = ($user_id == false) ? false : $modx->db->escape($user_id);
			if(!empty($username))  { $wheres[] = "usr.username = '{$username}'"; }
			if(!empty($username))  { $wheres[] = "usr.username = '{$username}'"; }
			if(!empty($email))     { $wheres[] = "attr.email = '{$email}'"; }
			if(!empty($hash))      { $wheres[] = "MD5(CONCAT(usr.username,usr.password,'{$site_id}','{$today}')) = '{$hash}'"; } 
			
			if($wheres)
			{
				$where = implode(' AND ',$wheres);
				$field = "usr.id, usr.username, attr.email, MD5(CONCAT(usr.username,usr.password,'{$site_id}','{$today}')) AS hash";
				$from = "{$tbl_manager_users} usr INNER JOIN {$tbl_user_attributes} attr ON usr.id = attr.internalKey";
				if($result = $modx->db->select($field,$from,$where,'',1))
				{
					if($modx->db->getRecordCount($result)==1)
					{
						$user = $modx->db->getRow($result);
					}
				}
			}
			
			if($user == null) { $this->errors[] = $_lang['could_not_find_user']; }
			
			return $user;
		}
		
		/* Send an email with a link to login */
		function sendEmail($to)
		{
			global $modx, $_lang;
			
			$user = $this->getUser(0, '', $to);
			if(!$user['username']) return;
			
			
			if($modx->config['use_captcha']==='1')
			{
				$captcha = '&captcha_code=ignore';
			}
			else $captcha = '';
			$body = <<< EOT
{$_lang['forgot_password_email_intro']}

{$modx->config['site_url']}manager/index.php?name={$user['username']}&hash={$user['hash']}{$captcha}
{$_lang['forgot_password_email_link']}

{$_lang['forgot_password_email_instructions']}
{$_lang['forgot_password_email_fine_print']}
EOT;
			$mail['subject'] = $_lang['password_change_request'];
			$mail['sendto'] = $to;
			$result = $modx->sendmail($mail,$body);
			
			if(!$result) $this->errors[] = $_lang['error_sending_email'];
			return $result;
		}
		
		function unblockUser($user_id)
		{
			global $modx, $_lang;
			
			$tbl_user_attributes = $modx->getFullTableName('user_attributes');
			$modx->db->update('blocked=0,blockeduntil=0,failedlogincount=0', $tbl_user_attributes, "internalKey='{$user_id}'");
			
			if(!$modx->db->getAffectedRows()) { $this->errors[] = $_lang['user_doesnt_exist']; return; }
			
			return true;
		}
		
		function checkLang()
		{
			global $_lang;
			
			$eng = array();
			$eng['forgot_your_password'] = 'Forgot your password?';
			$eng['account_email'] = 'Account email';
			$eng['send'] = 'Send';
			$eng['password_change_request'] = 'Password change request';
			$eng['forgot_password_email_intro'] = 'A request has been made to change the password on your account.';
			$eng['forgot_password_email_link'] = 'Click here to complete the process.';
			$eng['forgot_password_email_instructions'] = 'From there you will be able to change your password from the My Account menu.';
			$eng['forgot_password_email_fine_print'] = '* The URL above will expire once you change your password or after today.';
			$eng['error_sending_email'] = 'Error sending email';
			$eng['could_not_find_user'] = 'Could not find user';
			$eng['user_doesnt_exist'] = 'User does not exist';
			$eng['email_sent'] = 'Email sent';
			
			foreach($eng as $key=>$value)
			{
				if(empty($_lang[$key])) { $_lang[$key] = $value; }
			}  
		}
		
		function getErrorOutput()
		{
			$output = '';
			
			if($this->errors)
			{
				$output = '<span class="error">'.implode('</span><span class="errors">', $this->errors).'</span>';
			}
			return $output;
		}
	}
}

global $_lang;

$output = '';
$event_name = $modx->event->name;
$action   = (empty($_GET['action'])   ? ''    : $_GET['action']);
$username = (empty($_GET['username']) ? false : $_GET['username']);
$to       = (empty($_GET['email'])    ? ''    : $_GET['email']);
$hash     = (empty($_GET['hash'])     ? false : $_GET['hash']);
$forgot   = new ForgotManagerPassword();

if($event_name == 'OnManagerLoginFormPrerender' && isset($_GET['hash']) && isset($_GET['name']))
{
	if($modx->config['use_captcha']==='1')
	{
		$captcha = '&captcha_code=ignore';
	}
	else $captcha = '';

	$url = "{$modx->config['site_url']}manager/processors/login.processor.php?username={$_GET['name']}&hash={$hash}{$captcha}";
	header("Location:{$url}");
	exit;
}

if($event_name == 'OnManagerLoginFormRender')
{
	switch($action)
	{
		case 'show_form':
			$output = $forgot->getForm();
			break;
		case 'send_email':
			if($forgot->sendEmail($to))
			{
				$output = $_lang['email_sent'];
			}
			break;
		default:
			$output = $forgot->getLink();
			break;
	}
	
	if($forgot->errors) { $output = $forgot->getErrorOutput() . $forgot->getLink(); }
	$modx->event->output($output);
}

if($event_name == 'OnBeforeManagerLogin')
{
	$user = $forgot->getUser(false, '', '', $hash);
	if($user && is_array($user) && !$forgot->errors)
	{
		$forgot->unblockUser($user['id']);
	}
}

if($event_name == 'OnManagerAuthentication' && $hash && $username)
{
	if($hash) $_SESSION['mgrForgetPassword'] = '1';
	$user = $forgot->getUser(false, '', '', $hash);
	if($user !== null && count($forgot->errors) == 0)
	{
		if(isset($_GET['captcha_code'])) $_SESSION['veriword'] = $_GET['captcha_code'];
		if(!$hash && $_SESSION['mgrForgetPassword']) unset($_SESSION['mgrForgetPassword']);
		$output =  true;
	}
	else $output = false;
	$modx->event->output($output);
}
";s:30:"管理画面カスタマイズ";s:1004:"/* 当プラグインの使い方

1. チャンク「ログイン画面」「ダッシュボード」を作成します。
2. assets/templates/manager/ディレクトリのlogin.html・welcome.htmlの内容を各チャンクにコピー

当プラグインを無効にした場合はassets/templates/manager/ディレクトリのコードが出力されます。
assets/templates/manager/ディレクトリにファイルがない場合はMODX本体内蔵のコードが出力されます。
もしコードを書き間違えてログインできなくなった場合はmanager/index.php内の
「$modx->safeMode = true;」行頭のコメントアウトを削除してログインし、修正してください。

*/

switch($modx->event->name)
{
	case 'OnManagerLoginFormPrerender':
		$src = $modx->getChunk('ログイン画面');
		break;
	case 'OnManagerWelcomePrerender':
		$src = $modx->getChunk('ダッシュボード');
		break;
}
if($src!==false && !empty($src))
{
	global $tpl;
	$tpl = $src;
}
";s:14:"ManagerManager";s:1367:"// You can put your ManagerManager rules EITHER in a chunk OR in an external file - whichever suits your development style the best

// To use an external file, put your rules in /assets/plugins/managermanager/mm_rules.inc.php
// (you can rename default.mm_rules.inc.php and use it as an example)
// The chunk SHOULD have php opening tags at the beginning and end

// If you want to put your rules in a chunk (so you can edit them through the Manager),
// create the chunk, and enter its name in the configuration tab.
// The chunk should NOT have php tags at the beginning or end

// ManagerManager requires jQuery 1.3+
// The URL to the jQuery library. Choose from the configuration tab whether you want to use
// a local copy (which defaults to the jQuery library distributed with ModX 1.0.1)
// a remote copy (which defaults to the Google Code hosted version)
// or specify a URL to a custom location.
// Here we set some default values, because this is a convenient place to change them if we need to,
// but you should configure your preference via the Configuration tab.

// You don't need to change anything else from here onwards
//-------------------------------------------------------

// Run the main code
$mm_path = $modx->config['base_path'] . 'assets/plugins/managermanager/mm.inc.php';
include_once($mm_path);
$mm = new MANAGERMANAGER();
$mm->run();
";s:19:"ManagerManagerProps";s:285:"&config_chunk=Configuration Chunk;text;mm_rules; &remove_deprecated_tv_types_pref=Remove deprecated TV types;list;yes,no;yes &which_jquery=jQuery source;list;local (assets/js),remote (google code),manual url (specify below);local (assets/js) &js_src_override=jQuery URL override;text; ";s:14:"Quick Manager+";s:283:"$version = '1.5.5r5';

// In manager
if (isset($_SESSION['mgrValidated']))
{
	include_once($modx->config['base_path'].'assets/plugins/qm/qm.inc.php');
	$modx->event->params['version'] = $version;
	$qm = new Qm($modx, $modx->event->params);
	$qm->jqpath = 'assets/js/jquery.min.js';
}";s:19:"Quick Manager+Props";s:1310:"&loadmanagerjq=Load jQuery in manager;list;true,false;true &loadfrontendjq=Load jQuery in front-end;list;true,false;true &noconflictjq=jQuery noConflict mode in front-end;list;true,false;true &loadtb=Load modal box in front-end;list;true,false;true &tbwidth=Modal box window width;text;80% &tbheight=Modal box window height;text;90% &hidefields=Hide document fields from front-end editors;text;parent &hidetabs=Hide document tabs from front-end editors;text; &hidesections=Hide document sections from front-end editors;text; &addbutton=Show add document here button;list;true,false;true &tpltype=New document template type;list;config,parent,id,selected,sibling,system;config &tplid=New document template id;int; &custombutton=Custom buttons;textarea; &1=undefined;; &managerbutton=Show go to manager button;list;true,false;true &logout=Logout to;list;manager,front-end;manager &disabled=Plugin disabled on documents;text; &autohide=Autohide toolbar;list;true,false;true &editbuttons=Inline edit buttons;list;true,false;false &editbclass=Edit button CSS class;text;qm-edit &newbuttons=Inline new resource buttons;list;true,false;false &newbclass=New resource button CSS class;text;qm-new &tvbuttons=Inline template variable buttons;list;true,false;false &tvbclass=Template variable button CSS class;text;qm-tv ";s:24:"TinyMCE Rich Text Editor";s:4041:"// Set the name of the plugin folder
$plugin_dir = "tinymce";
$mce_version = '3.5.4.1';

// Set path and base setting variables
if(!isset($mce_path))
{ 
	$mce_path = MODX_BASE_PATH . 'assets/plugins/'.$plugin_dir . '/'; 
	$mce_url  = MODX_BASE_URL  . 'assets/plugins/'.$plugin_dir . '/'; 
}
$params = $modx->event->params;
$params['mce_path']         = $mce_path;
$params['mce_url']          = $mce_url;

include_once $mce_path . 'functions.php';

$mce = new TinyMCE($params);

// Handle event
$e = &$modx->event; 
switch ($e->name)
{
	case "OnRichTextEditorRegister": // register only for backend
		$e->output("TinyMCE");
		break;

	case "OnRichTextEditorInit": 
		if($editor!=="TinyMCE") return;
		
		$params['mce_version']     = $mce_version;
		$params['css_selectors']   = $modx->config['tinymce_css_selectors'];
		$params['use_browser']     = $modx->config['use_browser'];
		$params['editor_css_path'] = $modx->config['editor_css_path'];
		
		if($modx->isBackend() || (intval($_GET['quickmanagertv']) == 1 && isset($_SESSION['mgrValidated'])))
		{
			$params['theme']              = $modx->config['tinymce_editor_theme'];
			$params['mce_editor_skin']    = $modx->config['mce_editor_skin'];
			$params['mce_entermode']      = $modx->config['mce_entermode'];
			$params['language']           = get_mce_lang($modx->config['manager_language']);
			$params['frontend']           = false;
			$params['custom_plugins']     = $modx->config['tinymce_custom_plugins'];
			$params['custom_buttons1']    = $modx->config['tinymce_custom_buttons1'];
			$params['custom_buttons2']    = $modx->config['tinymce_custom_buttons2'];
			$params['custom_buttons3']    = $modx->config['tinymce_custom_buttons3'];
			$params['custom_buttons4']    = $modx->config['tinymce_custom_buttons4'];
			$params['toolbar_align']      = $modx->config['manager_direction'];
			$params['webuser']            = null;
			
			$html = $mce->get_mce_script($params);
		}
		else
		{
			$frontend_language = isset($modx->config['fe_editor_lang']) ? $modx->config['fe_editor_lang']:'';
			$webuser = (isset($modx->config['rb_webuser']) ? $modx->config['rb_webuser'] : null);
			
			$params['theme']           = $webtheme;
			$params['webuser']         = $webuser;
			$params['language']        = get_mce_lang($frontend_language);
			$params['frontend']        = true;
			$params['custom_plugins']  = $webPlugins;
			$params['custom_buttons1'] = $webButtons1;
			$params['custom_buttons2'] = $webButtons2;
			$params['custom_buttons3'] = $webButtons3;
			$params['custom_buttons4'] = $webButtons4;
			$params['toolbar_align']   = $webAlign;
			
			$html = $mce->get_mce_script($params);
		}
		$e->output($html);
		break;

	case "OnInterfaceSettingsRender":
		global $usersettings,$settings;
		switch ($modx->manager->action)
		{
    		case 11:
        		$mce_settings = array();
        		break;
    		case 12:
        		$mce_settings = $usersettings;
        		break;
    		case 17:
        		$mce_settings = $settings;
        		break;
    		default:
        		$mce_settings = $settings;
        		break;
    	}
    	
		$params['theme']              = $mce_settings['tinymce_editor_theme'];
		$params['mce_editor_skin']    = $mce_settings['mce_editor_skin'];
		$params['mce_entermode']      = $mce_settings['mce_entermode'];
		$params['mce_element_format'] = $mce_settings['mce_element_format'];
		$params['mce_schema']         = $mce_settings['mce_schema'];
		$params['css_selectors']      = $mce_settings['tinymce_css_selectors'];
		$params['custom_plugins']     = $mce_settings['tinymce_custom_plugins'];
		$params['custom_buttons1']    = $mce_settings['tinymce_custom_buttons1'];
		$params['custom_buttons2']    = $mce_settings['tinymce_custom_buttons2'];
		$params['custom_buttons3']    = $mce_settings['tinymce_custom_buttons3'];
		$params['custom_buttons4']    = $mce_settings['tinymce_custom_buttons4'];
    	
		$html = $mce->get_mce_settings($params);
		$e->output($html);
		break;

   default :    
      return; // stop here - this is very important. 
      break; 
}
";s:29:"TinyMCE Rich Text EditorProps";s:1209:"&customparams=Custom Parameters;textarea;valid_elements : "*[*]", &mce_formats=Block Formats;text;p,h1,h2,h3,h4,h5,h6,div,blockquote,code,pre &entity_encoding=Entity Encoding;list;named,numeric,raw;named &entities=Entities;text; &mce_path_options=Path Options;list;Site config,Absolute path,Root relative,URL,No convert;Site config &mce_resizing=Advanced Resizing;list;true,false;true &disabledButtons=Disabled Buttons;text; &link_list=Link List;list;enabled,disabled;enabled &webtheme=Web Theme;list;simple,editor,creative,custom;simple &webPlugins=Web Plugins;text;style,advimage,advlink,searchreplace,contextmenu,paste,fullscreen,xhtmlxtras,media &webButtons1=Web Buttons 1;text;undo,redo,selectall,|,pastetext,pasteword,|,search,replace,|,hr,charmap,|,image,link,unlink,anchor,media,|,cleanup,removeformat,|,fullscreen,code,help &webButtons2=Web Buttons 2;text;bold,italic,underline,strikethrough,sub,sup,|,|,blockquote,bullist,numlist,outdent,indent,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,|,styleprops &webButtons3=Web Buttons 3;text; &webButtons4=Web Buttons 4;text; &webAlign=Web Toolbar Alignment;list;ltr,rtl;ltr &width=Width;text;95% &height=Height;text;500 ";}