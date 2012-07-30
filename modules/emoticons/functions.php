<?php
/**
 * Emoticons
 *
 * @author Tino Goratsch
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

	$data = array();
	for ($i = 0; $i < $c_emoticons; ++$i) {
		$picInfos = getimagesize(ACP3_ROOT . 'uploads/emoticons/' . $emoticons[$i]['img']);
		$code = $db->escape($emoticons[$i]['code'], 3);
		$description = $db->escape($emoticons[$i]['description'], 3);
		$data[$code] = '<img src="' . ROOT_DIR . 'uploads/emoticons/' . $emoticons[$i]['img'] . '" width="' . $picInfos[0] . '" height="' . $picInfos[1] . '" alt="' . $description . '" title="' . $description . '" />';
	}

	return ACP3_Cache::create('emoticons', $data);
}
/**
 * Bindet die gecacheten Emoticons ein
 *
 * @return array
 */
function getEmoticonsCache()
{
	if (ACP3_Cache::check('emoticons') === false)
		setEmoticonsCache();

	return ACP3_Cache::output('emoticons');
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
	static $emoticons = array();

	if (empty($emoticons))
		$emoticons = getEmoticonsCache();

	$tpl->assign('emoticons_field_id', empty($field_id) ? 'message' : $field_id);
	$tpl->assign('emoticons', $emoticons);
	return ACP3_View::fetchTemplate('emoticons/list.tpl');
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

	if (empty($emoticons))
		$emoticons = getEmoticonsCache();

	return strtr($string, $emoticons);
}