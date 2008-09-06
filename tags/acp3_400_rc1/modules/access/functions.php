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
 * @param array $uri
 * @return string
 */
function buildAccessLevel($uri)
{
	if (!empty($uri) && is_array($uri)) {
		$uri['errors'] = '2';
		ksort($uri);
		$access_level = '';

		foreach ($uri as $module => $level) {
			$access_level.= $module . ':' . $level . ',';
		}
		return substr($access_level, 0, -1);
	}
	return '';
}
/**
 * Überprüft, ob zumindest ein Module ausgewählt wurde
 *
 * @param array $uri
 * @return boolean
 */
function emptyCheck($uri) {
	if (!empty($uri) && is_array($uri)) {
		foreach ($uri as $key) {
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