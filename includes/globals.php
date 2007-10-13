<?php
/**
 * Register_globals OFF emulieren
 *
 * @package ACP3
 * @subpackage Core
 * @copyright http://de.php.net/manual/de/faq.misc.php
 */
if ((bool)@ini_get('register_globals')) {
	//Superglobal
	$superglobals = array($_ENV, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
	if (isset($_SESSION))
		array_unshift($superglobals, $_SESSION);

	$knownglobals = array(
		//Reservierte Superglobale von PHP:
		'_ENV', 'HTTP_ENV_VARS', '_GET', 'HTTP_GET_VARS', '_POST', 'HTTP_POST_VARS', '_COOKIE', 'HTTP_COOKIE_VARS', '_FILES', 'HTTP_FILES_VARS', '_SERVER', 'HTTP_SERVER_VARS', '_SESSION', 'HTTP_SESSION_VARS', '_REQUEST',

		//Globale Variablen dieser Datei:
		'superglobals', 'knownglobals', 'superglobal', 'global', 'void',
	);
	foreach ($superglobals as $superglobal) {
		foreach ($superglobal as $global => $void) {
			if (!in_array($global, $knownglobals))
				unset($GLOBALS[$global]);
		}
	}
}
if ((bool)@ini_get('magic_quotes_gpc')) {
	/**
	 * Falls magic_quotes an sind, Slashes entfernen
	 * wir haben selbst eine Funktion dafür...
	 *
	 * @param mixed $global
	 * @return mixed
	 */
	function acp3_strip($global) {
		$result = is_array($global) ? array_map('acp3_strip', $global) : stripslashes($global);
		return $result;
	}
	$_GET = acp3_strip($_GET);
	$_POST = acp3_strip($_POST);
	$_COOKIE = acp3_strip($_COOKIE);
	$_REQUEST = acp3_strip($_REQUEST);
}
?>