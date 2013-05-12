<?php
/**
 * Guestbook
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

use ACP3\Core\Functions;

Functions::getRedirectMessage();

$settings = ACP3\Core\Config::getSettings('guestbook');
ACP3\CMS::$injector['View']->assign('overlay', $settings['overlay']);

$guestbook = ACP3\CMS::$injector['Db']->fetchAll('SELECT g.user_id, u.id AS user_id_real, u.nickname AS user_name, u.website AS user_website, u.mail AS user_mail, g.id, g.date, g.name, g.message, g.website, g.mail FROM ' . DB_PRE . 'guestbook AS g LEFT JOIN ' . DB_PRE . 'users AS u ON(u.id = g.user_id) ' . ($settings['notify'] == 2 ? 'WHERE active = 1' : '') . ' ORDER BY date DESC LIMIT ' . POS . ',' . ACP3\CMS::$injector['Auth']->entries);
$c_guestbook = count($guestbook);

if ($c_guestbook > 0) {
	ACP3\CMS::$injector['View']->assign('pagination', Functions::pagination(ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'guestbook')));

	// Emoticons einbinden
	$emoticons_active = false;
	if ($settings['emoticons'] == 1) {
		$emoticons_active = ACP3\Core\Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1 ? true : false;
		if ($emoticons_active === true) {
			require_once MODULES_DIR . 'emoticons/functions.php';
		}
	}

	for ($i = 0; $i < $c_guestbook; ++$i) {
		$guestbook[$i]['name'] = !empty($guestbook[$i]['user_name']) ? $guestbook[$i]['user_name'] : $guestbook[$i]['name'];
		$guestbook[$i]['date_formatted'] = ACP3\CMS::$injector['Date']->format($guestbook[$i]['date'], $settings['dateformat']);
		$guestbook[$i]['date_iso'] = ACP3\CMS::$injector['Date']->format($guestbook[$i]['date'], 'c');
		$guestbook[$i]['message'] = Functions::nl2p($guestbook[$i]['message']);
		if ($emoticons_active === true) {
			$guestbook[$i]['message'] = emoticonsReplace($guestbook[$i]['message']);
		}
		$guestbook[$i]['website'] = strlen($guestbook[$i]['user_website']) > 2 ? substr($guestbook[$i]['user_website'], 0, -2) : $guestbook[$i]['website'];
		if (!empty($guestbook[$i]['website']) && (bool) preg_match('=^http(s)?://=', $guestbook[$i]['website']) === false)
			$guestbook[$i]['website'] = 'http://' . $guestbook[$i]['website'];

		$guestbook[$i]['mail'] = !empty($guestbook[$i]['user_mail']) ? substr($guestbook[$i]['user_mail'], 0, -2) : $guestbook[$i]['mail'];
	}
	ACP3\CMS::$injector['View']->assign('guestbook', $guestbook);
}