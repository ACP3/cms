<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

$gb = $db->select('date, name, message, website, mail', 'gb', 0, 'id DESC', POS, CONFIG_ENTRIES);
$c_gb = count($gb);

if ($c_gb > 0) {
	$tpl->assign('pagination', pagination($db->select('id', 'gb', 0, 0, 0, 0, 1)));
	$emoticons = false;

	// Emoticons einbinden
	if ($modules->check('emoticons', 'functions')) {
		include_once 'modules/emoticons/functions.php';
		$emoticons = true;
	}

	for ($i = 0; $i < $c_gb; $i++) {
		$gb[$i]['date'] = date_aligned(1, $gb[$i]['date']);
		$gb[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $gb[$i]['message']);
		if ($emoticons) {
			$gb[$i]['message'] = emoticons_replace($gb[$i]['message']);
		}
		$gb[$i]['website'] = $db->escape($gb[$i]['website'], 3);
		if (!eregi('^(http:\/\/)+(.*)', $gb[$i]['website']))
			$gb[$i]['website'] = 'http://' . $gb[$i]['website'];

		$gb[$i]['mail'] = !empty($gb[$i]['mail']) ? $gb[$i]['mail'] : '';
	}
	$tpl->assign('gb', $gb);
}
$content = $tpl->fetch('gb/list.html');
?>