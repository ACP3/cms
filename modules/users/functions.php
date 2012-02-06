<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
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
function userNameExists($nickname, $id = 0)
{
	global $db;
	$nickname = $db->escape($nickname);
	$id = validate::isNumber($id) ? ' AND id != \'' . $id . '\'' : '';
	return !empty($nickname) && $db->countRows('*', 'users', 'nickname = \'' . $nickname . '\'' . $id) == 1 ? true : false;
}
/**
 * Überprüft, ob die übergebene E-Mail-Adresse schon existiert
 *
 * @param string $mail
 *  Die zu überprüfende E-Mail-Adresse
 * @return boolean
 */
function userEmailExists($mail, $id = 0)
{
	global $db;
	$id = validate::isNumber($id) ? ' AND id != \'' . $id . '\'' : '';
	return validate::email($mail) && $db->countRows('*', 'users', 'mail IN(\'' . $mail . ':1\', \'' . $mail . ':0\')' . $id) > 0 ? true : false;
}