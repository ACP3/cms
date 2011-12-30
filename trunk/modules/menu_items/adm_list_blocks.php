<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
breadcrumb::assign($lang->t('menu_items', 'menu_items'), $uri->route('acp/menu_items'));
breadcrumb::assign($lang->t('menu_items', 'adm_list_blocks'));

$blocks = $db->select('id, index_name, title', 'menu_items_blocks', 0, 'title ASC, index_name ASC', POS, $auth->entries);
$c_blocks = count($blocks);

if ($c_blocks > 0) {
	for ($i = 0; $i < $c_blocks; ++$i) {
		$blocks[$i]['index_name'] = $db->escape($blocks[$i]['index_name'], 3);
		$blocks[$i]['title'] = $db->escape($blocks[$i]['title'], 3);
	}

	$tpl->assign('pagination', pagination($db->countRows('*', 'menu_items_blocks')));
	$tpl->assign('blocks', $blocks);
}

$content = modules::fetchTemplate('menu_items/adm_list_blocks.html');
