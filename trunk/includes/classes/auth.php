<?php
/**
 * Authentification
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Authentifiziert den Benutzer
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class auth
{
	/**
	 * User oder nicht
	 *
	 * @var boolean
	 */
	private $isUser = false;

	/**
	 * Findet heraus, falls der ACP3_AUTH Cookie gesetzt ist, ob der Seitenbesucher auch wirklich ein registrierter Benutzer des ACP3 ist
	 */
	function __construct()
	{
		if (isset($_COOKIE['ACP3_AUTH'])) {
			global $db;

			$cookie = base64_decode($_COOKIE['ACP3_AUTH']);
			$cookie_arr = explode('|', $cookie);

			$user_check = $db->select('id, pwd', 'users', 'nickname = \'' . $db->escape($cookie_arr[0]) . '\' AND login_errors < 3');
			if (count($user_check) == 1) {
				$db_password = substr($user_check[0]['pwd'], 0, 40);
				if ($db_password == $cookie_arr[1]) {
					$this->isUser = true;
					define('USER_ID', $user_check[0]['id']);
				}
			}
			if (!$this->isUser) {
				setcookie('ACP3_AUTH', '', time() - 3600, '/');

				redirect(0, ROOT_DIR);
			}
		}
	}
	/**
	 * Gibt ein Array mit den angeforderten Daten eines Benutzers zurück
	 *
	 * @param integer $user_id
	 * 	Der angeforderte Benutzer
	 * @return mixed
	 */
	public function getUserInfo($user_id = '')
	{
		if (empty($user_id) && $this->isUser()) {
			$user_id = USER_ID;
		}
		if (validate::isNumber($user_id)) {
			global $db;
			static $user_info = array();

			if (empty($user_info[$user_id])) {
				$info = $db->select('nickname, realname, access, mail, website, time_zone, dst, language, draft', 'users', 'id = \'' . $user_id . '\'');
				$user_info[$user_id] = $info[0];
			}

			return !empty($user_info[$user_id]) ? $user_info[$user_id] : false;
		}
		return false;
	}
	/**
	 * Gibt den Status von $isUser zurück
	 *
	 * @return boolean
	 */
	public function isUser()
	{
		return $this->isUser && defined('USER_ID') && validate::isNumber(USER_ID) ? true : false;
	}
	/**
	 * Setzt den internen Authentifizierungscookie
	 *
	 * @param string $nickname
	 *  Der Loginname des Users
	 * @param string $password
	 *  Die Hashsumme des Passwortes
	 * @param integer $expiry
	 *  Zeit in Sekunden, bis der Cookie seine Gültigkeit verliert
	 */
	public function setCookie($nickname, $password, $expiry)
	{
		setcookie('ACP3_AUTH', base64_encode($nickname . '|' . $password), time() + $expiry, '/');
	}
}
?>