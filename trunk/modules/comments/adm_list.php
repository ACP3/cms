<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$module = $uri->module ? $db->escape($uri->module) : 0;
$tpl->assign('module', $module);

if (empty($module) || !empty($module) && $db->countRows('*', 'comments', 'module = \'' . $module . '\'') == '0') {
	$comments = $db->query('SELECT module FROM ' . $db->prefix . 'comments GROUP BY module LIMIT ' . POS . ',' . $auth->entries);
	$c_comments = count($comments);

	if ($c_comments > 0) {
		$tpl->assign('pagination', pagination($db->query('SELECT COUNT(*) FROM ' . $db->prefix . 'comments GROUP BY module', 1)));
		for ($i = 0; $i < $c_comments; ++$i) {
			$comments[$i]['name'] = $lang->t($comments[$i]['module'], $comments[$i]['module']);
			$comments[$i]['count'] = $db->countRows('*', 'comments', 'module = \'' . $comments[$i]['module'] . '\'');
		}
		$tpl->assign('comments', $comments);
	}

	$content = $tpl->fetch('comments/adm_list_module.html');
} else {
	//Brotkrümelspur
	breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
	breadcrumb::assign($lang->t('comments', 'comments'), uri('acp/comments'));
	breadcrumb::assign($lang->t($module, $module));

	$comments = $db->query('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM ' . $db->prefix . 'comments AS c LEFT JOIN (' . $db->prefix . 'users AS u) ON u.id = c.user_id WHERE c.module = \'' . $module . '\' ORDER BY c.date ASC LIMIT ' . POS . ', ' . $auth->entries);
	$c_comments = count($comments);

	if ($c_comments > 0) {
		$emoticons = false;
		// Emoticons einbinden
		if (modules::check('emoticons', 'functions') == 1) {
			require_once ACP3_ROOT . 'modules/emoticons/functions.php';
			$emoticons = true;
		}

		$tpl->assign('pagination', pagination($db->countRows('*', 'comments', 'module = \'' . $module . '\'')));
		for ($i = 0; $i < $c_comments; ++$i) {
			if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
				$comments[$i]['name'] = $lang->t('users', 'deleted_user');
			}
			$comments[$i]['date'] = $date->format($comments[$i]['date']);
			$comments[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $comments[$i]['message']);
			if ($emoticons) {
				$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
			}
		}
		$tpl->assign('comments', $comments);
	}

	$content = $tpl->fetch('comments/adm_list_comments.html');
}