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

	foreach ($modules as $module) {
		$path = DESIGN_PATH . $module . '/style.css';
		if (is_file($path) && $module != '.' && $module != '..') {
			$styles['css'][] = $path;
		}
	}

	$styles['css'][] = DESIGN_PATH . 'jquery/jquery-ui.css';
	$styles['css'][] = DESIGN_PATH . 'jquery/jquery-colorbox.css';

	return $styles;
} elseif ($_GET['g'] == 'js') {
	$scripts = array();
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.js';
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.cookie.js';
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.ui.js';
	$scripts['js'][] = DESIGN_PATH . 'jquery/jquery.colorbox.js';
	$scripts['js'][] = DESIGN_PATH . 'script.js';

	return $scripts;
}