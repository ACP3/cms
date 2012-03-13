<?php
/**
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Autoloading für die ACP3 eigenen Klassen
 *
 * @param string $class
 *  Der Name der zu ladenden Klasse
 */
function acp3_load_class($class)
{
	if (strpos($class, 'ACP3_') === 0) {
		$file = dirname(__FILE__) . '/classes/' . str_replace('ACP3_', '', $class) . '.class.php';
		if (is_file($file) === true)
			require $file;
	}
}
spl_autoload_register("acp3_load_class");