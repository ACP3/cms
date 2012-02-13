<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/

define('ACP3_ROOT', realpath(dirname(__FILE__) . '/../../') . '/');

require_once ACP3_ROOT . 'includes/config.php';

define('DESIGN_PATH', ACP3_ROOT . 'designs/' . CONFIG_DESIGN . '/');

if ($_GET['g'] === 'css' || $_GET['g'] === 'css_simple') {
	define('IN_ACP3', true);
	define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
	$php_self = dirname(PHP_SELF);
	define('ROOT_DIR', $php_self != '/' ? $php_self . '/' : '/');
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

	$session = new session();
	$auth = new auth();
	$lang = new lang();

	$key = $_GET['g'];

	$styles = array();
	$styles[$key][] = DESIGN_PATH . ($key === 'css' ? 'layout.css' : 'simple.css');

	$modules = scandir(DESIGN_PATH);
	foreach ($modules as $module) {
		$path = DESIGN_PATH . $module . '/style.css';
		if ($module !== '.' && $module !== '..' && is_file($path) === true && modules::isActive($module) === true)
			$styles[$key][] = $path;
	}

	$styles[$key][] = DESIGN_PATH . 'jquery/jquery-ui.css';
	$styles[$key][] = DESIGN_PATH . 'jquery/jquery-fancybox.css';

	return $styles;
} elseif ($_GET['g'] === 'js') {
	$scripts = array();
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.min.js';
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.cookie.js';
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.ui.min.js';
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.fancybox.js';
	$scripts['js'][] = DESIGN_PATH . 'script.js';

	return $scripts;
}