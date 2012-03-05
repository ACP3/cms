<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_wysiwyg($params) {
	$path = INCLUDES_DIR . 'wysiwyg/' . CONFIG_WYSIWYG . '/editor.php';
	$params['id'] = !empty($params['id']) ? $params['id'] : $params['name'];

	// WYSIWYG Editor einbinden
	if (CONFIG_WYSIWYG !== 'textarea' && is_file($path) === true && !preg_match('=/=', CONFIG_WYSIWYG)) {
		require_once $path;

		return editor($params);
	// Einfache textarea erzeugen
	} else {
		$out = '';

		// Falls aktiv, die Emoticons einbinden
		if (ACP3_Modules::check('emoticons', 'functions') === true) {
			include_once MODULES_DIR . 'emoticons/functions.php';
			$out.= emoticonsList($params['id']);
		}
		$out.= '<textarea name="' . $params['name'] . '" id="' . $params['id'] . '" cols="50" rows="6">' . (!empty($params['value']) ? $params['value'] : '') . '</textarea>';
		return $out;
	}
}
/* vim: set expandtab: */