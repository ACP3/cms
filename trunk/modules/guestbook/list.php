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

$guestbook = $db->select('date, name, user_id, message, website, mail', 'guestbook', 0, 'id DESC', POS, CONFIG_ENTRIES);
$c_guestbook = count($guestbook);

if ($c_guestbook > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'guestbook')));
	$emoticons = false;

	// Emoticons einbinden
	if (modules::check('emoticons', 'functions')) {
		include_once ACP3_ROOT . 'modules/emoticons/functions.php';
		$emoticons = true;
	}

	for ($i = 0; $i < $c_guestbook; ++$i) {
		$guestbook[$i]['date'] = $date->format($guestbook[$i]['date']);
		$guestbook[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $guestbook[$i]['message']);
		if ($emoticons) {
			$guestbook[$i]['message'] = emoticonsReplace($guestbook[$i]['message']);
		}
		$guestbook[$i]['website'] = $db->escape($guestbook[$i]['website'], 3);
		if (!empty($guestbook[$i]['website']) && strpos($guestbook[$i]['website'], 'http://') === false)
			$guestbook[$i]['website'] = 'http://' . $guestbook[$i]['website'];

		$guestbook[$i]['mail'] = !empty($guestbook[$i]['mail']) ? $guestbook[$i]['mail'] : '';
	}
	$tpl->assign('guestbook', $guestbook);
}
$content = $tpl->fetch('guestbook/list.html');
?>