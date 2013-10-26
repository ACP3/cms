<?php
/**
 * Fügt die angegebene JavaScript-Datei in ein Template ein
 *
 * @param array $params
 * @return string
 */
function smarty_function_include_js($params)
{
	if (isset($params['module'], $params['file']) === true &&
		(bool) preg_match('=/=', $params['module']) === false &&
		(bool) preg_match('=/=', $params['file']) === false) {
		$script = '<script type="text/javascript" src="%s"></script>';
		$module = ucfirst($params['module']);
		$file = $params['file'];
		if (is_file(DESIGN_PATH_INTERNAL . $module . '/' . $file . '.js') === true) {
			return sprintf($script, DESIGN_PATH . $module . '/' . $file . '.js');
		} elseif (is_file(MODULES_DIR . $module . '/templates/' . $file . '.js') === true) {
			return sprintf($script, ROOT_DIR . 'ACP3/Modules/' . $module . '/templates/' . $file . '.js');
		}
	} else {
		return 'Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!';
	}
}
/* vim: set expandtab: */