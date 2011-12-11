<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$accounts = $db->select('id, mail, hash', 'newsletter_accounts', 0, 'id DESC', POS, $auth->entries);
$c_accounts = count($accounts);

if ($c_accounts > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'newsletter_accounts')));
	
	for ($i = 0; $i < $c_accounts; ++$i) {
		if (!empty($accounts[$i]['hash'])) {
			$accounts[$i]['status'] = '<a href="' . $uri->route('acp/newsletter/adm_activate/id_' . $accounts[$i]['id']) . '" title="' . $lang->t('newsletter', 'activate_account') . '"><img src="' . ROOT_DIR . 'images/crystal/16/cancel.png" alt="" /></a>';
		} else {
			$accounts[$i]['status'] = '<img src="' . ROOT_DIR . 'images/crystal/16/apply.png" alt="" />';
		}
	}
	$tpl->assign('accounts', $accounts);
}
$content = modules::fetchTemplate('newsletter/adm_list.html');
