<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_wysiwyg($params) {
	$path = ACP3_ROOT . 'includes/wysiwyg/' . CONFIG_WYSIWYG . '/editor.php';
	$params['name'] = 'form[' . $params['name'] . ']';

	if (is_file($path) && !preg_match('=/=', CONFIG_WYSIWYG)) {
		require_once $path;

		return editor($params);
	} else {
		global $modules;
		
		$out = '';
		$id = substr($params['name'], 5, -1);

		// Falls aktiv, die Emoticons einbinden
		if ($modules->check('emoticons', 'functions')) {
			include_once ACP3_ROOT . 'modules/emoticons/functions.php';
			$out.= emoticonsList($id);
		}
		$out.= '<textarea name="' . $params['name'] . '" id="' . $id . '" cols="50" rows="6">' . (!empty($params['value']) ? $params['value'] : '') . '</textarea>';
		return $out;
	}
}
/* vim: set expandtab: */
?>