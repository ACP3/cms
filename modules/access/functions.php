<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit();

/**
 * Baut den String den zu erstellenden / verändernden Zugriffslevel zusammen
 *
 * @param array $modules
 * @return string
 */
function buildAccessLevel($modules)
{
	if (!empty($modules) && is_array($modules)) {
		$modules['errors'] = '2';
		ksort($modules);
		$access_level = '';

		foreach ($modules as $module => $level) {
			$access_level.= $module . ':' . $level . ',';
		}
		return substr($access_level, 0, -1);
	}
	return '';
}
/**
 * Überprüft, ob zumindest ein Module ausgewählt wurde
 *
 * @param array $modules
 * @return boolean
 */
function emptyCheck($modules) {
	if (!empty($modules) && is_array($modules)) {
		foreach ($modules as $key) {
			if (!empty($key)) {
				return false;
			}
		}
	}
	return true;
}
/**
 * Im Falle eines Fehlers im Formular, werden die ausgewählten Zugriffslevel selektiert
 *
 * @param string $dir
 * @param integer $value
 * @param integer $db_value
 * @return string
 */
function selectAccessLevel($dir, $value, $db_value = '')
{
	$selected = ' selected="selected"';
	if (isset($_POST['form']['modules'][$dir]) && $_POST['form']['modules'][$dir] == $value) {
		return $selected;
	} elseif ($db_value != '' && $db_value == $value) {
		return $selected;
	}
	return '';
}
?>