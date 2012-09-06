<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$accounts = ACP3_CMS::$db2->fetchAll('SELECT id, mail, hash FROM ' . DB_PRE . 'newsletter_accounts ORDER BY id DESC');
$c_accounts = count($accounts);

if ($c_accounts > 0) {
	$can_delete = ACP3_Modules::check('comments', 'acp_delete_account');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 3 : 2,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3_CMS::setContent(datatable($config));

	ACP3_CMS::$view->assign('accounts', $accounts);
	ACP3_CMS::$view->assign('can_delete', $can_delete);
}
ACP3_CMS::appendContent(ACP3_CMS::$view->fetchTemplate('newsletter/acp_list_accounts.tpl'));
