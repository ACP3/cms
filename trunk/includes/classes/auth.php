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

			$user_check = $db->select('id, pwd', 'users', 'nickname = \'' . $db->escape($cookie_arr[0]) . '\'');
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
	 * @param string $fields
	 * 	Die zu selektierenden Benutzerdaten
	 * @param integer $user_id
	 * 	Der angeforderte Benutzer
	 * @return mixed
	 */
	public function getUserInfo($fields, $user_id = '')
	{
		if (empty($user_id) && $this->isUser) {
			$user_id = USER_ID;
		}
		if (preg_match('/\d/', $user_id)) {
			global $db;

			$info = $db->select($fields, 'users', 'id = \'' . $user_id . '\'');

			return count($info) == '1' ? $info[0] : false;
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
		return $this->isUser;
	}
}
?>