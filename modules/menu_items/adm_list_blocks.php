<?php
/**
 * Pages
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$breadcrumb->append($lang->t('menu_items', 'adm_list_blocks'));

getRedirectMessage();

$blocks = $db->select('id, index_name, title', 'menu_items_blocks', 0, 'title ASC, index_name ASC', POS, $auth->entries);
$c_blocks = count($blocks);

if ($c_blocks > 0) {
	for ($i = 0; $i < $c_blocks; ++$i) {
		$blocks[$i]['index_name'] = $db->escape($blocks[$i]['index_name'], 3);
		$blocks[$i]['title'] = $db->escape($blocks[$i]['title'], 3);
	}

	$tpl->assign('pagination', pagination($db->countRows('*', 'menu_items_blocks')));
	$tpl->assign('blocks', $blocks);
	$tpl->assign('can_delete', ACP3_Modules::check('menu_items', 'delete_blocks'));
}

ACP3_View::setContent(ACP3_View::fetchTemplate('menu_items/adm_list_blocks.tpl'));
