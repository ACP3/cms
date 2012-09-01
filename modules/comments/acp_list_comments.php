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


if (ACP3_Validate::isNumber($uri->id) && $db->countRows('*', 'comments', 'module_id = ' . ((int) $uri->id)) > 0) {
	$module = $db->select('name', 'modules', 'id = ' . ((int) $uri->id));

	//Brotkrümelspur
	$breadcrumb->append($lang->t($module[0]['name'], $module[0]['name']));

	$comments = $db->query('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM {pre}comments AS c LEFT JOIN {pre}users AS u ON u.id = c.user_id WHERE c.module_id = ' . ((int) $uri->id) . ' ORDER BY c.date ASC LIMIT ' . POS . ', ' . $auth->entries);
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

		$tpl->assign('pagination', pagination($db->countRows('*', 'comments', 'module_id = ' . ((int) $uri->id))));
		for ($i = 0; $i < $c_comments; ++$i) {
			if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
				$comments[$i]['name'] = $lang->t('users', 'deleted_user');
			}
			$comments[$i]['date'] = $date->format($comments[$i]['date']);
			$comments[$i]['message'] = nl2p($db->escape($comments[$i]['message'], 3));
			if ($emoticons_active === true) {
				$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
			}
		}
		$tpl->assign('comments', $comments);
		$tpl->assign('can_delete', ACP3_Modules::check('comments', 'acp_delete_comments'));
	}
}

ACP3_View::setContent(ACP3_View::fetchTemplate('comments/acp_list_comments.tpl'));