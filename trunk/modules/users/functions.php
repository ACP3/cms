<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Überprüft, ob der übergebene Username schon existiert
 *
 * @param string $nickname
 *  Der zu überprüfende Nickname
 * @return boolean
 */
function userNameExists($nickname, $id = '')
{
	if (ACP3_Validate::isNumber($id) === true) {
		return !empty($nickname) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id != ? AND nickname = ?', array($id, $nickname)) == 1 ? true : false;
	} else {
		return !empty($nickname) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE nickname = ?', array($nickname)) == 1 ? true : false;
	}
}
/**
 * Überprüft, ob die übergebene E-Mail-Adresse schon existiert
 *
 * @param string $mail
 *  Die zu überprüfende E-Mail-Adresse
 * @return boolean
 */
function userEmailExists($mail, $id = '')
{
	if (ACP3_Validate::isNumber($id) === true) {
		return ACP3_Validate::email($mail) === true && ACP3_CMS::$db2->executeQuery('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id != ? AND mail = ?', array($id, $mail), array(\PDO::PARAM_INT, \PDO::PARAM_STR))->fetch(PDO::FETCH_COLUMN) > 0 ? true : false;
	} else {
		return ACP3_Validate::email($mail) === true && ACP3_CMS::$db2->executeQuery('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE mail = ?', array($mail), array(\PDO::PARAM_STR))->fetch(PDO::FETCH_COLUMN) > 0 ? true : false;
	}
}