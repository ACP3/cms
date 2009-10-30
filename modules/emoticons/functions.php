<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Cache die Emoticons
 *
 * @return boolean
 */
function setEmoticonsCache()
{
	global $db;
	return cache::create('emoticons', $db->select('code, description, img', 'emoticons', 0, 'code DESC'));
}
/**
 * Bindet die gecacheten Emoticons ein
 *
 * @return array
 */
function getEmoticonsCache()
{
	if (!cache::check('emoticons'))
		setEmoticonsCache();

	return cache::output('emoticons');
}
/**
 * Erzeugt eine Auflistung der Emoticons
 *
 * @param string $field_id
 * 	Die ID des Eingabefeldes, in welches die Emoticons eingefügt werden sollen
 * @return string
 */
function emoticonsList($field_id = 0)
{
	global $db, $tpl;

	$emoticons = getEmoticonsCache();
	$c_emoticons = count($emoticons);

	for ($i = 0; $i < $c_emoticons; ++$i) {
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

	$emoticons = getEmoticonsCache();

	foreach ($emoticons as $row) {
		$string = str_replace($row['code'], '<img src="' . ROOT_DIR . 'uploads/emoticons/' . $row['img'] . '" alt="' . $row['description'] . '" title="' . $row['description'] . '" />', $string);
	}
	return $string;
}
