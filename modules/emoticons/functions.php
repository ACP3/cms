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

	$emoticons = $db->select('code, description, img', 'emoticons', 0, 'code DESC');
	$c_emoticons = count($emoticons);

	for ($i = 0; $i < $c_emoticons; ++$i) {
		$picInfos = getimagesize(ACP3_ROOT . 'uploads/emoticons/' . $emoticons[$i]['img']);
		$emoticons[$i]['width'] = $picInfos[0];
		$emoticons[$i]['height'] = $picInfos[1];
	}

	return cache::create('emoticons', $emoticons);
}
/**
 * Bindet die gecacheten Emoticons ein
 *
 * @return array
 */
function getEmoticonsCache()
{
	if (cache::check('emoticons') === false)
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
		$emoticons[$i]['code'] = $db->escape($emoticons[$i]['code'], 3);
		$emoticons[$i]['description'] = $db->escape($emoticons[$i]['description'], 3);
		$emoticons[$i]['img'] = ROOT_DIR . 'uploads/emoticons/' . $emoticons[$i]['img'];
	}

	$tpl->assign('emoticons_field_id', empty($field_id) ? 'message' : $field_id);
	$tpl->assign('emoticons', $emoticons);
	return view::fetchTemplate('emoticons/list.tpl');
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
	static $emoticons = array();

	if (empty($emoticons)) {
		$cache = getEmoticonsCache();
		foreach($cache as $row) {
			$emoticons[$row['code']] = '<img src="' . ROOT_DIR . 'uploads/emoticons/' . $row['img'] . '" width="' . $row['width'] . '" height="' . $row['height'] . '" alt="' . $row['description'] . '" title="' . $row['description'] . '" />';
		}
	}

	return strtr($string, $emoticons);
}