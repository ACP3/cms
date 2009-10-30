<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
breadcrumb::assign($lang->t('menu_items', 'menu_items'), uri('acp/menu_items'));
breadcrumb::assign($lang->t('menu_items', 'adm_list_blocks'));

$blocks = $db->select('id, index_name, title', 'menu_items_blocks', 0, 'title ASC, index_name ASC', POS, CONFIG_ENTRIES);
$c_blocks = count($blocks);

if ($c_blocks > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'menu_items_blocks')));
	$tpl->assign('blocks', $blocks);
}

$content = $tpl->fetch('menu_items/adm_list_blocks.html');
