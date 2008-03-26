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
function emoticonsList($field_id = 0)
{
	global $cache, $db, $tpl;

	if (!$cache->check('emoticons')) {
		$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));
	}
	$emoticons = $cache->output('emoticons');
	$c_emoticons = count($emoticons);

	for ($i = 0; $i < $c_emoticons; $i++) {
		$emoticons[$i]['img'] = ROOT_DIR . 'uploads/emoticons/' . $emoticons[$i]['img'];
	}

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
function emoticonsReplace($string)
{
	global $cache, $db;
	static $emoticons = array();

	if (!$cache->check('emoticons')) {
		$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));
	}
	$emoticons = $cache->output('emoticons');

	foreach ($emoticons as $row) {
		$string = str_replace($row['code'], '<img src="' . ROOT_DIR . 'uploads/emoticons/' . $row['img'] . '" alt="' . $row['description'] . '" title="' . $row['description'] . '" />', $string);
	}
	return $string;
}
?>