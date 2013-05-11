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

$newsletter = ACP3_CMS::$db2->fetchAll('SELECT id, date, title, status FROM ' . DB_PRE . 'newsletters ORDER BY date DESC');
$c_newsletter = count($newsletter);

if ($c_newsletter > 0) {
	$can_delete = ACP3_Modules::check('newsletter', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 1 : 0,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3_CMS::$view->appendContent(datatable($config));

	$search = array('0', '1');
	$replace = array(ACP3_CMS::$lang->t('newsletter', 'not_yet_sent'), ACP3_CMS::$lang->t('newsletter', 'already_sent'));
	for ($i = 0; $i < $c_newsletter; ++$i) {
		$newsletter[$i]['date_formatted'] = ACP3_CMS::$date->formatTimeRange($newsletter[$i]['date']);
		$newsletter[$i]['status'] = str_replace($search, $replace, $newsletter[$i]['status']);
	}
	ACP3_CMS::$view->assign('newsletter', $newsletter);
	ACP3_CMS::$view->assign('can_delete', $can_delete);
	ACP3_CMS::$view->assign('can_send', ACP3_Modules::check('newsletter', 'acp_send'));
}