<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/
require_once '../config.php';

define('DESIGN_PATH', dirname(__FILE__) . '/../../designs/' . CONFIG_DESIGN . '/');

if ($_GET['g'] == 'css') {
	define('IN_ACP3', true);
	define('ACP3_ROOT', '../../');
	define('MODULES_DIR', ACP3_ROOT . 'modules/');

	set_include_path(get_include_path() . PATH_SEPARATOR . ACP3_ROOT . 'includes/classes/');
	spl_autoload_extensions('.class.php');
	spl_autoload_register();

	// Klassen initialisieren
	$db = new db();
	$handle = $db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_PRE);
	if ($handle !== true) {
		exit($handle);
	}

	$auth = new auth();
	$acl = new acl();
	$lang = new lang();

	$modules = scandir(DESIGN_PATH);
	$styles = array();
	$styles['css'][] = DESIGN_PATH . 'layout.css';

	foreach ($modules as $module) {
		$path = DESIGN_PATH . $module . '/style.css';
		if (is_file($path) && $module != '.' && $module != '..' && modules::isActive($module)) {
			$styles['css'][] = $path;
		}
	}

	$styles['css'][] = DESIGN_PATH . 'jquery/jquery-ui.css';
	$styles['css'][] = DESIGN_PATH . 'jquery/jquery-colorbox.css';

	return $styles;
} elseif ($_GET['g'] == 'js') {
	$scripts = array();
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.min.js';
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.cookie.js';
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.ui.min.js';
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.colorbox.min.js';
	$scripts['js'][] = DESIGN_PATH . 'script.js';

	return $scripts;
}