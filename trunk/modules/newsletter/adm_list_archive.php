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

$newsletter = $db->select('id, date, subject, text, status', 'newsletter_archive', 0, 'id DESC', POS, $auth->entries);
$c_newsletter = count($newsletter);

if ($c_newsletter > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'newsletter_archive')));

	for ($i = 0; $i < $c_newsletter; ++$i) {
		$newsletter[$i]['date'] = $date->format($newsletter[$i]['date']);
		$newsletter[$i]['subject'] = db::escape($newsletter[$i]['subject'], 3);
		$newsletter[$i]['text'] = str_replace(array("\r\n", "\r", "\n"), '<br />', db::escape($newsletter[$i]['text'], 3));
		$newsletter[$i]['status'] = str_replace(array('0', '1'), array($lang->t('newsletter', 'not_yet_sent'), $lang->t('newsletter', 'already_sent')), $newsletter[$i]['status']);
	}
	$tpl->assign('newsletter', $newsletter);
}
$content = $tpl->fetch('newsletter/adm_list_archive.html');
