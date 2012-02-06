<?php
/**
 * Validate
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Klasse zur Validierung von bestimmten Einträgen
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class validate
{
	/**
	 * Überprüft, ob die bergebenen Privilegien überhaupt existieren
	 * und plausible Werte enthalten
	 *
	 * @param array $privileges
	 *	Array mit den IDs der zu überprüfenden Privilegien mit ihren Berechtigungen
	 * @return boolean
	 */
	public static function aclPrivilegesExist(array $privileges)
	{
		$all_privs = acl::getAllPrivileges();
		$c_all_privs = count($all_privs);
		for ($i = 0; $i < $c_all_privs; ++$i) {
			$valid = false;
			foreach ($privileges as $module) {
				foreach ($module as $priv_id => $value) {
					if ($priv_id == $all_privs[$i]['id'] && $value >= 0 && $value <= 2)
						$valid = true;
				}
			}
		}
		return $valid;
	}
	/**
	 * Überprüft, ob die selektierten Rollen überhaupt existieren
	 *
	 * @param array $roles
	 *	Die zu überprüfenden Rollen
	 * @return boolean
	 */
	public static function aclRolesExist(array $roles)
	{
		$all_roles = acl::getAllRoles();
		$good = array();
		foreach ($all_roles as $row) {
			$good[] = $row['id'];
		}

		foreach ($roles as $row) {
			if (in_array($row, $good) === false)
				return false;
		}
		return true;
	}
	/**
	 * Überprüft einen Geburtstag auf seine Gültigkeit
	 *
	 * @param string $var
	 *  Das zu überprüfende Datum
	 * @param integer $format
	 * @return boolean
	 */
	public static function birthday($var, $format)
	{
		$regex = '/^(\d{4})-(\d{2})-(\d{2})$/';
		$matches = array();
		if (preg_match($regex, $var, $matches)) {
			if (checkdate($matches[2], $matches[3], $matches[1]) && ($format == 1 || $format == 2)) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Überpürft, ob der eingegebene Captcha mit dem generierten übereinstimmt
	 *
	 * @param string $input
	 * @return boolean
	 */
	public static function captcha($input)
	{
		global $session;

		return preg_match('/^[a-zA-Z0-9]+$/', $input) && $input === $session->get('captcha') ? true : false;
	}
	/**
	 * Überprüft, ob alle Daten ein sinnvolles Datum ergeben
	 *
	 * @param string $start
	 *  Startdatum
	 * @param string $end
	 *  Enddatum
 	 * @return boolean
	 */
	public static function date($start, $end = null)
	{
		$regex = '/^(\d{4})-(\d{2})-(\d{2})( ([01][0-9]|2[0-3]):([0-5][0-9])){0,1}$/';
		if (preg_match($regex, $start, $matches_start)) {
			// Wenn ein Enddatum festgelegt wurde, dieses ebenfalls mit überprüfen
			if ($end != null && preg_match($regex, $end, $matches_end)) {
				if (checkdate($matches_start[2], $matches_start[3], $matches_start[1]) &&
					checkdate($matches_end[2], $matches_end[3], $matches_end[1]) &&
					strtotime($start) <= strtotime($end)) {
					return true;
				}
			// Nur Startdatum überprüfen
			} else {
				if (checkdate($matches_start[2], $matches_start[3], $matches_start[1])) {
					return true;
				}
			}
		}
		return false;
	}
	public static function deleteEntries($entries)
	{
		return (bool) preg_match('/^((\d+)\|)*(\d+)$/', $entries);
	}
	/**
	 * Überprüft, ob eine Standardkonforme E-Mail-Adresse übergeben wurde
	 *
	 * @copyright HTML/QuickForm/Rule/Email.php
	 * 	Suchmuster von PEAR entnommen
	 * @param string $var
	 *  Zu überprüfende E-Mail-Adresse
	 * @return boolean
	 */
	public static function email($var)
	{
		$pattern = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

		return (bool) preg_match($pattern, $var);
	}
	/**
	 * Validiert das Formtoken auf seine Gültigkeit
	 *
	 * @return boolean
	 */
	public static function formToken()
	{
		global $session;

		return isset($_POST['security_token']) && $session->get('security_token') === $_POST['security_token'] ? true : false;
	}
	/**
	 * Bestimmung des Geschlechts
	 *  1 = Keine Angabe
	 *  2 = Weiblich
	 *  3 = Männlich
	 *
	 * @param string, integer $var
	 *  Die zu überprüfende Variable
	 * @return boolean
	 */
	public static function gender($var)
	{
		return $var == 1 || $var == 2 || $var == 3 ? true : false;
	}
	/**
	 * Überprüft, ob eine gültige ICQ-Nummer eingegeben wurde
	 *
	 * @param integer $var
	 * @return boolean
	 */
	public static function icq($var)
	{
		return (bool) preg_match('/^(\d{6,9})$/', $var);
	}
	/**
	 * Überprüft, ob die übergebene URI dem Format des ACP3 entspricht
	 *
	 * @param mixed $var
	 * @return boolean
	 */
	public static function isInternalURI($var)
	{
		return (bool) preg_match('/^([a-z\d_\-]+\/){2,}$/', $var);
	}
	/**
	 * Überprüft, ob ein gültiger MD5-Hash übergeben wurde
	 *
	 * @param string $string
	 * @return boolean
	 */
	public static function isMD5($string)
	{
		return is_string($string) === true && preg_match('/^[a-f\d]+$/', $string) && strlen($string) === 32 ? true : false;
 	}
	/**
	 * Überprüft eine Variable, ob diese nur aus Ziffern besteht
	 *
	 * @param mixed $var
	 * @return boolean
	 */
	public static function isNumber($var)
	{
		return (bool) preg_match('/^(\d+)$/', $var);
	}
	/**
	 * Überprüfen, ob es ein unterstütztes Bildformat ist
	 *
	 * @param string $file
	 *  Zu überprüfendes Bild
	 * @return boolean
	 */
	public static function isPicture($file, $width = '', $height = '', $filesize = '')
	{
		$info = getimagesize($file);
		$isPicture = $info[2] >= 1 && $info[2] <= 3 ? true : false;

		if ($isPicture === true) {
			$bool = true;
			// Optionale Parameter
			if (validate::isNumber($width) && $info[0] > $width)
				$bool = false;
			if (validate::isNumber($height) && $info[1] > $height)
				$bool = false;
			if (filesize($file) === 0 || validate::isNumber($filesize) && filesize($file) > $filesize)
				$bool = false;

			return $bool;
		}
		return false;
	}
	/**
	 *	Überprüft, ob der eingegebene URI-Alias sicher ist, d.h. es dürfen nur
	 *	die Kleinbuchstaben von a-z, Zahlen, der Bindestrich und das Slash eingegeben werden
	 *
	 * @param string $var
	 * @return boolean
	 */
	public static function isUriSafe($var)
	{
		return (bool) preg_match('/^([a-z]{1}[a-z\d\-]*(\/[a-z\d\-]+)*)$/', $var);
	}
	/**
	 * Gibt in Abhängigkeit des Parameters $mimetype entweder
	 * den gefundenen MIMETYPE aus oder ob der gefundene MIMETYPE
	 * mit dem erwarteten übereinstimmt
	 *
	 * @param string $file
	 *  Die zu überprüfende Datei
	 * @param string $mimetype
	 *  Der zu vergleichende MIMETYPE
	 * @return mixed
	 */
	public static function mimeType($file, $mimetype = '') {
		if (is_file($file) === true) {
			if (function_exists('finfo_open') === true && $fp = finfo_open(FILEINFO_MIME)) {
				$return = finfo_file($fp, $file);
				finfo_close($fp);
			} elseif (function_exists('mime_content_type') === true) {
				$return = mime_content_type($file);
			}

			if (!empty($mimetype)) {
				return $return == $mimetype ? true : false;
			} else {
				return $return;
			}
		}
	}
	/**
	 * Überprüft, ob ein URI-Alias bereits existiert
	 *
	 * @param string $alias
	 * @param string $path
	 * @return boolean
	 */
	public static function uriAliasExists($alias, $path = '')
	{
		if (self::isUriSafe($alias)) {
			if (is_dir(MODULES_DIR . $alias) === true) {
				return true;
			} else {
				global $db;

				$path.= !preg_match('/\/$/', $path) ? '/' : '';
				if ($path !== '/' && validate::isInternalURI($path) === true) {
					return $db->countRows('*', 'seo', 'alias = \'' . $alias . '\' AND uri != \'' . $path . '\'') > 0 ? true : false;
				} elseif ($db->countRows('*', 'seo', 'alias = \'' . $alias . '\'') > 0) {
					return true;
				}
			}
		}
		return false;
	}
}