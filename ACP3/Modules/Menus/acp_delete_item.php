<?php
/**
 * Menu bars
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit();

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3\Core\Validate::deleteEntries(ACP3\CMS::$injector['URI']->entries) === true)
	$entries = ACP3\CMS::$injector['URI']->entries;

if (!isset($entries)) {
	ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('system', 'confirm_delete'), ACP3\CMS::$injector['URI']->route('acp/menus/delete_item/entries_' . $marked_entries . '/action_confirmed/'), ACP3\CMS::$injector['URI']->route('acp/menus')));
} elseif (ACP3\CMS::$injector['URI']->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	$nestedSet = new ACP3\Core\NestedSet('menu_items', true);
	foreach ($marked_entries as $entry) {
		// URI-Alias löschen
		$item_uri = ACP3\CMS::$injector['Db']->fetchColumn('SELECT uri FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($entry));
		$bool = $nestedSet->deleteNode($entry);
		ACP3\Core\SEO::deleteUriAlias($item_uri);
	}

	require_once MODULES_DIR . 'menus/functions.php';
	setMenuItemsCache();

	ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
