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

$accounts = ACP3_CMS::$db->select('id, mail, hash', 'newsletter_accounts', 0, 'id DESC', POS, ACP3_CMS::$auth->entries);
$c_accounts = count($accounts);

if ($c_accounts > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'newsletter_accounts')));
	ACP3_CMS::$view->assign('accounts', $accounts);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('newsletter', 'acp_delete_account'));
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('newsletter/acp_list_accounts.tpl'));
