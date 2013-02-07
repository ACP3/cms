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
elseif (ACP3_Validate::deleteEntries(ACP3_CMS::$uri->entries) === true)
	$entries = ACP3_CMS::$uri->entries;

if (!isset($entries)) {
	ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('system', 'confirm_delete'), ACP3_CMS::$uri->route('acp/menus/delete_item/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/menus')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	$nestedSet = new ACP3_NestedSet('menu_items', true);
	foreach ($marked_entries as $entry) {
		// URI-Alias löschen
		$item_uri = ACP3_CMS::$db2->fetchColumn('SELECT uri FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($entry));
		$bool = $nestedSet->deleteNode($entry);
		ACP3_SEO::deleteUriAlias($item_uri);
	}

	require_once MODULES_DIR . 'menus/functions.php';
	setMenuItemsCache();

	setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
