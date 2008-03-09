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

$guestbook = $db->select('id, ip, date, name, message', 'guestbook', 0, 'date DESC', POS, CONFIG_ENTRIES);
$c_guestbook = count($guestbook);

if ($c_guestbook > 0) {
	$tpl->assign('pagination', pagination($db->select('id', 'guestbook', 0, 0, 0, 0, 1)));
	$emoticons = false;

	// Emoticons einbinden
	if ($modules->check('emoticons', 'functions')) {
		include_once 'modules/emoticons/functions.php';
		$emoticons = true;
	}

	for ($i = 0; $i < $c_guestbook; $i++) {
		$guestbook[$i]['date'] = date_aligned(1, $guestbook[$i]['date']);
		$guestbook[$i]['name'] = $guestbook[$i]['name'];
		$guestbook[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $guestbook[$i]['message']);
		if ($emoticons) {
			$guestbook[$i]['message'] = emoticons_replace($guestbook[$i]['message']);
		}
	}
	$tpl->assign('guestbook', $guestbook);
}
$content = $tpl->fetch('guestbook/adm_list.html');
?>