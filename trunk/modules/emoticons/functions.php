<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Erzeugt eine Auflistung der Emoticons
 *
 * @param string $field_id
 * 	Die ID des Eingabefeldes, in welches die Emoticons eingefügt werden sollen
 * @return string
 */
function emoticons_list($field_id = 0)
{
	global $cache, $db, $tpl;

	if (!$cache->check('emoticons')) {
		$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));
	}
	$emoticons = $cache->output('emoticons');

	$tpl->assign('emoticons_field_id', empty($field_id) ? 'message' : $field_id);
	$tpl->assign('emoticons', $emoticons);
	return $tpl->fetch('emoticons/list.html');
}
/**
 * Ersetzt bestimmte Zeichen durch Emoticons
 *
 * @param string $string
 *  Zu durchsuchender Text nach Zeichen
 * @return string
 */
function emoticons_replace($string)
{
	global $cache, $db;
	static $emoticons = array();

	if (!$cache->check('emoticons')) {
		$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));
	}
	$emoticons = $cache->output('emoticons');

	foreach ($emoticons as $row) {
		$string = str_replace($row['code'] . ' ', '<img src="' . ROOT_DIR . 'files/emoticons/' . $row['img'] . '" alt="' . $row['description'] . '" title="' . $row['description'] . '" />', $string);
	}
	return $string;
}
?>