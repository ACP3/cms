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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menu_items WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	$nestedSet = new ACP3\Core\NestedSet('menu_items', true);
	$nestedSet->order(ACP3\CMS::$injector['URI']->id, ACP3\CMS::$injector['URI']->action);

	require_once MODULES_DIR . 'menus/functions.php';
	setMenuItemsCache();

	ACP3\CMS::$injector['URI']->redirect('acp/menus');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}