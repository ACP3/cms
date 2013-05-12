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

ACP3\Core\Functions::getRedirectMessage();

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments WHERE module_id = ?', array(ACP3\CMS::$injector['URI']->id)) > 0) {
	$module = ACP3\CMS::$injector['Db']->fetchColumn('SELECT name FROM ' . DB_PRE . 'modules WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

	//Brotkrümelspur
	ACP3\CMS::$injector['Breadcrumb']->append(ACP3\CMS::$injector['Lang']->t($module, $module));

	$comments = ACP3\CMS::$injector['Db']->fetchAll('SELECT IF(c.name != "" AND c.user_id = 0,c.name,u.nickname) AS name, c.id, c.ip, c.user_id, c.date, c.message FROM ' . DB_PRE . 'comments AS c LEFT JOIN ' . DB_PRE . 'users AS u ON u.id = c.user_id WHERE c.module_id = ? ORDER BY c.entry_id ASC, c.id ASC', array(ACP3\CMS::$injector['URI']->id));
	$c_comments = count($comments);

	if ($c_comments > 0) {
		$can_delete = ACP3\Core\Modules::check('comments', 'acp_delete_comments');
		$config = array(
			'element' => '#acp-table',
			'sort_col' => $can_delete === true ? 5 : 4,
			'sort_dir' => 'asc',
			'hide_col_sort' => $can_delete === true ? 0 : ''
		);
		ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));

		$settings = ACP3\Core\Config::getSettings('comments');
		// Emoticons einbinden
		$emoticons_active = false;
		if ($settings['emoticons'] == 1) {
			if (ACP3\Core\Modules::check('emoticons', 'functions') === true) {
				require_once MODULES_DIR . 'emoticons/functions.php';
				$emoticons_active = true;
			}
		}

		for ($i = 0; $i < $c_comments; ++$i) {
			if (!empty($comments[$i]['user_id']) && empty($comments[$i]['name'])) {
				$comments[$i]['name'] = ACP3\CMS::$injector['Lang']->t('users', 'deleted_user');
			}
			$comments[$i]['date_formatted'] = ACP3\CMS::$injector['Date']->formatTimeRange($comments[$i]['date']);
			$comments[$i]['message'] = ACP3\Core\Functions::nl2p($comments[$i]['message']);
			if ($emoticons_active === true) {
				$comments[$i]['message'] = emoticonsReplace($comments[$i]['message']);
			}
		}
		ACP3\CMS::$injector['View']->assign('comments', $comments);
		ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
	}
}