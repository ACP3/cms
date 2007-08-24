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
		// Session starten
		session_start();

		if (isset($_COOKIE['ACP3_AUTH'])) {
			global $db;

			$cookie = $db->escape($_COOKIE['ACP3_AUTH']);
			$cookie_arr = explode('|', $cookie);

			$user_check = $db->select('id, pwd, access', 'users', 'name = \'' . $cookie_arr[0] . '\'');
			if (count($user_check) == '1') {
				$db_password = substr($user_check[0]['pwd'], 0, 40);
				if ($db_password == $cookie_arr[1]) {
					$this->is_user = true;

					// Falls nötig, Session neu setzen
					if (empty($_SESSION['acp3_id']) || empty($_SESSION['acp3_access'])) {
						$_SESSION['acp3_id'] = $user_check[0]['id'];
						$_SESSION['acp3_access'] = $user_check[0]['access'];
					}
				}
			}
			if (!$this->is_user) {
				redirect('users/signoff');
			}
		// Zugriffslevel für Besucher setzen
		} else {
			$_SESSION['acp3_access'] = '2';
		}
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