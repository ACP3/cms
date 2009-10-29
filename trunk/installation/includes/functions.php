<?php
/**
 * Diese Datei ist für die häufig verwendeten Funktionen im Installer zuständig
 *
 * @author Goratsch Webdesign
 * @package ACP3 Installer
 */
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
// Funktion zum Salzen von Passwörtern
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
// Selektion eines Eintrages in einem Dropdown-Menü
function select_entry($name, $value, $field_value = '', $attr = 'selected') {
	$attr = ' ' . $attr . '="' . $attr . '"';

	if (!isset($_POST['form'][$name])) {
		if (!is_array($field_value) && $field_value == $value) {
			return $attr;
		} elseif (is_array($field_value)) {
			foreach ($field_value as $row) {
				if ($row == $value)
					return $attr;
			}
		}
	} elseif (isset($_POST['form'][$name]) && $_POST['form'][$name] != '') {
		if (!is_array($_POST['form'][$name]) && $_POST['form'][$name] == $value) {
			return $attr;
		} elseif (is_array($_POST['form'][$name])) {
			foreach ($_POST['form'][$name] as $row) {
				if ($row == $value)
					return $attr;
			}
		}
	}
}
// URIs
function uri($uri)
{
	return PHP_SELF . '/' . $uri . (!preg_match('/\/$/', $uri) ? '/' : '') . 'lang_' . LANG . '/';
}
?>