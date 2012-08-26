<?php
/**
 * Guestbook
 *
 * @author Tino Goratsch
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

	$settings = ACP3_Config::getSettings('guestbook');

	// Emoticons einbinden
	$emoticons_active = false;
	if ($settings['emoticons'] == 1) {
		if (ACP3_Modules::check('emoticons', 'functions') === true) {
			require_once MODULES_DIR . 'emoticons/functions.php';
			$emoticons_active = true;
		}
	}

	for ($i = 0; $i < $c_guestbook; ++$i) {
		$guestbook[$i]['date'] = $date->format($guestbook[$i]['date']);
		$guestbook[$i]['name'] = $db->escape($guestbook[$i]['name'], 3);
		$guestbook[$i]['message'] = nl2p($db->escape($guestbook[$i]['message'], 3));
		if ($emoticons_active === true) {
			$guestbook[$i]['message'] = emoticonsReplace($guestbook[$i]['message']);
		}
	}
	$tpl->assign('guestbook', $guestbook);
	$tpl->assign('can_delete', ACP3_Modules::check('guestbook', 'acp_delete'));
}
ACP3_View::setContent(ACP3_View::fetchTemplate('guestbook/acp_list.tpl'));
