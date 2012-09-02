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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'menu_items', 'id = ' . ACP3_CMS::$uri->id) == 1) {
	$nestedSet = new ACP3_NestedSet('menu_items', true);
	$nestedSet->order(ACP3_CMS::$uri->id, ACP3_CMS::$uri->action);

	require_once MODULES_DIR . 'menus/functions.php';
	setMenuItemsCache();

	$menu = ACP3_CMS::$db->select('id', 'menus', 'id = ' . ((int) ACP3_CMS::$uri->id));
	ACP3_CMS::$uri->redirect('acp/menus/list_items/id_' . $menu[0]['id']);
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}