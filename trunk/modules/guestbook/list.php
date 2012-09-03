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

$settings = ACP3_Config::getSettings('guestbook');
ACP3_CMS::$view->assign('overlay', $settings['overlay']);

$guestbook = ACP3_CMS::$db->query('SELECT u.nickname AS user_name, u.website AS user_website, u.mail AS user_mail, g.id, g.date, g.name, g.user_id, g.message, g.website, g.mail FROM {pre}guestbook AS g LEFT JOIN {pre}users AS u ON(u.id = g.user_id) ' . ($settings['notify'] == 2 ? 'WHERE active = 1' : '') . ' ORDER BY date DESC LIMIT ' . POS . ', ' . ACP3_CMS::$auth->entries);
$c_guestbook = count($guestbook);

if ($c_guestbook > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'guestbook')));

	// Emoticons einbinden
	$emoticons_active = false;
	if ($settings['emoticons'] == 1) {
		$emoticons_active = ACP3_Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1 ? true : false;
		if ($emoticons_active === true) {
			require_once MODULES_DIR . 'emoticons/functions.php';
		}
	}

	for ($i = 0; $i < $c_guestbook; ++$i) {
		if (empty($guestbook[$i]['user_name']) && empty($guestbook[$i]['name'])) {
			$guestbook[$i]['name'] = ACP3_CMS::$lang->t('users', 'deleted_user');
			$guestbook[$i]['user_id'] = 0;
		}
		$guestbook[$i]['name'] = ACP3_CMS::$db->escape(!empty($guestbook[$i]['user_name']) ? $guestbook[$i]['user_name'] : $guestbook[$i]['name'], 3);
		$guestbook[$i]['date'] = ACP3_CMS::$date->format($guestbook[$i]['date'], $settings['dateformat']);
		$guestbook[$i]['message'] = nl2p(ACP3_CMS::$db->escape($guestbook[$i]['message'], 3));
		if ($emoticons_active === true) {
			$guestbook[$i]['message'] = emoticonsReplace($guestbook[$i]['message']);
		}
		$guestbook[$i]['website'] = ACP3_CMS::$db->escape(strlen($guestbook[$i]['user_website']) > 2 ? substr($guestbook[$i]['user_website'], 0, -2) : $guestbook[$i]['website'], 3);
		if (!empty($guestbook[$i]['website']) && (bool) preg_match('=^http(s)?://=', $guestbook[$i]['website']) === false)
			$guestbook[$i]['website'] = 'http://' . $guestbook[$i]['website'];

		$guestbook[$i]['mail'] = !empty($guestbook[$i]['user_mail']) ? substr($guestbook[$i]['user_mail'], 0, -2) : $guestbook[$i]['mail'];
	}
	ACP3_CMS::$view->assign('guestbook', $guestbook);
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('guestbook/list.tpl'));