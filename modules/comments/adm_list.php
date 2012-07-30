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

$module = $uri->module ? $db->escape($uri->module) : 0;
$tpl->assign('module', $module);

if (empty($module) || !empty($module) && $db->countRows('*', 'comments', 'module = \'' . $module . '\'') == 0) {
	$comments = $db->query('SELECT module FROM {pre}comments GROUP BY module LIMIT ' . POS . ',' . $auth->entries);
	$c_comments = count($comments);

	if ($c_comments > 0) {
		$tpl->assign('pagination', pagination($db->query('SELECT COUNT(DISTINCT module) FROM {pre}comments', 1)));
		for ($i = 0; $i < $c_comments; ++$i) {
			$comments[$i]['name'] = $lang->t($comments[$i]['module'], $comments[$i]['module']);
			$comments[$i]['count'] = $db->countRows('*', 'comments', 'module = \'' . $comments[$i]['module'] . '\'');
		}
		$tpl->assign('comments', $comments);
		$tpl->assign('can_delete', ACP3_Modules::check('comments', 'delete_comments_per_module'));
	}

	ACP3_View::setContent(ACP3_View::fetchTemplate('comments/adm_list_module.tpl'));
} else {
	//Brotkrümelspur
	$breadcrumb->append($lang->t($module, $module));

	$comments = $db->query('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM {pre}comments AS c LEFT JOIN ({pre}users AS u) ON u.id = c.user_id WHERE c.module = \'' . $module . '\' ORDER BY c.date ASC LIMIT ' . POS . ', ' . $auth->entries);
	$c_comments = count($comments);

	$settings = ACP3_Config::getModuleSettings('comments');

	if ($c_comments > 0) {
		// Emoticons einbinden
		$emoticons_active = false;
		if ($settings['emoticons'] == 1) {
			if (ACP3_Modules::check('emoticons', 'functions') === true) {
				require_once MODULES_DIR . 'emoticons/functions.php';
				$emoticons_active = true;
			}
		}

		$tpl->assign('pagination', pagination($db->countRows('*', 'comments', 'module = \'' . $module . '\'')));
		for ($i = 0; $i < $c_comments; ++$i) {
			if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
				$comments[$i]['name'] = $lang->t('users', 'deleted_user');
			}
			$comments[$i]['date'] = $date->format($comments[$i]['date']);
			$comments[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $comments[$i]['message']);
			if ($emoticons_active === true) {
				$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
			}
		}
		$tpl->assign('comments', $comments);
		$tpl->assign('can_delete', ACP3_Modules::check('comments', 'delete_comments'));
	}

	ACP3_View::setContent(ACP3_View::fetchTemplate('comments/adm_list_comments.tpl'));
}