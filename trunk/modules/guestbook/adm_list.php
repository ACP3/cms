<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$guestbook = $db->select('id, ip, date, name, message', 'guestbook', 0, 'date DESC', POS, $auth->entries);
$c_guestbook = count($guestbook);

if ($c_guestbook > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'guestbook')));

	$settings = config::getModuleSettings('guestbook');

	// Emoticons einbinden
	$emoticons_active = false;
	if ($settings['emoticons'] == 1) {
		if (modules::check('emoticons', 'functions') === true) {
			require_once MODULES_DIR . 'emoticons/functions.php';
			$emoticons_active = true;
		}
	}

	for ($i = 0; $i < $c_guestbook; ++$i) {
		$guestbook[$i]['date'] = $date->format($guestbook[$i]['date']);
		$guestbook[$i]['name'] = $db->escape($guestbook[$i]['name'], 3);
		$guestbook[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $db->escape($guestbook[$i]['message'], 3));
		if ($emoticons_active === true) {
			$guestbook[$i]['message'] = emoticonsReplace($guestbook[$i]['message']);
		}
	}
	$tpl->assign('guestbook', $guestbook);
}
view::setContent(view::fetchTemplate('guestbook/adm_list.tpl'));
