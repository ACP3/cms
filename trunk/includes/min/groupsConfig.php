<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 **/

define('ACP3_ROOT', realpath(__DIR__ . '/../../') . '/');

define('IN_ACP3', true);
define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
$php_self = dirname(PHP_SELF);
define('ROOT_DIR', $php_self != '/' ? $php_self . '/' : '/');
define('MODULES_DIR', ACP3_ROOT . 'modules/');
define('INCLUDES_DIR', ACP3_ROOT . 'includes/');

require_once INCLUDES_DIR . 'config.php';
require_once INCLUDES_DIR . 'autoload.php';

$db = new ACP3_DB();
$handle = $db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_PRE);
if ($handle !== true)
	exit($handle);

ACP3_Config::getSystemSettings();
define('DESIGN_PATH', ACP3_ROOT . 'designs/' . CONFIG_DESIGN . '/');

// Cache-Lebenszeit setzen
$min_serveOptions['maxAge'] = CONFIG_CACHE_MINIFY;

$libraries = !empty($_GET['libraries']) ? explode(',', $_GET['libraries']) : array();

if ($_GET['g'] === 'css') {
	// Klassen initialisieren
	$session = new ACP3_Session();
	$auth = new ACP3_Auth();
	$lang = new ACP3_Lang();

	$layout = isset($_GET['layout']) && !preg_match('=/=', $_GET['layout']) && is_file(DESIGN_PATH . 'css/' . $_GET['layout'] . '.css') === true ? $_GET['layout'] : 'layout';

	$styles = array();
	$styles['css'][] = DESIGN_PATH . 'css/bootstrap.css';
	$styles['css'][] = DESIGN_PATH . 'css/bootstrap-responsive.css';
	$styles['css'][] = DESIGN_PATH . 'css/' . $layout . '.css';
	$styles['css'][] = DESIGN_PATH . 'css/common.css';

	$modules = scandir(DESIGN_PATH . 'css/');
	foreach ($modules as $module) {
		$module = substr($module, 0, -4);
		$path = DESIGN_PATH . 'css/' . $module . '.css';
		if ($module !== '.' && $module !== '..' && is_file($path) === true && ACP3_Modules::isActive($module) === true)
			$styles['css'][] = $path;
	}

	if (in_array('jquery-ui', $libraries))
		$styles['css'][] = DESIGN_PATH . 'css/jquery-ui.css';
	if (in_array('timepicker', $libraries))
		$styles['css'][] = DESIGN_PATH . 'css/jquery-timepicker.css';
	if (in_array('fancybox', $libraries))
		$styles['css'][] = DESIGN_PATH . 'css/jquery-fancybox.css';

	return $styles;
} elseif ($_GET['g'] === 'js') {
	$scripts = array();
	$scripts['js'][] = DESIGN_PATH . 'js/jquery.min.js';
	$scripts['js'][] = DESIGN_PATH . 'js/bootstrap.min.js';
	if (in_array('bootbox', $libraries))
		$scripts['js'][] = DESIGN_PATH . 'js/bootbox.min.js';
	if (in_array('jquery-ui', $libraries))
		$scripts['js'][] = DESIGN_PATH . 'js/jquery.ui.min.js';
	if (in_array('timepicker', $libraries))
		$scripts['js'][] = DESIGN_PATH . 'js/jquery.timepicker.js';
	if (in_array('fancybox', $libraries))
		$scripts['js'][] = DESIGN_PATH . 'js/jquery.fancybox.js';

	$layout = isset($_GET['layout']) && !preg_match('=/=', $_GET['layout']) ? $_GET['layout'] : 'layout';
	if (is_file(DESIGN_PATH . 'js/' . $layout . '.js') === true)
		$scripts['js'][] = DESIGN_PATH . 'js/' . $layout . '.js';

	return $scripts;
}