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

$accounts = $db->select('id, mail, hash', 'newsletter_accounts', 0, 'id DESC', POS, $auth->entries);
$c_accounts = count($accounts);

if ($c_accounts > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'newsletter_accounts')));
	$tpl->assign('accounts', $accounts);
	$tpl->assign('can_delete', ACP3_Modules::check('newsletter', 'delete'));
}
ACP3_View::setContent(ACP3_View::fetchTemplate('newsletter/adm_list.tpl'));
