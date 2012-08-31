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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'menu_items', 'id = ' . $uri->id) == 1) {
	$nestedSet = new ACP3_NestedSet('menu_items', true);
	$nestedSet->order($uri->id, $uri->action);

	require_once MODULES_DIR . 'menus/functions.php';
	setMenuItemsCache();

	$menu = $db->select('id', 'menus', 'id = ' . ((int) $uri->id));
	$uri->redirect('acp/menus/list_items/id_' . $menu[0]['id']);
} else {
	$uri->redirect('errors/404');
}