<?php
/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();


if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) && ACP3_CMS::$db->countRows('*', 'comments', 'module_id = ' . ((int) ACP3_CMS::$uri->id)) > 0) {
	$module = ACP3_CMS::$db->select('name', 'modules', 'id = ' . ((int) ACP3_CMS::$uri->id));

	//Brotkrümelspur
	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t($module[0]['name'], $module[0]['name']));

	$comments = ACP3_CMS::$db->query('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM {pre}comments AS c LEFT JOIN {pre}users AS u ON u.id = c.user_id WHERE c.module_id = ' . ((int) ACP3_CMS::$uri->id) . ' ORDER BY c.date ASC LIMIT ' . POS . ', ' . ACP3_CMS::$auth->entries);
	$c_comments = count($comments);

	$settings = ACP3_Config::getSettings('comments');

	if ($c_comments > 0) {
		// Emoticons einbinden
		$emoticons_active = false;
		if ($settings['emoticons'] == 1) {
			if (ACP3_Modules::check('emoticons', 'functions') === true) {
				require_once MODULES_DIR . 'emoticons/functions.php';
				$emoticons_active = true;
			}
		}

		ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'comments', 'module_id = ' . ((int) ACP3_CMS::$uri->id))));
		for ($i = 0; $i < $c_comments; ++$i) {
			if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
				$comments[$i]['name'] = ACP3_CMS::$lang->t('users', 'deleted_user');
			}
			$comments[$i]['date'] = ACP3_CMS::$date->format($comments[$i]['date']);
			$comments[$i]['message'] = nl2p(ACP3_CMS::$db->escape($comments[$i]['message'], 3));
			if ($emoticons_active === true) {
				$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
			}
		}
		ACP3_CMS::$view->assign('comments', $comments);
		ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('comments', 'acp_delete_comments'));
	}
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('comments/acp_list_comments.tpl'));