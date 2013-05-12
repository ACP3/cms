<?php
/**
 * Menu bars
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

ACP3\Core\Functions::getRedirectMessage();

require_once MODULES_DIR . 'menus/functions.php';

$menus = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, title, index_name FROM ' . DB_PRE . 'menus');
$c_menus = count($menus);

if ($c_menus > 0) {
	$can_delete_item = ACP3\Core\Modules::check('menus', 'acp_delete_item');
	$can_order_item = ACP3\Core\Modules::check('menus', 'acp_order');
	ACP3\CMS::$injector['View']->assign('can_delete_item', $can_delete_item);
	ACP3\CMS::$injector['View']->assign('can_order_item', $can_order_item);
	ACP3\CMS::$injector['View']->assign('can_delete', ACP3\Core\Modules::check('menus', 'acp_delete'));
	ACP3\CMS::$injector['View']->assign('can_edit', ACP3\Core\Modules::check('menus', 'acp_edit'));
	ACP3\CMS::$injector['View']->assign('colspan', $can_delete_item && $can_order_item ? 5 : ($can_delete_item || $can_order_item ? 4 : 3));

	$pages_list = menuItemsList();
	for ($i = 0; $i < $c_menus; ++$i) {
		if (isset($pages_list[$menus[$i]['index_name']]) === false) {
			$pages_list[$menus[$i]['index_name']]['title'] = $menus[$i]['title'];
			$pages_list[$menus[$i]['index_name']]['menu_id'] = $menus[$i]['id'];
			$pages_list[$menus[$i]['index_name']]['items'] = array();
		}
	}
	ACP3\CMS::$injector['View']->assign('pages_list', $pages_list);
}