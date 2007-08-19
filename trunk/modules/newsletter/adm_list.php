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

if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/newsletter/entry.php';
} else {
	$newsletter = $db->select('id, mail, hash', 'nl_accounts', 0, 'id DESC, hash ASC', POS, CONFIG_ENTRIES);

	if (count($newsletter) > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'nl_accounts', 0, 0, 0, 0, 1)));
		$tpl->assign('newsletter', $newsletter);
	}
	$content = $tpl->fetch('newsletter/adm_list.html');
}
?>