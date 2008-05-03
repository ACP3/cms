<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

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
* @param InstanceName Editor instance name (form field name)
* @param BasePath optional Path to the FCKeditor directory. Need only be set once on page. Default: /FCKeditor/
* @param Value optional data that control will start with, default is taken from the javascript file
* @param Width optional width (css units)
* @param Height optional height (css units)
* @param ToolbarSet optional what toolbar to use from configuration
* @param CheckBrowser optional check the browser compatibility when rendering the editor
* @param DisplayErrors optional show error messages on errors while rendering the editor
*
* Default values for optional parameters (except BasePath) are taken from fckeditor.js.
*
* All other parameters used in the function will be put into the configuration section,
* CustomConfigurationsPath is useful for example.
* See http://wiki.fckeditor.net/Developer%27s_Guide/Configuration/Configurations_File for more configuration info.
*/
function smarty_function_fckeditor($params, &$smarty) {
	if (!isset($params['InstanceName']) || empty($params['InstanceName']))
		$smarty->trigger_error('fckeditor: required parameter "InstanceName" missing');

	static $base_arguments = array(), $config_arguments = array();

	// Test if editor has been loaded before
	$init = count($base_arguments) ? true : false;

	$base_arguments['BasePath'] = ROOT_DIR . '/includes/fckeditor/';

	$base_arguments['InstanceName'] = 'form[' . $params['InstanceName'] . ']';

	if (isset($params['Value']))
		$base_arguments['Value'] = $params['Value'];
	if (isset($params['Width']))
		$base_arguments['Width'] = $params['Width'];
	if (isset($params['Height']))
		$base_arguments['Height'] = $params['Height'];
	if (isset($params['ToolbarSet']))
		$base_arguments['ToolbarSet'] = $params['ToolbarSet'];
	if (isset($params['CheckBrowser']))
		$base_arguments['CheckBrowser'] = $params['CheckBrowser'];
	if (isset($params['DisplayErrors']))
		$base_arguments['DisplayErrors'] = $params['DisplayErrors'];

	// Use all other parameters for the config array (replace if needed)
	$other_arguments = array_diff_assoc($params, $base_arguments);
	$config_arguments = array_merge($config_arguments, $other_arguments);

	$out = '';

	if (!$init)
		$out.= '<script type="text/javascript" src="' . $base_arguments['BasePath'] . 'fckeditor.js"></script>' . "\n";

	$out.= '<script type="text/javascript">' . "\n";
	$out.= '//<![CDATA[' . "\n";
	$out.= 'var oFCKeditor = new FCKeditor(\'' . $base_arguments['InstanceName'] . '\');' . "\n";

	foreach ($base_arguments as $key => $value) {
		// Fix newlines, javascript cannot handle multiple line strings very well.
		if (!is_bool($value))
			$value = '"' . preg_replace("/[\r\n]+/", '" + $0"', addslashes($value)) . '"';

		$out.= 'oFCKeditor.' . $key . ' = ' . $value . ';' . "\n";
	}

	foreach ($config_arguments as $key => $value) {
		if (!is_bool($value))
			$value = '"' . preg_replace("/[\r\n]+/", '" + $0"', addslashes($value)) . '"';

		$out.= 'oFCKeditor.Config[\'' . $key . '\'] = ' . $value . ';' . "\n";
	}

	$out.= 'oFCKeditor.Create();' . "\n";
	$out.= '//]]>' . "\n";
	$out.= '</script>' . "\n";
	$out.= '<noscript>' . "\n";
	$out.= '<textarea name="' . $base_arguments['InstanceName'] . '" id="' . substr($base_arguments['InstanceName'], 5, strlen($base_arguments['InstanceName']) - 6) . '" cols="50" rows="5">' . $base_arguments['Value'] . "</textarea>\n";
	$out.= '</noscript>' . "\n";

	return $out;
}
/* vim: set expandtab: */
?>