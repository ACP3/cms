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
 *  Der überprüfende Nickname
 * @return boolean
 */
function userNameExists($nickname, $id = 0)
{
	global $db;
	$nickname = $db->escape($nickname);
	$id = validate::isNumber($id) ? ' AND id != \'' . $id . '\'' : '';
	return !empty($nickname) && $db->select('COUNT(id)', 'users', 'nickname = \'' . $nickname . '\'' . $id, 0, 0, 0, 1) == 1 ? true : false;
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
	return validate::email($mail) && $db->select('COUNT(id)', 'users', 'mail =\'' . $mail . '\'' . $id, 0, 0, 0, 1) == 1 ? true : false;
}
?>
