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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments WHERE module_id = ?', array(ACP3_CMS::$uri->id)) > 0) {
	$module = ACP3_CMS::$db2->fetchColumn('SELECT name FROM ' . DB_PRE . 'modules WHERE id = ?', array(ACP3_CMS::$uri->id));

	//Brotkrümelspur
	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t($module, $module));

	$comments = ACP3_CMS::$db2->fetchAll('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM ' . DB_PRE . 'comments AS c LEFT JOIN ' . DB_PRE . 'users AS u ON u.id = c.user_id WHERE c.module_id = ? ORDER BY c.entry_id ASC, c.id ASC', array(ACP3_CMS::$uri->id));
	$c_comments = count($comments);

	if ($c_comments > 0) {
		$can_delete = ACP3_Modules::check('comments', 'acp_delete_comments');
		$config = array(
			'element' => '#acp-table',
			'sort_col' => $can_delete === true ? 5 : 4,
			'sort_dir' => 'asc',
			'hide_col_sort' => $can_delete === true ? 0 : ''
		);
		ACP3_CMS::$view->setContent(datatable($config));

		$settings = ACP3_Config::getSettings('comments');
		// Emoticons einbinden
		$emoticons_active = false;
		if ($settings['emoticons'] == 1) {
			if (ACP3_Modules::check('emoticons', 'functions') === true) {
				require_once MODULES_DIR . 'emoticons/functions.php';
				$emoticons_active = true;
			}
		}

		for ($i = 0; $i < $c_comments; ++$i) {
			if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
				$comments[$i]['name'] = ACP3_CMS::$lang->t('users', 'deleted_user');
			}
			$comments[$i]['date_formatted'] = ACP3_CMS::$date->formatTimeRange($comments[$i]['date']);
			$comments[$i]['message'] = nl2p($comments[$i]['message']);
			if ($emoticons_active === true) {
				$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
			}
		}
		ACP3_CMS::$view->assign('comments', $comments);
		ACP3_CMS::$view->assign('can_delete', $can_delete);
	}
}

ACP3_CMS::$view->appendContent(ACP3_CMS::$view->fetchTemplate('comments/acp_list_comments.tpl'));