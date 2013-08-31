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
define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../../') . '/');

require_once ACP3_ROOT_DIR . 'ACP3/Application.php';

\ACP3\Application::defineDirConstants();
\ACP3\Application::startupChecks();
\ACP3\Application::includeAutoLoader();
\ACP3\Application::initializeClasses();

// Cache-Lebenszeit setzen
$min_serveOptions['maxAge'] = CONFIG_CACHE_MINIFY;

$libraries = !empty($_GET['libraries']) ? explode(',', $_GET['libraries']) : array();
$layout = isset($_GET['layout']) && !preg_match('=/=', $_GET['layout']) ? $_GET['layout'] : 'layout';

if ($_GET['g'] === 'css') {
	return array('css' => \ACP3\Core\View::includeCssFiles($libraries, $layout));
} elseif ($_GET['g'] === 'js') {
	return array('js' =>  \ACP3\Core\View::includeJsFiles($libraries, $layout));
}