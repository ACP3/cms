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

$guestbook = ACP3_CMS::$db2->fetchAll('SELECT id, ip, date, name, message FROM ' . DB_PRE . 'guestbook ORDER BY id DESC');
$c_guestbook = count($guestbook);

if ($c_guestbook > 0) {
	$can_delete = ACP3_Modules::check('guestbook', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 5 : 4,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3_CMS::setContent(datatable($config));

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
		$guestbook[$i]['date'] = ACP3_CMS::$date->format($guestbook[$i]['date']);
		$guestbook[$i]['message'] = nl2p($guestbook[$i]['message']);
		if ($emoticons_active === true) {
			$guestbook[$i]['message'] = emoticonsReplace($guestbook[$i]['message']);
		}
	}
	ACP3_CMS::$view->assign('guestbook', $guestbook);
	ACP3_CMS::$view->assign('can_delete', $can_delete);
}
ACP3_CMS::appendContent(ACP3_CMS::$view->fetchTemplate('guestbook/acp_list.tpl'));
