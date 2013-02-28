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
	require_once INCLUDES_DIR . 'wysiwyg/ckeditor/ckeditor_php5.php';

	$ckeditor = new CKEditor(ROOT_DIR . 'includes/wysiwyg/ckeditor/');
	$ckeditor->returnOutput = true;

	$basepath = ROOT_DIR . 'includes/wysiwyg/ckeditor/';
	$filebrowser_uri = ROOT_DIR . 'libraries/kcfinder/browse.php%s';
	$upload_uri = ROOT_DIR . 'libraries/kcfinder/upload.php%s';

	$config = array();
	$config['filebrowserBrowseUrl'] = sprintf($filebrowser_uri, '?type=files');
	$config['filebrowserImageBrowseUrl'] = sprintf($filebrowser_uri, '?type=gallery');
	$config['filebrowserFlashBrowseUrl'] = sprintf($filebrowser_uri, '?type=files');
	$config['filebrowserUploadUrl'] = sprintf($upload_uri, '?type=files');
	$config['filebrowserImageUploadUrl'] = sprintf($upload_uri, '?type=gallery');
	$config['filebrowserFlashUploadUrl'] = sprintf($upload_uri, '?type=files');

	if (isset($params['height']))
		$config['height'] = $params['height'] . 'px';
	if (isset($params['toolbar']))
		$config['toolbar'] = $params['toolbar'] === 'simple' ? 'Basic' : 'Full';

	// Smilies
	if ((!isset($config['toolbar']) || $config['toolbar'] !== 'simple') && ACP3_Modules::check('emoticons', 'functions') === true) {
		$config['smiley_path'] = ROOT_DIR . 'uploads/emoticons/';
		$config['smiley_images'] = $config['smiley_descriptions'] = '';
		$emoticons = ACP3_CMS::$db2->fetchAll('SELECT description, img FROM ' . DB_PRE . 'emoticons');
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

	$wysiwyg = array(
		'id' => $params['id'],
		'editor' => $ckeditor->editor($params['name'], $params['id'], $params['value'], $config),
		'advanced' => isset($params['advanced']) && $params['advanced'] == 1 ? true : false,
	);

	if ($wysiwyg['advanced'] === true)
		$wysiwyg['advanced_replace_content'] = 'CKEDITOR.instances.' . $wysiwyg['id'] . '.insertHtml(text);';

	ACP3_CMS::$view->assign('wysiwyg', $wysiwyg);
	return ACP3_CMS::$view->fetchTemplate('system/wysiwyg.tpl');
}