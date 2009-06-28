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
	$modules = scandir(DESIGN_PATH);
	$styles = array();
	$styles['css'][] = DESIGN_PATH . '/layout.css';
	$styles['css'][] = DESIGN_PATH . '/jquery-ui.css';

	foreach ($modules as $module) {
		if (is_dir(DESIGN_PATH . $module) && $module != '.' && $module != '..' && is_file(DESIGN_PATH . $module . '/style.css')) {
			$styles['css'][] = DESIGN_PATH . '/' . $module . '/style.css';
		}
	}
	return $styles;
}