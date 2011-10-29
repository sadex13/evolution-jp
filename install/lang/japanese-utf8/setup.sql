# non-upgrade-able[[ - This block of code will not be executed during upgrades

# Default Site Template

REPLACE INTO `{PREFIX}site_templates` 
(id, templatename, description, editor_type, category, icon, template_type, content, locked) VALUES ('3','Minimal Template','Default minimal empty template (content returned only)','0','0','','0','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n<head>\r\n    <title>[*pagetitle*] | [(site_name)]</title>         <!--リソース変数pagetitleとコンフィグ変数site_name-->\r\n    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=[(modx_charset)]\" /> <!--コンフィグ変数modx_charset-->\r\n  <base href=\"[(site_url)]\" />               <!--コンフィグ変数site_url-->\r\n</head>\r\n<body>\r\n    <h1>[*pagetitle*]</h1>                    <!--リソース変数pagetitle-->\r\n     [*content*]                                         <!--リソース変数content-->\r\n</body>\r\n</html>\r\n','0');

REPLACE INTO `{PREFIX}user_roles` 
(id,name,description,frames,home,view_document,new_document,save_document,publish_document,delete_document,empty_trash,action_ok,logout,help,messages,new_user,edit_user,logs,edit_parser,save_parser,edit_template,settings,credits,new_template,save_template,delete_template,edit_snippet,new_snippet,save_snippet,delete_snippet,edit_chunk,new_chunk,save_chunk,delete_chunk,empty_cache,edit_document,change_password,error_dialog,about,file_manager,save_user,delete_user,save_password,edit_role,save_role,delete_role,new_role,access_permissions,bk_manager,new_plugin,edit_plugin,save_plugin,delete_plugin,new_module,edit_module,save_module,exec_module,delete_module,view_eventlog,delete_eventlog,manage_metatags,edit_doc_metatags,new_web_user,edit_web_user,save_web_user,delete_web_user,web_access_permissions,view_unpublished,import_static,export_static) VALUES 
(2,'投稿者','記事投稿専用に権限を制限したロール',1,1,1,1,1,1,1,0,1,1,1,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,1,0,1,0,1,1,1,1,1,1,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,1,0,0),
(3,'編集者','記事管理担当者用のロール。グローバル設定なども変更可',1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,0,0,1,1,1,1,1,1,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,1,0,0,0,0,0,0,0,1,0,0,0,0,0,1,1,1,1,0,1,0,0);

# ]]non-upgrade-able

INSERT IGNORE INTO `{PREFIX}system_settings` 
(setting_name, setting_value) VALUES 
('manager_theme','MODxCarbon'),
('settings_version',''),
('show_meta','0'),
('server_offset_time','0'),
('server_protocol','http'),
('manager_language','{MANAGERLANGUAGE}'),
('modx_charset','UTF-8'),
('site_name','My MODx Site'),
('site_start','1'),
('error_page','1'),
('unauthorized_page','1'),
('site_status','1'),
('site_unavailable_message','サイトは現在メンテナンス中です。しばらくお待ちください。'),
('track_visitors','0'),
('resolve_hostnames','0'),
('top_howmany','10'),
('default_template','3'),
('old_template',''),
('publish_default','1'),
('cache_default','1'),
('search_default','1'),
('friendly_urls','0'),
('friendly_url_prefix',''),
('friendly_url_suffix','.html'),
('friendly_alias_urls','1'),
('use_alias_path','1'),
('use_udperms','1'),
('udperms_allowroot','0'),
('failed_login_attempts','3'),
('blocked_minutes','60'),
('use_captcha','0'),
('captcha_words','isono,fuguta,sazae,masuo,katsuo,wakame,tarao,namihei,fune,tama,mokuzu,umihei,norisuke,taiko,ikura,sakeo,norio,isasaka,hanazawa,hanako,anago'),
('emailsender','{ADMINEMAIL}'),
('emailsubject','ログイン情報のお知らせ'),
('number_of_logs','100'),
('number_of_messages','30'),
('number_of_results','20'),
('use_editor','1'),
('use_browser','1'),
('rb_base_dir',''),
('rb_base_url',''),
('which_editor','TinyMCE'),
('fe_editor_lang','{MANAGERLANGUAGE}'),
('fck_editor_toolbar','standard'),
('fck_editor_autolang','0'),
('editor_css_path',''),
('editor_css_selectors',''),
('strip_image_paths','0'),
('upload_images','bmp,ico,gif,jpeg,jpg,png,psd,tif,tiff'),
('upload_media','au,avi,mp3,mp4,mpeg,mpg,wav,wmv'),
('upload_flash','fla,flv,swf'),
('upload_files','aac,au,avi,css,cache,doc,docx,gz,gzip,htaccess,htm,html,js,mp3,mp4,mpeg,mpg,ods,odp,odt,pdf,ppt,pptx,rar,tar,tgz,txt,wav,wmv,xls,xlsx,xml,z,zip'),
('upload_maxsize','1048576'),
('new_file_permissions','0644'),
('new_folder_permissions','0755'),
('filemanager_path',''),
('theme_refresher',''),
('manager_layout','4'),
('custom_contenttype','application/rss+xml,application/pdf,application/vnd.ms-word,application/vnd.ms-excel,text/html,text/css,text/xml,text/javascript,text/plain'),
('auto_menuindex','1'),
('session.cookie.lifetime','604800'),
('mail_check_timeperiod','60'),
('manager_direction','ltr'),
('tinymce_editor_theme','editor'),
('tinymce_custom_plugins','save,advlist,clearfloat,style,fullscreen,advimage,paste,advlink,media,contextmenu,table'),
('tinymce_custom_buttons1','undo,redo,|,bold,forecolor,backcolor,strikethrough,formatselect,fontsizeselect,pastetext,pasteword,code,|,fullscreen,help'),
('tinymce_custom_buttons2','image,media,link,unlink,anchor,|,justifyleft,justifycenter,justifyright,clearfloat,|,bullist,numlist,|,blockquote,outdent,indent,|,table,hr,|,styleprops,removeformat'),
('tinymce_css_selectors', '左寄せ=justifyleft;右寄せ=justifyright'),
('tree_show_protected', '0'),
('rss_url_news', 'http://feeds2.feedburner.com/modxjp'),
('rss_url_security', 'http://feeds2.feedburner.com/modxjpsec'),
('validate_referer', '1'),
('datepicker_offset','-10'),
('xhtml_urls','1'),
('allow_duplicate_alias','1'),
('automatic_alias','0'),
('datetime_format','YYYY/mm/dd'),
('warning_visibility', '0'),
('remember_last_tab', '0');


