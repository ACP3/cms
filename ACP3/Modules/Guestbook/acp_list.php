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

ACP3\Core\Functions::getRedirectMessage();

$guestbook = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, ip, date, name, message FROM ' . DB_PRE . 'guestbook ORDER BY date DESC');
$c_guestbook = count($guestbook);

if ($c_guestbook > 0) {
	$can_delete = ACP3\Core\Modules::check('guestbook', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 1 : 0,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));

	$settings = ACP3\Core\Config::getSettings('guestbook');
	// Emoticons einbinden
	$emoticons_active = false;
	if ($settings['emoticons'] == 1) {
		if (ACP3\Core\Modules::check('emoticons', 'functions') === true) {
			require_once MODULES_DIR . 'emoticons/functions.php';
			$emoticons_active = true;
		}
	}

	for ($i = 0; $i < $c_guestbook; ++$i) {
		$guestbook[$i]['date_formatted'] = ACP3\CMS::$injector['Date']->formatTimeRange($guestbook[$i]['date']);
		$guestbook[$i]['message'] = ACP3\Core\Functions::nl2p($guestbook[$i]['message']);
		if ($emoticons_active === true) {
			$guestbook[$i]['message'] = emoticonsReplace($guestbook[$i]['message']);
		}
	}
	ACP3\CMS::$injector['View']->assign('guestbook', $guestbook);
	ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
}