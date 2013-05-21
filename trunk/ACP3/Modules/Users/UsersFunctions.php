<?php

/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Users;

use ACP3\Core;

abstract class UsersFunctions {

	/**
	 * Überprüft, ob der übergebene Username schon existiert
	 *
	 * @param string $nickname
	 *  Der zu überprüfende Nickname
	 * @return boolean
	 */
	public static function userNameExists($nickname, $id = '')
	{
		if (Core\Validate::isNumber($id) === true) {
			return !empty($nickname) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id != ? AND nickname = ?', array($id, $nickname)) == 1 ? true : false;
		} else {
			return !empty($nickname) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE nickname = ?', array($nickname)) == 1 ? true : false;
		}
	}

	/**
	 * Überprüft, ob die übergebene E-Mail-Adresse schon existiert
	 *
	 * @param string $mail
	 *  Die zu überprüfende E-Mail-Adresse
	 * @return boolean
	 */
	public static function userEmailExists($mail, $id = '')
	{
		if (Core\Validate::isNumber($id) === true) {
			return Core\Validate::email($mail) === true && Core\Registry::get('Db')->executeQuery('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id != ? AND mail = ?', array($id, $mail), array(\PDO::PARAM_INT, \PDO::PARAM_STR))->fetch(\PDO::FETCH_COLUMN) > 0 ? true : false;
		} else {
			return Core\Validate::email($mail) === true && Core\Registry::get('Db')->executeQuery('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE mail = ?', array($mail), array(\PDO::PARAM_STR))->fetch(\PDO::FETCH_COLUMN) > 0 ? true : false;
		}
	}

}
