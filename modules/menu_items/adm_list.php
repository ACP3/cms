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

getRedirectMessage();

require_once MODULES_DIR . 'menu_items/functions.php';

$pages_list = menuItemsList();

if (count($pages_list) > 0) {
	$tpl->assign('pages_list', $pages_list);
	$tpl->assign('can_delete', ACP3_Modules::check('menu_items', 'delete'));
	$tpl->assign('can_order', ACP3_Modules::check('menu_items', 'order'));
}
ACP3_View::setContent(ACP3_View::fetchTemplate('menu_items/adm_list.tpl'));
