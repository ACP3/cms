<?php
/**
 * Diese Datei ist für die häufig verwendeten Funktionen im ACP3 zuständig
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Erzeugt das Captchafeld für das Template
 *
 * @param integer $captcha_length
 * @return string
 */
function captcha($captcha_length = 5)
{
	global $tpl;
	
	$captcha['hash'] = md5(uniqid(rand(), true));
	$captcha['length'] = $captcha_length;
	$tpl->assign('captcha', $captcha);
	return $tpl->fetch('common/captcha.html');
}
/**
 * Gibt je nach angegeben Parametern eine Fehlerbox oder eine Bestätigungsbox aus
 *
 * @param string $text
 *  Zu übergebender Text
 * @param string $forward
 *  Weiter Hyperlink
 * @param string $back
 *  Zurück Hyperlink
 * @return string
 */
function comboBox($text, $forward = 0, $back = 0)
{
	global $tpl;

	if (is_array($text) && empty($forward) && empty($back)) {
		$tpl->assign('text', $text);
		return $tpl->fetch('common/error.html');
	} elseif (!empty($text) && (!empty($forward) || !empty($back))) {
		$tpl->assign('text', $text);
		$tpl->assign('forward', $forward);
		if (!empty($back)) {
			$tpl->assign('back', $back);
		}

		return $tpl->fetch('common/combo.html');
	}
	return '';
}
/**
 * Ermittelt die Dateigröße, gemäß IEC 60027-2
 *
 * @param integer $value
 * 	Die Dateigröße in Byte
 * @return string
 * 	Die Dateigröße als Fließkommazahl, mit der dazugehörigen Einheit
 */
function calcFilesize($value)
{
	$units = array(
		0 => 'Byte',
		1 => 'KiB',
		2 => 'MiB',
		3 => 'GiB',
		4 => 'TiB',
	);

	for ($i = 0; $value >= 1024; ++$i) {
		$value = $value / 1024;
	}
	return round($value, 3) . ' ' . $units[$i];
}
/**
 * Hochgeladene Dateien verschieben und umbenennen
 *
 * @param string $tmp_filename
 *  Temporäre Datei
 * @param string $filename
 *  Dateiname
 * @param string $dir
 *  Ordner, in den die Datei verschoben werden soll
 * @return array
 *  Gibt ein Array mit dem Namen und der Größe der neuen Datei aus
 */
function moveFile($tmp_filename, $filename, $dir)
{
	$path = ACP3_ROOT . 'uploads/' . $dir . '/';
	$ext = strrchr($filename, '.');
	
	$new_name = 1;
	while (is_file($path . $new_name . $ext)) {
		$new_name++;
	}
	if (is_writable($path)) {
		if (!@move_uploaded_file($tmp_filename, $path . $new_name . $ext)) {
			global $lang;

			echo sprintf($lang->t('common', 'upload_error'), $filename);
		} else {
			$new_file['name'] = $new_name . $ext;
			$new_file['size'] = calcFilesize(filesize($path . $new_file['name']));

			return $new_file;
		}
	}
	return array();
}
/**
 * Zeigt Dropdown-Menüs für die Veröffentlichungsdauer von Inhalten an
 *
 * @param string $mode
 * 	Start- bzw. Enddatum
 * @param integer $value
 * 	Die Zeitstempel des Eintrages
 * @return string
 */
function datepicker($name, $value = '')
{
	global $date, $tpl;
	static $included = false;

	$format = 'Y-m-d H:i';
	if (!empty($_POST['form'][$name])) {
		$input = $_POST['form'][$name];
	} elseif (validate::isNumber($value)) {
		$input = $date->format($value, $format);
	} else {
		$input = $date->format(time(), $format);
	}

	$datepicker = array(
		'already_included' => $included,
		'format' => 'yy-mm-dd',
		'name' => $name,
		'input' => $input,
	);
	$tpl->assign('date', $datepicker);
	// Variable auf 'true' setzen, damit der datepicker nicht unzählige Male eingebunden wird
	$included = true;

	return $tpl->fetch('common/date.html');
}
/**
 * Gibt eine Seitenauswahl aus
 *
 * @param integer $rows
 *  Anzahl der Datensätze
 * @return string
 *  Gibt die Seitenauswahl aus
 */
function pagination($rows)
{
	global $tpl, $uri;

	if ($rows > CONFIG_ENTRIES) {
		// Alle angegeben URL Parameter mit in die URL einbeziehen
		$acp = defined('IN_ADM') ? 'acp/' : '';
		$params = '';
		if (!empty($uri->params)) {
			foreach ($uri->params as $key => $value) {
				if ($key != 'mod' && $key != 'page' && $key != 'pos') {
					$params.= '/' . $key . '_' . $value;
				}
			}
		}

		$tpl->assign('uri', uri($acp . $uri->mod . '/' . $uri->page . $params));

		// Seitenauswahl
		$c_pages = ceil($rows / CONFIG_ENTRIES);
		$recent = 0;

		for ($i = 1; $i <= $c_pages; ++$i) {
			$pages[$i]['selected'] = POS == $recent ? true : false;
			$pages[$i]['page'] = $i;
			$pages[$i]['pos'] = 'pos_' . $recent . '/';

			$recent = $recent + CONFIG_ENTRIES;
		}
		$tpl->assign('pages', $pages);

		// Vorherige Seite
		$pos_prev = array('pos' => POS - CONFIG_ENTRIES >= 0 ? 'pos_' . (POS - CONFIG_ENTRIES) . '/' : '', 'selected' => POS == 0 ? true : false);
		$tpl->assign('pos_prev', $pos_prev);

		// Nächste Seite
		$pos_next = array('pos' => 'pos_' . (POS + CONFIG_ENTRIES) . '/', 'selected' => POS + CONFIG_ENTRIES >= $rows ? true : false);
		$tpl->assign('pos_next', $pos_next);

		return $tpl->fetch('common/pagination.html');
	}
}
/**
 * Umleitung auf andere URLs
 *
 * @param string $args
 *  Leitet auf eine Interne ACP3 Seite weiter
 * @param string $new_page
 *  Leitet auf eine externe Seite weiter
 */
function redirect($args, $new_page = 0)
{
	header('Location:' . (empty($args) && !empty($new_page) ? str_replace('&amp;', '&', $new_page) : uri($args)));
	exit;
}
function removeFile($dir, $file)
{
	$path = ACP3_ROOT . 'uploads/' . $dir . '/' . $file;
	if (!empty($dir) && !empty($file) && !preg_match('=/=', $dir) && !preg_match('=/=', $file) && is_file($path)) {
		@unlink($path);
	}
	return false;
}
/**
 * Funktion zum Salzen von Passwörtern, damit diese nicht so leicht entschlüsselt werden können
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
 * @param mixed $value
 *  Abzugleichender Parameter mit $field_value
 * @param mixed $field_value
 *  Wert aus der SQL Tabelle
 * @param string $attr
 *  HTML-Attribut, um Eintrag zu selektieren
 * @return string
 */
function selectEntry($name, $value, $field_value = '', $attr = 'selected')
{
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
	return '';
}
/**
 * Gibt eine Liste aller Zeitzonen aus
 *
 * @param integer $value
 * 	Der Wert der aktuell eingestellten Zeitzone
 * @return array
 */
function timeZones($value)
{
	global $lang;

	$time_zones = array(-12, -11, -10, -9.5, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 8, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 14);
	$time_zone = array();
	$i = 0;
	foreach ($time_zones as $row) {
		$time_zone[$i]['value'] = $row * 3600;
		$time_zone[$i]['selected'] = selectEntry('time_zone', $row * 3600, $value);
		$time_zone[$i]['lang'] = $lang->t('common', 'utc' . $row);
		$i++;
	}
	return $time_zone;
}
/**
 * Generiert die ACP3 internen Hyperlinks
 *
 * @param string $uri
 *  Inhalt der zu generierenden URL
 * @return string
 */
function uri($uri)
{
	$prefix = CONFIG_SEF == '1' && defined('IN_ACP3') ? ROOT_DIR : PHP_SELF . '/';
	return $prefix . $uri . (!preg_match('/\/$/', $uri) ? '/' : '');
}
?>