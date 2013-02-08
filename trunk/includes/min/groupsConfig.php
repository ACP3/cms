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

define('IN_ACP3', true);
define('ACP3_ROOT', realpath(__DIR__ . '/../../') . '/');

require_once ACP3_ROOT . 'includes/bootstrap.php';

ACP3_CMS::defineDirConstants();
ACP3_CMS::includeAutoLoader();
ACP3_CMS::initializeDoctrineDBAL();
ACP3_CMS::initializeClasses();

// Cache-Lebenszeit setzen
$min_serveOptions['maxAge'] = CONFIG_CACHE_MINIFY;

$libraries = !empty($_GET['libraries']) ? explode(',', $_GET['libraries']) : array();
$layout = isset($_GET['layout']) && !preg_match('=/=', $_GET['layout']) ? $_GET['layout'] : 'layout';

if ($_GET['g'] === 'css') {
	$design_info = ACP3_XML::parseXmlFile(DESIGN_PATH_INTERNAL . 'info.xml', '/design/responsive_layouts');

	$styles = array();
	$styles['css'][] = LIBRARIES_DIR . 'bootstrap/css/bootstrap.css';
	// Styles für das Responsive Design nur einbinden,
	// falls dies vom Design benötigt wird
	if (isset($design_info['layout']) &&
		($design_info['layout'] === $layout || (is_array($design_info['layout']) === true && in_array($layout, $design_info['layout']) === true)))
		$styles['css'][] = LIBRARIES_DIR . 'bootstrap/css/bootstrap-responsive.css';

	// Stylesheets der Bibliotheken zuerst laden,
	// damit deren Styles überschrieben werden können
	if (in_array('jquery-ui', $libraries))
		$styles['css'][] = LIBRARIES_DIR . 'js/jquery-ui.css';
	if (in_array('timepicker', $libraries))
		$styles['css'][] = LIBRARIES_DIR . 'js/jquery-timepicker.css';
	if (in_array('fancybox', $libraries))
		$styles['css'][] = LIBRARIES_DIR . 'js/jquery-fancybox.css';
	if (in_array('datatables', $libraries))
		$styles['css'][] = LIBRARIES_DIR . 'js/jquery-datatables.css';

	// Stylesheet für das Layout-Tenplate
	$styles['css'][] = DESIGN_PATH_INTERNAL . (is_file(DESIGN_PATH_INTERNAL . $layout . '.css') === true ? $layout : 'layout') . '.css';
	$styles['css'][] = DESIGN_PATH_INTERNAL . 'common.css';

	// Zusätzliche Stylesheets einbinden
	$extra_css = explode(',', CONFIG_EXTRA_CSS);
	if (count($extra_css) > 0) {
		foreach ($extra_css as $file) {
			$path = DESIGN_PATH_INTERNAL . 'css/' . trim($file);
			if (is_file($path) && in_array($path, $styles['css'])) {
				$styles['css'][] = $path;
			}
		}
	}

	// Stylesheets der Module
	$modules = ACP3_Modules::getActiveModules();
	foreach ($modules as $module) {
		$path_design = DESIGN_PATH_INTERNAL . $module['dir'] . '/style.css';
		$path_module = MODULES_DIR . $module['dir'] . '/templates/style.css';
		if (is_file($path_design) === true) {
			$styles['css'][] = $path_design;
		} elseif (is_file($path_module) === true) {
			$styles['css'][] = $path_module;
		}
	}

	return $styles;
} elseif ($_GET['g'] === 'js') {
	$scripts = array();
	$scripts['js'][] = LIBRARIES_DIR . 'js/jquery.min.js';
	$scripts['js'][] = LIBRARIES_DIR . 'bootstrap/js/bootstrap.min.js';
	if (in_array('bootbox', $libraries))
		$scripts['js'][] = LIBRARIES_DIR . 'js/bootbox.min.js';
	if (in_array('jquery-ui', $libraries))
		$scripts['js'][] = LIBRARIES_DIR . 'js/jquery.ui.min.js';
	if (in_array('timepicker', $libraries))
		$scripts['js'][] = LIBRARIES_DIR . 'js/jquery.timepicker.js';
	if (in_array('fancybox', $libraries))
		$scripts['js'][] = LIBRARIES_DIR . 'js/jquery.fancybox.min.js';
	if (in_array('datatables', $libraries))
		$scripts['js'][] = LIBRARIES_DIR . 'js/jquery.datatables.min.js';

	if (is_file(DESIGN_PATH_INTERNAL . $layout . '.js') === true)
		$scripts['js'][] = DESIGN_PATH_INTERNAL . $layout . '.js';

	// Zusätzliche JavaScript Dateien einbinden
	$extra_js = explode(',', CONFIG_EXTRA_JS);
	if (count($extra_js) > 0) {
		foreach ($extra_js as $file) {
			$path = DESIGN_PATH_INTERNAL . 'js/' . trim($file);
			if (is_file($path) && in_array($path, $styles['js'])) {
				$styles['js'][] = $path;
			}
		}
	}

	return $scripts;
}