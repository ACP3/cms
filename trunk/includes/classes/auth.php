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
	private $is_user = false;

	/**
	 * Findet heraus, falls der ACP3_AUTH Cookie gesetzt ist, ob der Seitenbesucher auch wirklich ein registrierter Benutzer des ACP3 ist
	 */
	function __construct()
	{
		// Session Einstellungen setzen und Session starten
		session_set_cookie_params(0, ROOT_DIR, htmlentities($_SERVER['HTTP_HOST']));
		session_start();

		if (isset($_COOKIE['ACP3_AUTH'])) {
			global $db;

			$cookie = $db->escape($_COOKIE['ACP3_AUTH']);
			$cookie_arr = explode('|', $cookie);

			$user_check = $db->select('id, pwd', 'users', 'nickname = \'' . $cookie_arr[0] . '\'');
			if (count($user_check) == '1') {
				$db_password = substr($user_check[0]['pwd'], 0, 40);
				if ($db_password == $cookie_arr[1]) {
					$this->is_user = true;

					// Falls nötig, Session neu setzen
					if (empty($_SESSION['acp3_id'])) {
						$_SESSION['acp3_id'] = $user_check[0]['id'];
					}
				}
			}
			if (!$this->is_user) {
				setcookie('ACP3_AUTH', '', time() - 3600, ROOT_DIR);
				$_SESSION = array();
				if (isset($_COOKIE[session_name()]))
					setcookie(session_name(), '', time() - 3600, ROOT_DIR);
				session_destroy();
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
	function getUserInfo($fields, $user_id = 0)
	{
		if (empty($user_id) && $this->is_user) {
			$user_id = $_SESSION['acp3_id'];
		}
		if (preg_match('/\d/', $user_id)) {
			global $db;

			$info = $db->select($fields, 'users', 'id = \'' . $user_id . '\'');

			return count($info) == '1' ? $info[0] : false;
		}
		return false;
	}
	/**
	 * Gibt den Status von $is_user zurück
	 *
	 * @return boolean
	 */
	function is_user()
	{
		return $this->is_user;
	}
}
?>