<?php
/**
* Smarty function plugin (function.fckeditor.php)
* Requires PHP >= 4.3.0
* -------------------------------------------------------------
* Type:     function
* Name:     fckeditor
* Version:  1.0
* Author:   gazoot (gazoot care of gmail dot com)
* Purpose:  Creates a FCKeditor, a very powerful textarea replacement.
* -------------------------------------------------------------
* @param name Editor instance name (form field name)
* @param BasePath optional Path to the FCKeditor directory. Need only be set once on page. Default: /FCKeditor/
* @param value optional data that control will start with, default is taken from the javascript file
* @param height optional height (css units)
* @param toolbar optional what toolbar to use from configuration
*
* Default values for optional parameters (except BasePath) are taken from fckeditor.js.
*
* All other parameters used in the function will be put into the configuration section,
* CustomConfigurationsPath is useful for example.
* See http://wiki.fckeditor.net/Developer%27s_Guide/Configuration/Configurations_File for more configuration info.
*/
function editor($params) {
	require_once ACP3_DIR . 'wysiwyg/ckeditor/ckeditor.php';

	$filebrowser_uri = ROOT_DIR . 'libraries/kcfinder/browse.php%s&cms=acp3';
	$upload_uri = ROOT_DIR . 'libraries/kcfinder/upload.php%s&cms=acp3';

	$config = array();
	$config['filebrowserBrowseUrl'] = sprintf($filebrowser_uri, '?type=files&amp;cms=');
	$config['filebrowserImageBrowseUrl'] = sprintf($filebrowser_uri, '?type=gallery');
	$config['filebrowserFlashBrowseUrl'] = sprintf($filebrowser_uri, '?type=files');
	$config['filebrowserUploadUrl'] = sprintf($upload_uri, '?type=files');
	$config['filebrowserImageUploadUrl'] = sprintf($upload_uri, '?type=gallery');
	$config['filebrowserFlashUploadUrl'] = sprintf($upload_uri, '?type=files');

	if (isset($params['height']))
		$config['height'] = $params['height'] . 'px';
	if (isset($params['toolbar']))
		$config['toolbar'] = $params['toolbar'] === 'simple' ? 'Basic' : 'Full';

	$config['extraPlugins'] = 'divarea,oembed,codemirror';
	$config['allowedContent'] = true;
	$config['codemirror'] = '@@{ theme: \'default\',
	lineNumbers: true,
	lineWrapping: true,
	matchBrackets: true,
	autoCloseTags: true,
	autoCloseBrackets: true,
	enableSearchTools: true,
	enableCodeFolding: true,
	enableCodeFormatting: true,
	autoFormatOnStart: true,
	autoFormatOnUncomment: true,
	highlightActiveLine: true,
	highlightMatches: true,
	showFormatButton: false,
	showCommentButton: false,
	showUncommentButton: false
}';

	// Smilies
	if ((!isset($config['toolbar']) || $config['toolbar'] !== 'simple') && \ACP3\Core\Modules::check('emoticons', 'functions') === true) {
		$config['smiley_path'] = ROOT_DIR . 'uploads/emoticons/';
		$config['smiley_images'] = $config['smiley_descriptions'] = '';
		$emoticons = \ACP3\CMS::$injector['Db']->fetchAll('SELECT description, img FROM ' . DB_PRE . 'emoticons');
		$c_emoticons = count($emoticons);

		for ($i = 0; $i < $c_emoticons; ++$i) {
			$config['smiley_images'].= '\'' . $emoticons[$i]['img'] . '\',';
			$config['smiley_descriptions'].= '\'' . $emoticons[$i]['description'] . '\',';
		}

		$config['smiley_images'] = '@@[' . substr($config['smiley_images'], 0, -1) . ']';
		$config['smiley_descriptions'] = '@@[' . substr($config['smiley_descriptions'], 0, -1) . ']';
	}
	// Basic Toolbar erweitern
	if (isset($config['toolbar']) && $config['toolbar'] == 'Basic') {
		$config['toolbar_Basic'] = "@@[ ['Source','-','Undo','Redo','-','Bold','Italic','-','NumberedList','BulletedList','-','Link','Unlink','-','About'] ]";
	}

	$ckeditor = new CKEditor(ROOT_DIR . 'ACP3/wysiwyg/ckeditor/');
	$ckeditor->returnOutput = true;

	$wysiwyg = array(
		'id' => $params['id'],
		'editor' => $ckeditor->editor($params['name'], $params['id'], $params['value'], $config),
		'advanced' => isset($params['advanced']) && $params['advanced'] == 1 ? true : false,
	);

	if ($wysiwyg['advanced'] === true)
		$wysiwyg['advanced_replace_content'] = 'CKEDITOR.instances.' . $wysiwyg['id'] . '.insertHtml(text);';

	\ACP3\CMS::$injector['View']->assign('wysiwyg', $wysiwyg);
	return \ACP3\CMS::$injector['View']->fetchTemplate('system/wysiwyg.tpl');
}