<?php
/**
 * magic_quotes deaktivieren
 *
 * @package ACP3
 * @subpackage Core
 * @copyright http://www.php.net/manual/faq.misc.php#53961
 */

if (defined('IN_ACP3') === false)
	exit;

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