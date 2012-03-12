<?php
/**
 * register_globals = off Emulation und magic_quotes deaktivieren
 *
 * @package ACP3
 * @subpackage Core
 * @copyright http://www.php.net/manual/faq.misc.php#53961
 */

if (defined('IN_ACP3') === false)
	exit;

if ((bool)@ini_get('register_globals')) {
	// Superglobal
	$superglobals = array($_ENV, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
	if (isset($_SESSION))
		array_unshift($superglobals, $_SESSION);

	$knownglobals = array(
		// Reservierte Superglobale von PHP:
		'_ENV', 'HTTP_ENV_VARS', '_GET', 'HTTP_GET_VARS', '_POST', 'HTTP_POST_VARS', '_COOKIE', 'HTTP_COOKIE_VARS', '_FILES', 'HTTP_FILES_VARS', '_SERVER', 'HTTP_SERVER_VARS', '_SESSION', 'HTTP_SESSION_VARS', '_REQUEST',

		// Globale Variablen dieser Datei:
		'superglobals', 'knownglobals', 'superglobal', 'global', 'void',
	);
	foreach ($superglobals as $superglobal) {
		foreach ($superglobal as $global => $void) {
			if (in_array($global, $knownglobals) === false)
				unset($GLOBALS[$global]);
		}
	}
}

// Magic Quotes deaktivieren
if (get_magic_quotes_gpc()) {
	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while (list($key, $val) = each($process)) {
		foreach ($val as $k => $v) {
			unset($process[$key][$k]);
			if (is_array($v) === true) {
				$process[$key][stripslashes($k)] = $v;
				$process[] = &$process[$key][stripslashes($k)];
			} else {
				$process[$key][stripslashes($k)] = stripslashes($v);
			}
		}
	}
	unset($process);
}