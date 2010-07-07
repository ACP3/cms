<?php
/**
 * Diese Datei ist für die häufig verwendeten Funktionen im ACP3 zuständig
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
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
		5 => 'PiB',
		6 => 'EiB',
		7 => 'ZiB',
		8 => 'YiB',
	);

	for ($i = 0; $value >= 1024; ++$i) {
		$value = $value / 1024;
	}

	return round($value, 2) . ' ' . $units[$i];
}
/**
 * Erzeugt das Captchafeld für das Template
 *
 * @param integer $captcha_length
 *  Anzahl der Zeichen, welche das Captcha haben soll
 * @return string
 */
function captcha($captcha_length = 5)
{
	global $auth, $tpl;

	// Wenn man als User angemeldet ist, Captcha nicht anzeigen
	if (!$auth->isUser()) {
		$captcha['hash'] = md5(uniqid(rand(), true));
		$captcha['length'] = $captcha_length;
		$captcha['width'] = $captcha_length * 25;
		$captcha['height'] = 30;
		$tpl->assign('captcha', $captcha);
		return modules::fetchTemplate('common/captcha.html');
	}
	return '';
}
/**
 * Gibt je nach angegebenen Parametern eine Fehlerbox oder eine Bestätigungsbox aus
 *
 * @param string $text
 *  Zu übergebender Text
 * @param string $forward
 *  Weiter Hyperlink
 * @param string $backward
 *  Zurück Hyperlink
 * @return string
 */
function comboBox($text, $forward = 0, $backward = 0)
{
	global $tpl;

	if (is_array($text) && empty($forward) && empty($backward)) {
		$tpl->assign('text', $text);
		return modules::fetchTemplate('common/error.html');
	} elseif (!empty($text) && (!empty($forward) || !empty($backward))) {
		$tpl->assign('text', $text);
		$tpl->assign('forward', $forward);
		if (!empty($backward)) {
			$tpl->assign('backward', $backward);
		}

		return modules::fetchTemplate('common/combo.html');
	}
	return '';
}
/**
 * Zeigt Dropdown-Menüs für die Veröffentlichungsdauer von Inhalten an
 *
 * @param mixed $name
 * 	Name des jeweiligen Inputfeldes
 * @param mixed $value
 * 	Der Zeitstempel des jeweiligen Eintrages
 * @param string $format
 *	Das anzuzeigende Format im Textfeld
 * @param array $params
 *	Dient dem Festlegen von weiteren Parametern
 * @param integer $range
 *	1 = Start- und Enddatum anzeigen
 *	2 = Einfaches Inputfeld mitsamt Datepicker anzeigen
 * @return string
 */
function datepicker($name, $value = '', $format = 'Y-m-d H:i', array $params = array(), $range = 1)
{
	global $date, $tpl;

	$datepicker = array(
		'range' => $range,
		'params' => array(
			'firstDay' => '\'1\'',
			'dateFormat' => '\'yy-mm-dd\'',
			'showOn' => '\'button\'',
			'buttonImage' => '\'' . ROOT_DIR . 'images/crystal/16/cal.png\'',
			'buttonImageOnly' => 'true',
			'constrainInput' => 'false',
		)
	);

	// Zusätzliche Datepicker-Parameter hinzufügen
	if (!empty($params) && is_array($params)) {
		$datepicker['params'] = array_merge($datepicker['params'], $params);
	}

	// Veröffentlichungszeitraum
	if ($range == 1 && is_array($name)) {
		if (!empty($_POST['form'][$name[0]]) && !empty($_POST['form'][$name[1]])) {
			$value_start = $_POST['form'][$name[0]];
			$value_end = $_POST['form'][$name[1]];
		} elseif (is_array($value) && validate::isNumber($value[0]) && validate::isNumber($value[1])) {
			$value_start = $date->format($value[0], $format);
			$value_end = $date->format($value[1], $format);
		} else {
			$value_start = $date->format(time(), $format);
			$value_end = $date->format(time(), $format);
		}

		$datepicker['name_start'] = $name[0];
		$datepicker['name_end'] = $name[1];
		$datepicker['value_start'] = $value_start;
		$datepicker['value_end'] = $value_end;
	// Einfaches Inputfeld mit Datepicker
	} else {
		if (!empty($_POST['form'][$name])) {
			$value = $_POST['form'][$name];
		} elseif (validate::isNumber($value)) {
			$value = $date->format($value, $format);
		} else {
			$value = $date->format(time(), $format);
		}

		$datepicker['name'] = $name;
		$datepicker['value'] = $value;
	}

	$tpl->assign('datepicker', $datepicker);

	return modules::fetchTemplate('common/date.html');
}
/**
 * Generiert eine E-Mail und versendet diese
 *
 * @param string $recipient_name
 *	Name des Empfängers
 * @param string $recipient_email
 *	E-Mail-Adresse des Empfängers
 * @param string $from
 *	E-mail-Adresse des Versenders
 * @param string $subject
 *	Betreff der E-Mail
 * @param string $body
 *	E-Mail-Body
 * @return boolean
 */
function genEmail($recipient_name, $recipient_email, $from, $subject, $body)
{
	require_once './includes/phpmailer/class.phpmailer.php';

	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->SetFrom($from);
	$mail->AddAddress($recipient_email, $recipient_name);
	$mail->Subject = $subject;
	$mail->Body = $body;

	return $mail->Send();
}
/**
 * Generiert ein gesalzenes Passwort
 *
 * @param string $salt
 *	Das zu verwendende Salz
 * @param string $plaintext
 *	Das Passwort in Klartextform, welches verschlüsselt werden soll
 * @param string $algorithm
 *	Der zu verwendende Hash-Algorithmus
 * @return string
 */
function genSaltedPassword($salt, $plaintext, $algorithm = 'sha1')
{
	return hash($algorithm, $salt . hash($algorithm, $plaintext));
}
/**
 * Macht einen String URL sicher
 *
 * @param string $var
 *	Die unzuwandelnde Variable
 * @return string
 */
function makeStringUrlSafe($var)
{
	$var = strip_tags($var);
	if (!preg_match('/&([a-z]+);/', $var)) {
		$var = htmlentities($var, ENT_QUOTES, 'UTF-8');
	}
	$var = strtolower($var);
	$search = array(
		'/&([a-z]{1})uml;/',
		'/&szlig;/',
		'/&([a-z0-9]+);/',
		'/(\s+)/',
		'/[^a-z0-9-]/'
	);
	$replace = array(
		'${1}e',
		'ss',
		'',
		'-',
		''
	);
	return preg_replace($search, $replace, $var);
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

	// Dateiname solange ändern, wie die Datei im aktuellen Ordner vorhanden ist
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
 * Verschiebt einen DB-Eintrag um einen Schritt nach oben bzw. unten
 *
 * @param string $action
 *	up = einen Schritt nach oben verschieben
 *	down = einen Schritt nach unten verschieben
 * @param string $table
 *	Die betroffene Tabelle
 * @param string $id_field
 *	Name des ID-Feldes
 * @param string $sort_field
 *	Name des Sortier-Feldes. damit die Sortierung geändert werden kann
 * @param string $id
 *	Die ID des Datensatzes, welcher umsortiert werden soll
 * @return boolean
 */
function moveOneStep($action, $table, $id_field, $sort_field, $id, $where = 0)
{
	if ($action == 'up' || $action == 'down') {
		global $db;

		$elem = null;

		// Zusätzliche WHERE-Bedingung
		$where = !empty($where) ? $where . ' AND ' : '';

		switch ($action) {
			// Ein Schritt nach oben
			case 'up':
				if ($db->countRows($id_field, $table, $where . $id_field . ' != \'' . $id . '\' AND ' . $sort_field . ' < (SELECT ' . $sort_field . ' FROM ' . $db->prefix . $table . ' WHERE ' . $where . $id_field . ' = \'' . $id . '\')') > 0) {
					$elem = $db->select($sort_field, $table, $where . $id_field . ' = \'' . $id . '\'');
					$pre = $db->select($id_field . ', ' . $sort_field, $table, $where . $sort_field . ' < ' . $elem[0][$sort_field] . '', $sort_field . ' DESC', 1);
				}
				break;
			// Ein Schritt nach unten
			case 'down':
				if ($db->countRows($id_field, $table, $where . $id_field . ' != \'' . $id . '\' AND ' . $sort_field . ' > (SELECT ' . $sort_field . ' FROM ' . $db->prefix . $table . ' WHERE ' . $where . $id_field . ' = \'' . $id . '\')') > 0) {
					$elem = $db->select($sort_field, $table, $where . $id_field . ' = \'' . $id . '\'');
					$pre = $db->select($id_field . ',' . $sort_field, $table, $where . $sort_field . ' > ' . $elem[0][$sort_field] . '', $sort_field . ' ASC', 1);
				}
				break;
			default:
				return false;
		}

		// Sortierung aktualisieren
		if (count($elem) == 1 && count($pre) == 1) {
			$bool = $db->update($table, array($sort_field => $pre[0][$sort_field]), $id_field . ' = \'' . $id . '\'');
			$bool2 = $db->update($table, array($sort_field => $elem[0][$sort_field]), $id_field . ' = \'' . $pre[0][$id_field] . '\'');

			return $bool && $bool2 ? true : false;
		}
	}
	return false;

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
	global $auth;

	if ($rows > $auth->entries) {
		global $lang, $tpl, $uri;

		// Alle angegebenen URL Parameter mit in die URL einbeziehen
		$acp = defined('IN_ADM') ? 'acp/' : '';
		$params = '';
		if (!empty($uri->params)) {
			foreach ($uri->params as $key => $value) {
				if ($key != 'mod' && $key != 'page' && $key != 'pos') {
					$params.= '/' . $key . '_' . $value;
				}
			}
		}

		$link = uri($acp . $uri->mod . '/' . $uri->page . $params);

		// Seitenauswahl
		$pagination = array();
		$c_pagination = ceil($rows / $auth->entries);
		$fl = 5;
		$pn = 2;
		$j = 0;

		// Erste Seite
		if ($c_pagination > $fl) {
			$pagination[$j]['selected'] = POS == 0 ? true : false;
			$pagination[$j]['page'] = '&laquo;';
			$pagination[$j]['title'] = $lang->t('common', 'first_page');
			$pagination[$j]['uri'] = $link;
			++$j;
		}

		// Vorherige Seite
		if ($c_pagination > $pn) {
			$pagination[$j]['selected'] = POS == 0 ? true : false;
			$pagination[$j]['page'] = '&lsaquo;';
			$pagination[$j]['title'] = $lang->t('common', 'previous_page');
			$pagination[$j]['uri'] = $link . (POS - $auth->entries >= 0 ? 'pos_' . (POS - $auth->entries) . '/' : '');
			++$j;
		}

		// Wenn mehr als 9 Seiten vorhanden sind, nur noch einen bestimmten Teil der Seitenauswahl anzeigen
		if ($c_pagination > 9) {
			$start = ceil(POS / $auth->entries) - $pn <= 0 ? 1 : ceil(POS / $auth->entries) - $pn;
			$end = $start + 4 > $c_pagination ? $c_pagination : $start + 4;
			$currentPos = $start * $auth->entries - $auth->entries;
		// Pagination komplett anzeigen
		} else {
			$start = 1;
			$end = $c_pagination;
			$currentPos = 0;
		}

		for ($i = $start; $i <= $end; ++$i, ++$j) {
			$pagination[$j]['selected'] = POS == $currentPos ? true : false;
			$pagination[$j]['page'] = $i;
			$pagination[$j]['uri'] = $link . 'pos_' . $currentPos . '/';

			$currentPos = $currentPos + $auth->entries;
		}

		// Nächste Seite
		if ($c_pagination > $pn) {
			$pagination[$j]['selected'] = POS + $auth->entries >= $rows ? true : false;
			$pagination[$j]['page'] = '&rsaquo;';
			$pagination[$j]['title'] = $lang->t('common', 'next_page');
			$pagination[$j]['uri'] = $link . 'pos_' . (POS + $auth->entries) . '/';
			++$j;
		}

		// Letzte Seite
		if ($c_pagination > $fl) {
			$pagination[$j]['selected'] = POS == ($currentPos - $auth->entries) ? true : false;
			$pagination[$j]['page'] = '&raquo;';
			$pagination[$j]['title'] = $lang->t('common', 'last_page');
			$pagination[$j]['uri'] = $link . 'pos_' . ($c_pagination * $auth->entries - $auth->entries) . '/';
		}

		$tpl->assign('pagination', $pagination);

		return modules::fetchTemplate('common/pagination.html');
	}
}
/**
 * Umleitung auf andere URLs
 *
 * @param string $args
 *  Leitet auf eine interne ACP3 Seite weiter
 * @param string $new_page
 *  Leitet auf eine externe Seite weiter
 */
function redirect($args, $new_page = 0)
{
	if (!empty($args)) {
		if ($args == 'errors/404' || $args == 'errors/403')
			$args = (defined('IN_ACP3') ? '' : 'acp/') . $args;

		$protocol = empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'off' ? 'http://' : 'https://';
		$host = $_SERVER['HTTP_HOST'];
		header('Location: ' . $protocol . $host . uri($args));
		exit;
	}
	header('Location:' . str_replace('&amp;', '&', $new_page));
	exit;
}
/**
 * Löscht eine Datei im uploads Ordner
 *
 * @param string $dir
 *	Der Ordner, in welchem die Datei liegt
 * @param string $file
 *	Der Name der Datei
 * @return boolean
 */
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
 * Kürzt einen String, welcher im UTF-8-Charset vorliegt
 * auf eine bestimmte Länge
 *
 * @param string $data
 *	Der zu kürzende String
 * @param integer $chars
 *	Die anzuzeigenden Zeichen
 * @param integer $diff
 *	Anzahl der Zeichen, welche nach strlen($data) - $chars noch kommen müssen
 * @param string append
 *	Kann bspw. dazu genutzt werden, um an den gekürzten Text noch einen Weiterlesen-Link anzuhängen
 * @return string
 */
function shortenEntry($data, $chars = 300, $diff = 50, $append = '')
{
	if ($chars <= $diff)
		$diff = 0;

	$shortened = strip_tags($data);
	$shortened = utf8_decode(html_entity_decode($shortened, ENT_QUOTES, 'UTF-8'));
	if (strlen($shortened) > $chars && strlen($shortened) - $chars >= $diff) {
		return utf8_encode(substr($shortened, 0, $chars - $diff)) . $append;
	}
	return $data;
}
/**
 * Gibt eine Liste aller Zeitzonen aus
 *
 * @param integer $value
 * 	Der Wert der aktuell eingestellten Zeitzone
 * @return array
 */
function timeZones($value, $name = 'time_zone')
{
	global $lang;

	$areas = array(-12, -11, -10, -9.5, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 8, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 14);

	$i = 0;
	foreach ($areas as $row) {
		$time_zones[$i]['value'] = $row * 3600;
		$time_zones[$i]['selected'] = selectEntry($name, $time_zones[$i]['value'], $value);
		$time_zones[$i]['lang'] = $lang->t('common', 'utc' . $row);
		$i++;
	}
	return $time_zones;
}
/**
 * Generiert die ACP3 internen Hyperlinks
 *
 * @param string $uri
 *  Inhalt der zu generierenden URL
 * @param integer $alias
 *	Gibt an, ob für die auszugebende Seite überprüft werden soll, ob ein Alias
 *	existier und dieser ausgegeben werden soll
 * @return string
 */
function uri($uri, $alias = 0)
{
	$uri = $uri . (!preg_match('/\/$/', $uri) ? '/' : '');

	// Überprüfen, ob Alias vorhanden ist und diesen als URI verwenden
	if ($alias == 1 && !preg_match('/^acp\//', $uri)) {
		global $db;

		$alias = $db->select('alias', 'aliases', 'uri = \'' . db::escape($uri) . '\'');
		if (!empty($alias)) {
			$uri = $alias[0]['alias'] . '/';
		}
	}
	$prefix = CONFIG_SEO_MOD_REWRITE == '0' || preg_match('/^acp\//', $uri) ? PHP_SELF . '/' : ROOT_DIR;
	return $prefix . $uri;
}