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
	static $base_arguments = array(), $config_arguments = array();

	// Test if editor has been loaded before
	$init = count($base_arguments) ? true : false;

	$base_arguments['BasePath'] = ROOT_DIR . 'includes/wysiwyg/fckeditor/';

	$base_arguments['name'] = $params['name'];

	if (isset($params['value']))
		$base_arguments['Value'] = $params['value'];
	if (isset($params['height']))
		$base_arguments['Height'] = $params['height'] . 'px';
	if (isset($params['toolbar']))
		$base_arguments['ToolbarSet'] = $params['toolbar'] == 'simple' ? 'Basic' : 'Default';

	// Use all other parameters for the config array (replace if needed)
	$other_arguments = array_diff_assoc($params, $base_arguments);
	$config_arguments = array_merge($config_arguments, $other_arguments);

	$out = '';

	if (!$init)
		$out.= '<script type="text/javascript" src="' . $base_arguments['BasePath'] . 'fckeditor.js"></script>' . "\n";

	$out.= '<script type="text/javascript">' . "\n";
	$out.= '//<![CDATA[' . "\n";
	$out.= 'var oFCKeditor = new FCKeditor(\'' . $base_arguments['name'] . '\');' . "\n";

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
	$out.= '<textarea name="' . $base_arguments['name'] . '" id="' . substr($base_arguments['name'], 5, -1) . '" cols="50" rows="5">' . (!empty($base_arguments['value']) ? $base_arguments['value'] : '') . "</textarea>\n";
	$out.= '</noscript>' . "\n";

	return $out;
	
}
?>