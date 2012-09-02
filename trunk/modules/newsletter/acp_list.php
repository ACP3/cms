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

$newsletter = ACP3_CMS::$db->select('id, date, subject, status', 'newsletter_archive', 0, 'id DESC', POS, ACP3_CMS::$auth->entries);
$c_newsletter = count($newsletter);

if ($c_newsletter > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'newsletter_archive')));

	for ($i = 0; $i < $c_newsletter; ++$i) {
		$newsletter[$i]['date'] = ACP3_CMS::$date->format($newsletter[$i]['date']);
		$newsletter[$i]['subject'] = ACP3_CMS::$db->escape($newsletter[$i]['subject'], 3);
		$newsletter[$i]['status'] = str_replace(array('0', '1'), array(ACP3_CMS::$lang->t('newsletter', 'not_yet_sent'), ACP3_CMS::$lang->t('newsletter', 'already_sent')), $newsletter[$i]['status']);
	}
	ACP3_CMS::$view->assign('newsletter', $newsletter);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('newsletter', 'acp_delete'));
	ACP3_CMS::$view->assign('can_send', ACP3_Modules::check('newsletter', 'acp_send'));
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('newsletter/acp_list.tpl'));
