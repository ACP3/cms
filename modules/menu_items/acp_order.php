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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'menu_items', 'id = ' . $uri->id) == 1) {
	$nestedSet = new ACP3_NestedSet('menu_items', true);
	$nestedSet->order($uri->id, $uri->action);

	require_once MODULES_DIR . 'menu_items/functions.php';
	setMenuItemsCache();

	$uri->redirect('acp/menu_items');
} else {
	$uri->redirect('errors/404');
}