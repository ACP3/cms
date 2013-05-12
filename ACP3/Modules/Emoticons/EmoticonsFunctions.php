<?php

/**
 * Emoticons
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Emoticons;

use ACP3\Core;

class EmoticonsFunctions {

	/**
	 * Cache die Emoticons
	 *
	 * @return boolean
	 */
	public static function setEmoticonsCache() {
		$emoticons = \ACP3\CMS::$injector['Db']->fetchAll('SELECT code, description, img FROM ' . DB_PRE . 'emoticons ORDER BY code DESC');
		$c_emoticons = count($emoticons);

		$data = array();
		for ($i = 0; $i < $c_emoticons; ++$i) {
			$picInfos = getimagesize(UPLOADS_DIR . 'emoticons/' . $emoticons[$i]['img']);
			$code = $emoticons[$i]['code'];
			$description = $emoticons[$i]['description'];
			$data[$code] = '<img src="' . ROOT_DIR . 'uploads/emoticons/' . $emoticons[$i]['img'] . '" width="' . $picInfos[0] . '" height="' . $picInfos[1] . '" alt="' . $description . '" title="' . $description . '" />';
		}

		return Core\Cache::create('list', $data, 'emoticons');
	}

	/**
	 * Bindet die gecacheten Emoticons ein
	 *
	 * @return array
	 */
	public static function getEmoticonsCache() {
		if (Core\Cache::check('list', 'emoticons') === false)
			self::setEmoticonsCache();

		return Core\Cache::output('list', 'emoticons');
	}

	/**
	 * Erzeugt eine Auflistung der Emoticons
	 *
	 * @param string $field_id
	 * 	Die ID des Eingabefeldes, in welches die Emoticons eingefügt werden sollen
	 * @return string
	 */
	public static function emoticonsList($field_id = 0) {
		static $emoticons = array();

		if (empty($emoticons))
			$emoticons = self::getEmoticonsCache();

		\ACP3\CMS::$injector['View']->assign('emoticons_field_id', empty($field_id) ? 'message' : $field_id);
		\ACP3\CMS::$injector['View']->assign('emoticons', $emoticons);
		return \ACP3\CMS::$injector['View']->fetchTemplate('emoticons/list.tpl');
	}

	/**
	 * Ersetzt bestimmte Zeichen durch Emoticons
	 *
	 * @param string $string
	 *  Zu durchsuchender Text nach Zeichen
	 * @return string
	 */
	public static function emoticonsReplace($string) {
		static $emoticons = array();

		if (empty($emoticons))
			$emoticons = self::getEmoticonsCache();

		return strtr($string, $emoticons);
	}

}