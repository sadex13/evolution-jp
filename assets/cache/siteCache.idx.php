<?php
$c = &$this->contentTypes;
$e = &$this->pluginEvent;
$e['OnBeforeDocFormSave'] = array('ManagerManager');
$e['OnBeforeManagerLogin'] = array('Forgot Manager Login');
$e['OnDocFormPrerender'] = array('Quick Manager+','ManagerManager');
$e['OnDocFormRender'] = array('ManagerManager');
$e['OnDocFormSave'] = array('Quick Manager+');
$e['OnInterfaceSettingsRender'] = array('TinyMCE Rich Text Editor');
$e['OnManagerAuthentication'] = array('Forgot Manager Login');
$e['OnManagerLoginFormPrerender'] = array('Forgot Manager Login','管理画面カスタマイズ');
$e['OnManagerLoginFormRender'] = array('Forgot Manager Login');
$e['OnManagerLogout'] = array('Quick Manager+');
$e['OnManagerMainFrameHeaderHTMLBlock'] = array('ManagerManager');
$e['OnManagerWelcomePrerender'] = array('管理画面カスタマイズ');
$e['OnManagerWelcomeRender'] = array('dashboard-yourinfo','dashboard3-online');
$e['OnParseDocument'] = array('Quick Manager+');
$e['OnPluginFormRender'] = array('ManagerManager');
$e['OnRichTextEditorInit'] = array('TinyMCE Rich Text Editor');
$e['OnRichTextEditorRegister'] = array('TinyMCE Rich Text Editor');
$e['OnTVFormRender'] = array('ManagerManager');
$e['OnUserSettingsRender'] = array('Bindings機能の有効無効');
$e['OnWebPagePrerender'] = array('Quick Manager+');

