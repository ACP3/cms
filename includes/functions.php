<?php
/**
 * Diese Datei ist für die häufig verwendeten Funktionen im ACP3 zuständig
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
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
function combo_box($text, $forward = 0, $back = 0)
{
	global $tpl;

	$tpl->assign('text', $text);
	if (empty($forward) && empty($back)) {
		$tpl->assign('error_msg', $tpl->fetch('common/error.html'));
		return;
	} else {
		$tpl->assign('forward', $forward);
		$tpl->assign('back', $back);

		return $tpl->fetch('common/combo.html');
	}
}
/**
 * Datumsausrichtung an den Zeitzonen
 *
 * @param integer $mode
 * 	1 = Gibt ein formatiertes Datum aus
 * 	2 = Gibt einen Zeitstempel aus
 * 	3 = Erstellt einen Zeitstempel anhand der Daten aus den Dropdownmenüs für den Veröffentlichungszeitraum
 * @param mixed $time_stamp
 *  Zu formatierender Zeitstempel
 * @param string $format
 *  Datumsformat
 * @return mixed
 */
function date_aligned($mode, $time_stamp, $format = 0)
{
	global $auth;
	static $info = array();

	$info = $auth->getUserInfo('time_zone, dst');

	if (!empty($info)) {
		$time_zone = $info['time_zone'];
		$dst = $info['dst'];
	} else {
		$time_zone = CONFIG_TIME_ZONE;
		$dst = CONFIG_DST;
	}
	$offset = $time_zone + ($dst == '1' ? 3600 : 0);

	// Datum in jeweiliger Formatierung ausgeben
	if ($mode == 1) {
		$format = !empty($format) ? $format : CONFIG_DATE;
		return gmdate($format, $time_stamp + $offset);
	// Einfachen Zeitstempel ausgeben
	} elseif ($mode == 2) {
		return gmdate('U', $time_stamp);
	// Zeitstempel aus Dropdownmenü erstellen
	} elseif ($mode == 3 && is_array($time_stamp)) {
		$hour = $time_stamp[0] * 3600;
		$min = $time_stamp[1] * 60;
		$seconds = $hour + $min - $offset;
		return gmmktime(0, 0, $seconds, $time_stamp[3], $time_stamp[4], $time_stamp[5]);
	}
	return false;
}
/**
 * Datums Dropdownmenüs
 *
 * @param string $mode
 *  Legt fest, ob der Typ des Dropdownmenüs für die Tage, Monate, etc. zuständig sein soll
 * @param string $name
 *  Der Name des Dropdownmenüs im Formular
 * @param string $id
 *  Die ID des Dropdownmenüs im Formular
 * @param integer $value
 *  Evtl. schon vorhandener Wert, um diesen selektieren zu können
 * @return string
 */
function date_dropdown($mode, $name, $id, $value = '')
{
	global $tpl, $validate;

	$time = date_aligned(1, time(), 'Y');
	$date_arr = array(
		'day' => 'j|1|31',
		'month' => 'n|1|12',
		'year' => 'Y|' . ($time - 6) . '|' . ($time + 3),
		'hour' => 'G|0|23',
		'min' => 'i|0|59'
	);

	$tpl->assign('mode', $mode);
	$tpl->assign('name', $name);
	$tpl->assign('id', $id);

	$date = explode('|', $date_arr[$mode]);
	$loop = NULL;
	for ($date[1]; $date[1] <= $date[2]; $date[1]++) {
		$loop[$date[1]]['current'] = $date[1];
		$time = !$validate->is_number($value) ? date_aligned(1, time(), $date[0]) : $value;
		$loop[$date[1]]['selected'] = select_entry($name, $date[1], $time);
	}
	$tpl->assign('loop', $loop);

	return $tpl->fetch('common/date.html');
}
/**
 * Ermittelt die Dateigröße, gemäß IEC 60027-2
 *
 * @param integer $value
 * 	Die Dateigröße in Byte
 * @return string
 * 	Die Dateigröße als Fließkommazahl, mit der dazugehörigen Einheit
 */
function calc_filesize($value)
{
	$units = array(
		0 => 'Byte',
		1 => 'KiB',
		2 => 'MiB',
		3 => 'GiB',
		4 => 'TiB',
	);
	$loops = 0;

	while ($value >= 1024) {
		$value = $value / 1024;
		$loops++;
	}
	return round($value, 3) . ' ' . $units[$loops];
}
/**
 * Diese Funktion gibt den Inhalt der angeforderten Sprachkonstante aus
 *
 * @param string $mod
 *  Betroffenes Modul
 * @param string $key
 *  Betroffene Konstante
 * @return string
 */
function lang($mod, $key)
{
	global $auth;
	static $lang = 0;

	if (empty($lang)) {
		$info = $auth->getUserInfo('language');
		$lang = !empty($info) ? $info['language'] : CONFIG_LANG;
	}

	$path = 'languages/' . $lang . '/' . $mod . '.php';

	if (!defined($mod . '_' . $key) && is_file($path))
		include_once $path;

	return defined($mod . '_' . $key) ? str_replace('\n', '<br />', constant($mod . '_' . $key)) : strtoupper('{' . $mod . '_' . $key . '}');
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
function move_file($tmp_filename, $filename, $dir)
{
	$ext = strrchr($filename, '.');
	$path = 'uploads/' . $dir . '/';

	$new_name = 1;
	while (file_exists($path . $new_name . $ext)) {
		$new_name++;
	}
	if (is_writable($path)) {
		if (!@move_uploaded_file($tmp_filename, $path . $new_name . $ext)) {
			echo sprintf(lang('common', 'upload_error'), $filename);
		} else {
			$new_file['name'] = $new_name . $ext;
			$new_file['size'] = calc_filesize(filesize($path . $new_file['name']));

			return $new_file;
		}
	}
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
	global $modules, $tpl;

	if ($rows > CONFIG_ENTRIES) {
		// Alle angegeben URL Parameter mit in die URL einbeziehen
		$acp = defined('IN_ADM') ? 'acp/' : '';
		$id = !empty($modules->id) ? '/id_' . $modules->id : '';
		$cat = !empty($modules->cat) ? '/cat_' . $modules->cat : '';
		$gen = '';
		if (!empty($modules->gen)) {
			foreach ($modules->gen as $key => $value) {
				if ($key != 'pos') {
					$gen.= '/' . $key . '_' . $value;
				}
			}
		}

		$tpl->assign('uri', uri($acp . $modules->mod . '/' . $modules->page . $id . $cat . $gen));

		// Seitenauswahl
		$c_pages = ceil($rows / CONFIG_ENTRIES);
		$recent = 0;

		for ($i = 1; $i <= $c_pages; $i++) {
			$pages[$i]['selected'] = POS == $recent ? true : false;
			$pages[$i]['page'] = $i;
			$pages[$i]['pos'] = 'pos_' . $recent . '/';

			$recent = $recent + CONFIG_ENTRIES;
		}
		$tpl->assign('pages', $pages);

		// Vorherige Seite
		$pos_prev = array(
			'pos' => POS - CONFIG_ENTRIES >= 0 ? 'pos_' . (POS - CONFIG_ENTRIES) . '/' : '',
			'selected' => POS == 0 ? true : false,
		);
		$tpl->assign('pos_prev', $pos_prev);

		// Nächste Seite
		$pos_next = array(
			'pos' => 'pos_' . (POS + CONFIG_ENTRIES) . '/',
			'selected' => POS + CONFIG_ENTRIES >= $rows ? true : false,
		);
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
/**
 * Funktion zum Salzen von Passwörtern, damit diese nicht so leicht entschlüsselt werden können
 *
 * @param integer $str_length
 *  Länge des zufälligen Strings
 * @return string
 */
function salt($str_length)
{
	$chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	$c_chars = count($chars) - 1;
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
function select_entry($name, $value, $field_value = '', $attr = 'selected')
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
}
/**
 * Gibt eine Liste aller Zeitzonen aus
 *
 * @param integer $value
 * 	Der Wert der aktuell eingestellten Zeitzone
 * @return array
 */
function time_zones($value)
{
	$time_zones = array(-12, -11, -10, -9.5, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 8, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 14);
	$time_zone = array();
	$i = 0;
	foreach ($time_zones as $row) {
		$time_zone[$i]['value'] = $row * 3600;
		$time_zone[$i]['selected'] = select_entry('time_zone', $row * 3600, $value);
		$time_zone[$i]['lang'] = lang('common', 'utc' . $row);
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
	$pre = CONFIG_SEF == '0' ? PHP_SELF . '?stm=' : ROOT_DIR;
	return $pre . $uri . (!preg_match('/\/$/', $uri) ? '/' : '');
}
?>