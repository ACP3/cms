<?php
/**
 * Diese Datei ist für die häufig verwendeten Funktionen im ACP3 zuständig
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Ermittelt die Dateigröße gemäß IEC 60027-2
 *
 * @param integer $value
 * 	Die Dateigröße in Byte
 * @return string
 * 	Die Dateigröße als Fließkommazahl mit der dazugehörigen Einheit
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
 * Gibt je nach angegebenen Parametern eine Fehlerbox oder eine Bestätigungsbox aus
 *
 * @param string $text
 *  Zu übergebender Text
 * @param string $forward
 *  Weiter Hyperlink
 * @param string $backward
 *  Zurück Hyperlink
 * @param integer $overlay
 *	Wenn Wert "1", dann wird das Fenster geschlossen
 * @return string
 */
function confirmBox($text, $forward = 0, $backward = 0, $overlay = 0)
{
	global $tpl;

	if (!empty($text)) {
		$confirm = array(
			'text' => $text,
			'forward' => $forward,
			'overlay' => $overlay,
		);
		if (!empty($backward))
			$confirm['backward'] = $backward;
		$tpl->assign('confirm', $confirm);

		return ACP3_View::fetchTemplate('common/confirm_box.tpl');
	}
	return '';
}
/**
 * Gibt eine Box mit den aufgetretenen Fehlern aus
 *
 * @param string|array $errors
 * @return string
 */
function errorBox($errors)
{
	global $tpl;

	$non_integer_keys = false;
	if (is_array($errors) === true) {
		foreach(array_keys($errors) as $key) {
			if (ACP3_Validate::isNumber($key) === false) {
				$non_integer_keys = true;
				break;
			}
		}
	} else {
		$errors = (array) $errors;
	}
	$tpl->assign('error_box', array('non_integer_keys' => $non_integer_keys, 'errors' => $errors));
	return ACP3_View::fetchTemplate('common/error_box.tpl');
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
 * @return boolean|string
 */
function generateEmail($recipient_name, $recipient_email, $from, $subject, $body)
{
	require_once LIBRARIES_DIR . 'phpmailer/class.phpmailer.php';

	$mail = new PHPMailer(true);
	try {
		if (strtolower(CONFIG_MAILER_TYPE) === 'smtp') {
			$mail->IsSMTP();
			$mail->Host = CONFIG_MAILER_SMTP_HOST;
			$mail->Port = CONFIG_MAILER_SMTP_PORT;
			$mail->SMTPSecure = CONFIG_MAILER_SMTP_SECURITY === 'ssl' || CONFIG_MAILER_SMTP_SECURITY === 'tls' ? CONFIG_MAILER_SMTP_SECURITY : '';
			if ((bool) CONFIG_MAILER_SMTP_AUTH === true) {
				$mail->SMTPAuth = true;
				$mail->Username = CONFIG_MAILER_SMTP_USER;
				$mail->Password = CONFIG_MAILER_SMTP_PASSWORD;
			}
		} else {
			$mail->IsMail();
		}
		$mail->CharSet = 'UTF-8';
		$mail->Encoding = '8bit';
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->WordWrap = 76;
		$mail->SetFrom($from);
		$mail->AddAddress($recipient_email, $recipient_name);
		$mail->Send();

		return true;
	} catch(phpmailerException $e) {
		return $e->errorMessage();
	} catch (Exception $e) {
		return $e->getMessage();
	}
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
function generateSaltedPassword($salt, $plaintext, $algorithm = 'sha1')
{
	return hash($algorithm, $salt . hash($algorithm, $plaintext));
}
/**
 * Generiert das Inhaltsverzeichnis
 *
 * @param string $pages
 */
function generateTOC(array $pages, $path)
{
	if (!empty($pages)) {
		global $lang, $tpl, $uri;

		$toc = array();
		$i = 0;
		foreach ($pages as $page) {
			$attributes = getHtmlAttributes($page);
			$page_num = $i + 1;
			$toc[$i]['title'] = !empty($attributes['title']) ? $attributes['title'] : sprintf($lang->t('static_pages', 'page'), $page_num);
			$toc[$i]['uri'] = $uri->route($path, 1) . 'page_' . $page_num . '/';
			$toc[$i]['selected'] = (ACP3_Validate::isNumber($uri->page) === false && $i === 0) || $uri->page === $page_num ? true : false;
			++$i;
		}
		$tpl->assign('toc', $toc);
		return ACP3_View::fetchTemplate('common/toc.tpl');
	}
	return '';
}
/**
 * Liest aus einem String alle vorhandenen HTML-Attribute ein und
 * liefert diese als assoziatives Array zurück
 *
 * @param string $string
 * @return array
 */
function getHtmlAttributes($string)
{
	$matches = array();
	preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $matches);

	$return = array();
	if (!empty($matches)) {
		$c_matches = count($matches[1]);
		for ($i = 0; $i < $c_matches; ++$i)
			$return[$matches[1][$i]] = $matches[2][$i];
	}

	return $return;
}
/**
 * Parst einen Text und zerlegt diesen bei Bedarf mehrere Seiten
 *
 * @param string $text
 *	Der zu parsende Text
 * @param string $path
 *	Der ACP3-interne URI-Pfad, um die Links zu generieren
 * @return string|array
 */
function splitTextIntoPages($text, $path)
{
	// Falls keine Seitenumbrüche vorhanden sein sollten, Text nicht unnötig bearbeiten
	if (strpos($text, 'class="page-break"') === false) {
		return $text;
	} else {
		$regex = '/<hr(.+)class="page-break"(.*)(\/>|>)/iU';

		$pages = preg_split($regex, $text, -1, PREG_SPLIT_NO_EMPTY);
		$c_pages = count($pages);

		// Falls zwar Seitenumbruch gesetzt ist, aber danach
		// kein weiterer Text kommt, den unbearbeiteten Text ausgeben
		if ($c_pages == 1) {
			return $text;
		} else {
			global $uri;

			$matches = array();
			preg_match_all($regex, $text, $matches);

			$currentPage = ACP3_Validate::isNumber($uri->page) === true && $uri->page <= $c_pages ? $uri->page - 1 : 0;
			$next_page = $currentPage + 2 <= $c_pages ? $uri->route($path, 1) . 'page_' . ($currentPage + 2) . '/' : '';
			$previous_page = $currentPage > 0 ? $uri->route($path, 1) . 'page_' . $currentPage . '/' : '';

			if (!empty($next_page))
				ACP3_SEO::setNextPage($next_page);
			if (!empty($previous_page))
				ACP3_SEO::setPreviousPage($previous_page);

			$page = array(
				'toc' => generateTOC($matches[0], $path),
				'text' => $pages[$currentPage],
				'next' => $next_page,
				'previous' => $previous_page,
			);

			return $page;
		}
	}
}
/**
 * Holt sich die von setRedirectMatch() erzeugte Redirect Nachricht
 */
function getRedirectMessage()
{
	global $tpl;

	if (isset($_SESSION['redirect_message']) && is_array($_SESSION['redirect_message'])) {
		$tpl->assign('redirect', $_SESSION['redirect_message']);
		$tpl->assign('redirect_message', ACP3_View::fetchTemplate('common/redirect_message.tpl'));
		unset($_SESSION['redirect_message']);
	}
}
/**
 * Setzt eine Redirect Nachricht
 *
 * @param string $text
 * @param string $path
 */
function setRedirectMessage($success, $text, $path)
{
	global $uri;

	if (empty($text) === false && empty($path) === false) {
		$_SESSION['redirect_message'] = array(
			'success' => (bool) $success,
			'text' => $text
		);
		$uri->redirect($path);
	}
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
	if (!preg_match('/&([a-z]+);/', $var))
		$var = htmlentities($var, ENT_QUOTES, 'UTF-8');
	$var = strtolower($var);
	$search = array(
		'/&([a-z]{1})uml;/',
		'/&szlig;/',
		'/&([a-z0-9]+);/',
		'/(\s+)/',
		'/-{2,}/',
		'/[^a-z0-9-]/',
	);
	$replace = array(
		'${1}e',
		'ss',
		'',
		'-',
		'-',
		'',
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
	while (is_file($path . $new_name . $ext) === true) {
		$new_name++;
	}

	if (is_writable($path) === true) {
		if (!@move_uploaded_file($tmp_filename, $path . $new_name . $ext)) {
			global $lang;

			echo sprintf($lang->t('common', 'upload_error'), $filename);
		} else {
			$new_file = array();
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
 * @param string $where
 *	Optionales Vergleichsfeld, um den richtigen Vorgänger/Nachfolger bestimmen zu können
 * @return boolean
 */
function moveOneStep($action, $table, $id_field, $sort_field, $id, $where = '')
{
	if ($action === 'up' || $action === 'down') {
		global $db;

		$bool = $bool2 = $bool3 = false;
		$id = (int) $id;

		// Zusätzliche WHERE-Bedingung
		$where = !empty($where) ? 'a.' . $where . ' = b.' . $where . ' AND ' : '';

		// Ein Schritt nach oben
		if ($action === 'up') {
			// Aktuelles Element und das vorherige Element selektieren
			$query = $db->query('SELECT a.' . $id_field . ' AS other_id, a.' . $sort_field . ' AS other_sort, b.' . $sort_field . ' AS elem_sort FROM {pre}' . $table . ' AS a, {pre}' . $table . ' AS b WHERE ' . $where . 'b.' . $id_field . ' = ' . $id . ' AND a.' . $sort_field . ' < b.' . $sort_field . ' ORDER BY a.' . $sort_field . ' DESC LIMIT 1');
		// Ein Schritt nach unten
		} else {
			// Aktuelles Element und das nachfolgende Element selektieren
			$query = $db->query('SELECT a.' . $id_field . ' AS other_id, a.' . $sort_field . ' AS other_sort, b.' . $sort_field . ' AS elem_sort FROM {pre}' . $table . ' AS a, {pre}' . $table . ' AS b WHERE ' . $where . 'b.' . $id_field . ' = ' . $id . ' AND a.' . $sort_field . ' > b.' . $sort_field . ' ORDER BY a.' . $sort_field . ' ASC LIMIT 1');
		}

		if (!empty($query)) {
			// Sortierreihenfolge des aktuellen Elementes zunächst auf 0 setzen
			// um Probleme mit möglichen Duplicate-Keys zu umgehen
			$bool = $db->update($table, array($sort_field => 0), $id_field . ' = ' . $id);
			$bool2 = $db->update($table, array($sort_field => $query[0]['elem_sort']), $id_field . ' = ' . $query[0]['other_id']);
			// Element nun den richtigen Wert zuweisen
			$bool3 = $db->update($table, array($sort_field => $query[0]['other_sort']), $id_field . ' = ' . $id);
		}
		return $bool !== false && $bool2 !== false ? true : false;
	}
	return false;
}
/**
 * Konvertiert Zeilenumbrüche zu neuen Abschnitten
 *
 * @param string $data
 * @param boolean $is_xhtml
 * @param boolean $line_breaks
 * @return string
 */
function nl2p($data, $is_xhtml = true, $line_breaks = false)
{
	$data = trim($data);
	if ($line_breaks === true) {
		return '<p>' . preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '<br' . ($is_xhtml == true ? ' /' : '') . '>'), $data) . '</p>';
	} else {
		return '<p>' . preg_replace("/([\n]{1,})/i", "</p>\n<p>", $data) . '</p>';
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
function pagination($rows, $fragment = '')
{
	global $auth;

	if ($rows > $auth->entries) {
		global $lang, $tpl, $uri;

		// Alle angegebenen URL Parameter mit in die URL einbeziehen
		$link = $uri->route((defined('IN_ADM') === true ? 'acp/' : '') . $uri->getCleanQuery(), 1);

		// Seitenauswahl
		$current_page = ACP3_Validate::isNumber($uri->page) ? (int) $uri->page : 1;
		$pagination = array();
		$c_pagination = (int) ceil($rows / $auth->entries);
		$show_first_last = 5;
		$show_previous_next = 2;
		$pages_to_display = 7;
		$j = 0;

		// Vorherige und nächste Seite für Suchmaschinen und Prefetching propagieren
		if (defined('IN_ADM') === false) {
			if ($current_page - 1 > 0)
				ACP3_SEO::setPreviousPage($link . 'page_' . ($current_page - 1) . '/');
			if ($current_page + 1 <= $c_pagination)
				ACP3_SEO::setNextPage($link . 'page_' . ($current_page + 1) . '/');
		}

		// Wenn mehr als die in $pages_to_display festgelegten Seiten vorhanden sind, nur noch einen bestimmten Teil der Seitenauswahl anzeigen
		$start = $c_pagination > $pages_to_display && $current_page - $show_previous_next > 0 ? $current_page - $show_previous_next : 1;
		$end = $c_pagination > $pages_to_display && $start + $pages_to_display - 1 <= $c_pagination ? $start + $pages_to_display - 1 : $c_pagination;

		if ($c_pagination > $pages_to_display &&
			$end - $start < $pages_to_display && $end - $pages_to_display > 0) {
			$start = $end - $pages_to_display + 1;
		}

		// Erste Seite
		if ($c_pagination > $show_first_last && $start > 1) {
			$pagination[$j]['selected'] = false;
			$pagination[$j]['page'] = '&laquo;';
			$pagination[$j]['title'] = $lang->t('common', 'first_page');
			$pagination[$j]['uri'] = $link . $fragment;
			++$j;
		}

		// Vorherige Seite
		if ($c_pagination > $show_previous_next && $current_page !== 1) {
			$pagination[$j]['selected'] = false;
			$pagination[$j]['page'] = '&lsaquo;';
			$pagination[$j]['title'] = $lang->t('common', 'previous_page');
			$pagination[$j]['uri'] = $link . ($current_page - 1 > 1 ? 'page_' . ($current_page - 1) . '/' : '') . $fragment;
			++$j;
		}

		for ($i = (int) $start; $i <= $end; ++$i, ++$j) {
			$pagination[$j]['selected'] = $current_page === $i ? true : false;
			$pagination[$j]['page'] = $i;
			$pagination[$j]['uri'] = $link . 'page_' . $i . '/' . $fragment;
		}

		// Nächste Seite
		if ($c_pagination > $show_previous_next && $current_page !== $c_pagination) {
			$pagination[$j]['selected'] = false;
			$pagination[$j]['page'] = '&rsaquo;';
			$pagination[$j]['title'] = $lang->t('common', 'next_page');
			$pagination[$j]['uri'] = $link . 'page_' . ($current_page + 1) . '/' . $fragment;
			++$j;
		}

		// Letzte Seite
		if ($c_pagination > $show_first_last && $c_pagination !== $end) {
			$pagination[$j]['selected'] = false;
			$pagination[$j]['page'] = '&raquo;';
			$pagination[$j]['title'] = $lang->t('common', 'last_page');
			$pagination[$j]['uri'] = $link . 'page_' . $c_pagination . '/' . $fragment;
		}

		$tpl->assign('pagination', $pagination);

		return ACP3_View::fetchTemplate('common/pagination.tpl');
	}
}
/**
 * Liefert ein Array zur Ausgabe als Dropdown-Menü
 * für die Anzahl der anzuzeigenden Datensätze je Seite
 *
 * @param integer $current_value
 * @param integer $steps
 * @param integer $max_value
 * @return array
 */
function recordsPerPage($current_value, $steps = 5, $max_value = 50)
{
	// Einträge pro Seite
	$records = array();
	for ($i = 0, $j = $steps; $j <= $max_value; $i++, $j+= $steps) {
		$records[$i]['value'] = $j;
		$records[$i]['selected'] = selectEntry('entries', $j, $current_value);
	}
	return $records;
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
function removeUploadedFile($dir, $file)
{
	$path = ACP3_ROOT . 'uploads/' . $dir . '/' . $file;
	if (!empty($dir) && !empty($file) && !preg_match('=/=', $file) && is_file($path) === true)
		return @unlink($path);
	return false;
}
/**
 * Ersetzt interne ACP3 interne URIs in Texten mit ihren jeweiligen Aliasen
 *
 * @param string $text
 * @return string
 */
function rewriteInternalUri($text)
{
	$root_dir = str_replace('/', '\/', ROOT_DIR);
	return preg_replace_callback('/<a href="((' . $root_dir . ')?)((index\.php)?)(\/?)((?i:[a-z\d_\-]+\/){2,})"/', 'rewriteInternalUriCallback', $text);
}
/**
 * Callback-Funktion zum Ersetzen der ACP3 internen URIs gegen ihre Aliase
 *
 * @param string $matches
 * @return string
 */
function rewriteInternalUriCallback($matches)
{
	global $uri;

	return '<a href="' . $uri->route($matches[6], 1) . '"';
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
	$salt = '';
	$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$c_chars = strlen($chars) - 1;
	while (strlen($salt) < $str_length) {
		$char = $chars[mt_rand(0, $c_chars)];
		// Zeichen nur hinzufügen, wenn sich dieses nicht bereits im Salz befindet
		if (strpos($salt, $char) === false) {
			$salt.= $char;
		}
	}
	return $salt;
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
		if (is_array($_POST[$name]) === false && $_POST[$name] == $defValue) {
			return $attr;
		} elseif (is_array($_POST[$name]) === true) {
			foreach ($_POST[$name] as $row) {
				if ($row == $defValue)
					return $attr;
			}
		}
	} else {
		if (is_array($currentValue) === false && $currentValue == $defValue) {
			return $attr;
		} elseif (is_array($currentValue) === true) {
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

	$shortened = utf8_decode(html_entity_decode(strip_tags($data), ENT_QUOTES, 'UTF-8'));
	if (strlen($shortened) > $chars && strlen($shortened) - $chars >= $diff) {
		return utf8_encode(substr($shortened, 0, $chars - $diff)) . $append;
	}
	return $data;
}