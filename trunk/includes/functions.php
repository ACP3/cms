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
	if (!empty($text)) {
		$confirm = array(
			'text' => $text,
			'forward' => $forward,
			'overlay' => $overlay,
		);
		if (!empty($backward))
			$confirm['backward'] = $backward;
		ACP3_CMS::$view->assign('confirm', $confirm);

		return ACP3_CMS::$view->fetchTemplate('system/confirm_box.tpl');
	}
	return '';
}
/**
 * 
 * @param array $config
 * @return string
 */
function datatable(array $config)
{
	ACP3_CMS::$view->enableJsLibraries(array('datatables'));

	static $init = false;

	if (isset($config['records_per_page']) === false)
		$config['records_per_page'] = ACP3_CMS::$auth->entries;
	
	$config['initialized'] = $init;

	ACP3_CMS::$view->assign('dt', $config);
	$init = true;

	return ACP3_CMS::$view->fetchTemplate('system/data_table.tpl');
}
/**
 * Gibt eine Box mit den aufgetretenen Fehlern aus
 *
 * @param string|array $errors
 * @return string
 */
function errorBox($errors)
{
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
	ACP3_CMS::$view->assign('error_box', array('non_integer_keys' => $non_integer_keys, 'errors' => $errors));
	return ACP3_CMS::$view->fetchTemplate('system/error_box.tpl');
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
			$mail->set('Mailer', 'smtp');
			$mail->Host = CONFIG_MAILER_SMTP_HOST;
			$mail->Port = CONFIG_MAILER_SMTP_PORT;
			$mail->SMTPSecure = CONFIG_MAILER_SMTP_SECURITY === 'ssl' || CONFIG_MAILER_SMTP_SECURITY === 'tls' ? CONFIG_MAILER_SMTP_SECURITY : '';
			if ((bool) CONFIG_MAILER_SMTP_AUTH === true) {
				$mail->SMTPAuth = true;
				$mail->Username = CONFIG_MAILER_SMTP_USER;
				$mail->Password = CONFIG_MAILER_SMTP_PASSWORD;
			}
		} else {
			$mail->set('Mailer', 'mail');
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
 * Generiert ein Inhaltsverzeichnis
 * 
 * @param array $pages
 * @param string $path
 * @param boolean $titles_from_db
 * @param boolean $custom_uris
 * @return string
 */
function generateTOC(array $pages, $path = '', $titles_from_db = false, $custom_uris = false)
{
	if (!empty($pages)) {
		$path = empty($path) ? ACP3_CMS::$uri->getCleanQuery() : $path;
		$toc = array();
		$i = 0;
		foreach ($pages as $page) {
			$page_num = $i + 1;
			if ($titles_from_db === false) {
				$attributes = getHtmlAttributes($page);
				$toc[$i]['title'] = !empty($attributes['title']) ? $attributes['title'] : sprintf(ACP3_CMS::$lang->t('system', 'toc_page'), $page_num);
			} else {
				$toc[$i]['title'] = !empty($page['title']) ? $page['title'] : sprintf(ACP3_CMS::$lang->t('system', 'toc_page'), $page_num);
			}

			$toc[$i]['uri'] = $custom_uris === true ? $page['uri'] : ACP3_CMS::$uri->route($path) . ($page_num > 1 ? 'page_' . $page_num . '/' : '');

			$toc[$i]['selected'] = false;
			if ($custom_uris === true) {
				if ($page['uri'] === ACP3_CMS::$uri->route(ACP3_CMS::$uri->query) ||
					ACP3_CMS::$uri->route(ACP3_CMS::$uri->query) === ACP3_CMS::$uri->route(ACP3_CMS::$uri->mod . '/' . ACP3_CMS::$uri->file) && $i == 0) {
					$toc[$i]['selected'] = true;
					ACP3_CMS::$breadcrumb->setTitlePostfix($toc[$i]['title']);
				}
			} else {
				if ((ACP3_Validate::isNumber(ACP3_CMS::$uri->page) === false && $i === 0) || ACP3_CMS::$uri->page === $page_num) {
					$toc[$i]['selected'] = true;
					ACP3_CMS::$breadcrumb->setTitlePostfix($toc[$i]['title']);
				}
			}
			++$i;
		}
		ACP3_CMS::$view->assign('toc', $toc);
		return ACP3_CMS::$view->fetchTemplate('system/toc.tpl');
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
			$matches = array();
			preg_match_all($regex, $text, $matches);

			$currentPage = ACP3_Validate::isNumber(ACP3_CMS::$uri->page) === true && ACP3_CMS::$uri->page <= $c_pages ? ACP3_CMS::$uri->page : 1;
			$next_page = !empty($pages[$currentPage]) ? ACP3_CMS::$uri->route($path) . 'page_' . ($currentPage + 1) . '/' : '';
			$previous_page = $currentPage > 1 ? ACP3_CMS::$uri->route($path) . ($currentPage - 1 > 1 ? 'page_' . ($currentPage - 1) . '/' : '') : '';

			if (!empty($next_page))
				ACP3_SEO::setNextPage($next_page);
			if (!empty($previous_page))
				ACP3_SEO::setPreviousPage($previous_page);

			$page = array(
				'toc' => generateTOC($matches[0], $path),
				'text' => $pages[$currentPage - 1],
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
	if (isset($_SESSION['redirect_message']) && is_array($_SESSION['redirect_message'])) {
		ACP3_CMS::$view->assign('redirect', $_SESSION['redirect_message']);
		ACP3_CMS::$view->assign('redirect_message', ACP3_CMS::$view->fetchTemplate('system/redirect_message.tpl'));
		unset($_SESSION['redirect_message']);
	}
}
/**
 * Setzt eine Redirect Nachricht
 *
 * @param string $text
 * @param string $path
 */
function setRedirectMessage($success, $text, $path, $overlay = false)
{
	if (empty($text) === false && empty($path) === false) {
		$_SESSION['redirect_message'] = array(
			'success' => is_int($success) ? true : (bool) $success,
			'text' => $text
		);
		if ($overlay === true) {
			ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('system/close_overlay.tpl'));
		} else {
			ACP3_CMS::$uri->redirect($path);
		}
	}
}
/**
 * Gibt zurück, ob der aktuelle User Agent ein mobiler Browser ist, oder nicht.
 *
 * @return boolean
 * @see http://detectmobilebrowsers.com/download/php
 */
function isMobileBrowser()
{
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) ||
		preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
		return true;
	return false;
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
	return preg_replace($search, $replace, strtolower($var));
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
	$path = UPLOADS_DIR . $dir . '/';
	$ext = strrchr($filename, '.');
	$new_name = 1;

	// Dateiname solange ändern, wie die Datei im aktuellen Ordner vorhanden ist
	while (is_file($path . $new_name . $ext) === true) {
		$new_name++;
	}

	if (is_writable($path) === true) {
		if (!@move_uploaded_file($tmp_filename, $path . $new_name . $ext)) {
			echo sprintf(ACP3_CMS::$lang->t('system', 'upload_error'), $filename);
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
		ACP3_CMS::$db2->beginTransaction();
		try {
			$id = (int) $id;
			$table = DB_PRE . $table;

			// Zusätzliche WHERE-Bedingung
			$where = !empty($where) ? 'a.' . $where . ' = b.' . $where . ' AND ' : '';

			// Ein Schritt nach oben
			if ($action === 'up') {
				// Aktuelles Element und das vorherige Element selektieren
				$query = ACP3_CMS::$db2->fetchAssoc('SELECT a.' . $id_field . ' AS other_id, a.' . $sort_field . ' AS other_sort, b.' . $sort_field . ' AS elem_sort FROM ' . $table . ' AS a, ' . $table . ' AS b WHERE ' . $where . 'b.' . $id_field . ' = ' . $id . ' AND a.' . $sort_field . ' < b.' . $sort_field . ' ORDER BY a.' . $sort_field . ' DESC LIMIT 1');
			// Ein Schritt nach unten
			} else {
				// Aktuelles Element und das nachfolgende Element selektieren
				$query = ACP3_CMS::$db2->fetchAssoc('SELECT a.' . $id_field . ' AS other_id, a.' . $sort_field . ' AS other_sort, b.' . $sort_field . ' AS elem_sort FROM ' . $table . ' AS a, ' . $table . ' AS b WHERE ' . $where . 'b.' . $id_field . ' = ' . $id . ' AND a.' . $sort_field . ' > b.' . $sort_field . ' ORDER BY a.' . $sort_field . ' ASC LIMIT 1');
			}

			if (!empty($query)) {
				// Sortierreihenfolge des aktuellen Elementes zunächst auf 0 setzen
				// um Probleme mit möglichen Duplicate-Keys zu umgehen
				ACP3_CMS::$db2->update($table, array($sort_field => 0), array($id_field => $id));
				ACP3_CMS::$db2->update($table, array($sort_field => $query['elem_sort']), array($id_field => $query['other_id']));
				// Element nun den richtigen Wert zuweisen
				ACP3_CMS::$db2->update($table, array($sort_field => $query['other_sort']), array($id_field => $id));

				ACP3_CMS::$db2->commit();
				return true;
			}

		} catch (Exception $e) {
			ACP3_CMS::$db2->rollback();
		}
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
	if ($rows > ACP3_CMS::$auth->entries) {
		// Alle angegebenen URL Parameter mit in die URL einbeziehen
		$link = ACP3_CMS::$uri->route((defined('IN_ADM') === true ? 'acp/' : '') . ACP3_CMS::$uri->getCleanQuery());

		// Seitenauswahl
		$current_page = ACP3_Validate::isNumber(ACP3_CMS::$uri->page) ? (int) ACP3_CMS::$uri->page : 1;

		if ($current_page > 1) {
			$postfix = sprintf(ACP3_CMS::$lang->t('system', 'page_x'), $current_page);
			ACP3_CMS::$breadcrumb->setTitlePostfix($postfix);
		}
		$pagination = array();
		$c_pagination = (int) ceil($rows / ACP3_CMS::$auth->entries);
		$show_first_last = 5;
		$show_previous_next = 2;
		$pages_to_display = 7;
		$j = 0;

		// Vorherige und nächste Seite für Suchmaschinen und Prefetching propagieren
		if (defined('IN_ADM') === false) {
			if ($current_page - 1 > 0) {
				// Seitenangabe in der Seitenbeschreibung ab Seite 2 angeben
				ACP3_SEO::setDescriptionPostfix(sprintf(ACP3_CMS::$lang->t('system', 'page_x'), $current_page));
				ACP3_SEO::setPreviousPage($link . 'page_' . ($current_page - 1) . '/');
			}
			if ($current_page + 1 <= $c_pagination)
				ACP3_SEO::setNextPage($link . 'page_' . ($current_page + 1) . '/');
			if (isset(ACP3_CMS::$uri->page) && ACP3_CMS::$uri->page === 1)
				ACP3_SEO::setCanonicalUri($link);
		}

		$start = 1;
		$end = $c_pagination;
		if ($c_pagination > $pages_to_display) {
			$center = floor($pages_to_display / 2);
			// Beginn der anzuzeigenden Seitenzahlen
			if ($current_page - $center > 0)
				$start = $current_page - $center;
			// Ende der anzuzeigenden Seitenzahlen
			if ($start + $pages_to_display - 1 <= $c_pagination)
				$end = $start + $pages_to_display - 1;

			// Anzuzeigende Seiten immer auf dem Wert von $pages_to_display halten
			if ($end - $start < $pages_to_display && $end - $pages_to_display > 0) {
				$start = $end - $pages_to_display + 1;
			}
		}

		// Erste Seite
		if ($c_pagination > $show_first_last && $start > 1) {
			$pagination[$j]['selected'] = false;
			$pagination[$j]['page'] = '&laquo;';
			$pagination[$j]['title'] = ACP3_CMS::$lang->t('system', 'first_page');
			$pagination[$j]['uri'] = $link . $fragment;
			++$j;
		}

		// Vorherige Seite
		if ($c_pagination > $show_previous_next && $current_page !== 1) {
			$pagination[$j]['selected'] = false;
			$pagination[$j]['page'] = '&lsaquo;';
			$pagination[$j]['title'] = ACP3_CMS::$lang->t('system', 'previous_page');
			$pagination[$j]['uri'] = $link . ($current_page - 1 > 1 ? 'page_' . ($current_page - 1) . '/' : '') . $fragment;
			++$j;
		}

		for ($i = (int) $start; $i <= $end; ++$i, ++$j) {
			$pagination[$j]['selected'] = $current_page === $i ? true : false;
			$pagination[$j]['page'] = $i;
			$pagination[$j]['uri'] = $link . ($i > 1 ? 'page_' . $i . '/' : '') . $fragment;
		}

		// Nächste Seite
		if ($c_pagination > $show_previous_next && $current_page !== $c_pagination) {
			$pagination[$j]['selected'] = false;
			$pagination[$j]['page'] = '&rsaquo;';
			$pagination[$j]['title'] = ACP3_CMS::$lang->t('system', 'next_page');
			$pagination[$j]['uri'] = $link . 'page_' . ($current_page + 1) . '/' . $fragment;
			++$j;
		}

		// Letzte Seite
		if ($c_pagination > $show_first_last && $c_pagination !== $end) {
			$pagination[$j]['selected'] = false;
			$pagination[$j]['page'] = '&raquo;';
			$pagination[$j]['title'] = ACP3_CMS::$lang->t('system', 'last_page');
			$pagination[$j]['uri'] = $link . 'page_' . $c_pagination . '/' . $fragment;
		}

		ACP3_CMS::$view->assign('pagination', $pagination);

		return ACP3_CMS::$view->fetchTemplate('system/pagination.tpl');
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
	$path = UPLOADS_DIR . $dir . '/' . $file;
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
	$host = $_SERVER['HTTP_HOST'];
	return preg_replace_callback('/<a href="(http(s?):\/\/' . $host . ')?(' . $root_dir . ')?(index\.php)?(\/?)((?i:[a-z\d_\-]+\/){2,})"/', 'rewriteInternalUriCallback', $text);
}
/**
 * Callback-Funktion zum Ersetzen der ACP3 internen URIs gegen ihre Aliase
 *
 * @param string $matches
 * @return string
 */
function rewriteInternalUriCallback($matches)
{
	return '<a href="' . ACP3_CMS::$uri->route($matches[6], 1) . '"';
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
/**
 * Enkodiert alle HTML-Entitäten eines Strings
 * zur Vermeidung von XSS
 *
 * @param string $var
 * @param boolean $script_tag_only
 * @return string
 */
function str_encode($var, $script_tag_only = false)
{
	$var = preg_replace('=<script[^>]*>.*</script>=isU', '', $var);
	return $script_tag_only === true ? $var : htmlentities($var, ENT_QUOTES, 'UTF-8');
}