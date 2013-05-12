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

ACP3\Core\Functions::getRedirectMessage();

$accounts = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, mail, hash FROM ' . DB_PRE . 'newsletter_accounts ORDER BY id DESC');
$c_accounts = count($accounts);

if ($c_accounts > 0) {
	$can_delete = ACP3\Core\Modules::check('newsletter', 'acp_delete_account');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 3 : 2,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));

	ACP3\CMS::$injector['View']->assign('accounts', $accounts);
	ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
}