<?php
/**
 * Diese Datei ist für die häufig verwendeten Funktionen im Installer zuständig
 *
 * @author Goratsch Webdesign
 * @package ACP3 Installer
 */

if (defined('IN_INSTALL') === false)
	exit;

/**
 * Generiert ein gesalzenes Passwort
 *
 * @param string $salt
 * @param string $plaintext
 * @param string $algorithm
 * @return string
 */
function genSaltedPassword($salt, $plaintext, $algorithm = 'sha1')
{
	return hash($algorithm, $salt . hash($algorithm, $plaintext));
}
/**
 * Generiert einen Zufallsstring beliebiger Länge
 *
 * @param integer $str_length
 *  Länge des zufälligen Strings
 * @return string
 */
function salt($str_length)
{
	$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$c_chars = strlen($chars) - 1;
	$key = '';
	for ($i = 0; $i < $str_length; ++$i) {
		$key.= $chars[mt_rand(0, $c_chars)];
	}
	return $key;
}
/**
 * Selektion eines Eintrages in einem Dropdown-Menü
 *
 * @param string $name
 *  Name des Feldes im Formular
 * @param mixed $defValue
 *  Abzugleichender Parameter mit $currentvalue
 * @param mixed $currentValue
 *  Wert aus der SQL Tabelle
 * @param string $attr
 *  HTML-Attribut, um Eintrag zu selektieren
 * @return string
 */
function selectEntry($name, $defValue, $currentValue = '', $attr = 'selected')
{
	$attr = ' ' . $attr . '="' . $attr . '"';

	if (isset($_POST[$name])) {
		$field = $_POST[$name];
	} elseif (isset($_POST['form'][$name])) {
		$field = $_POST['form'][$name];
	}

	if (isset($field)) {
		if (!is_array($field) && $field == $defValue) {
			return $attr;
		} elseif (is_array($field)) {
			foreach ($field as $row) {
				if ($row == $defValue)
					return $attr;
			}
		}
	} else {
		if (!is_array($currentValue) && $currentValue == $defValue) {
			return $attr;
		} elseif (is_array($currentValue)) {
			foreach ($currentValue as $row) {
				if ($row == $defValue)
					return $attr;
			}
		}
		return '';
	}
}
/**
 * Schreibt die Systemkonfigurationsdatei
 *
 * @param array $data
 * @return boolean
 */
function writeConfigFile(array $data)
{
	$path = ACP3_ROOT . 'includes/config.php';
	if (is_writable($path)){
		// Konfigurationsdatei in ein Array schreiben
		ksort($data);

		$content = "<?php\n";
		$content.= "define('INSTALLED', true);\n";
		if (defined('DEBUG')) {
			$content.= "define('DEBUG', " . ((bool) DEBUG) . ");\n";
		}
		$pattern = "define('CONFIG_%s', '%s');\n";
		foreach ($data as $key => $value) {
			$content.= sprintf($pattern, strtoupper($key), $value);
		}
		$content.= '?>';
		$bool = @file_put_contents($path, $content, LOCK_EX);
		return $bool ? true : false;
	}
	return false;
}
?>