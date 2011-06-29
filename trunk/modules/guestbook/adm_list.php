<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$guestbook = $db->select('id, ip, date, name, message', 'guestbook', 0, 'date DESC', POS, $auth->entries);
$c_guestbook = count($guestbook);

if ($c_guestbook > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'guestbook')));
	$emoticons = false;

	// Emoticons einbinden
	if (modules::check('emoticons', 'functions') == 1) {
		require_once ACP3_ROOT . 'modules/emoticons/functions.php';
		$emoticons = true;
	}

	for ($i = 0; $i < $c_guestbook; ++$i) {
		$guestbook[$i]['date'] = $date->format($guestbook[$i]['date']);
		$guestbook[$i]['name'] = $guestbook[$i]['name'];
		$guestbook[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $db->escape($guestbook[$i]['message'], 3));
		if ($emoticons) {
			$guestbook[$i]['message'] = emoticonsReplace($guestbook[$i]['message']);
		}
	}
	$tpl->assign('guestbook', $guestbook);
}
$content = modules::fetchTemplate('guestbook/adm_list.html');
