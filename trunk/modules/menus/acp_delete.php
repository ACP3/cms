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

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3_Validate::deleteEntries($uri->entries) === true)
	$entries = $uri->entries;

if (!isset($entries)) {
	ACP3_View::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif ($uri->action !== 'confirmed') {
	$marked_entries = implode('|', (array) $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/menus/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/menus')));
} elseif ($uri->action === 'confirmed') {
	require_once MODULES_DIR . 'menus/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	$nestedSet = new ACP3_NestedSet('menu_items', true);
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $db->countRows('*', 'menus', 'id = \'' . $entry . '\'') == 1) {
			$block = $db->select('index_name', 'menus', 'id = \'' . $entry . '\'');
			ACP3_Cache::delete('visible_menu_items_' . $block[0]['index_name']);

			// Der Navigationsleiste zugeordnete Menüpunkte ebenfalls löschen
			$items = $db->select('id', 'menu_items', 'block_id = ' . ((int) $entry));
			foreach ($items as $row) {
				$nestedSet->deleteNode($row['id']);
			}

			$bool = $db->delete('menus', 'id = \'' . $entry . '\'');
		}
	}

	setMenuItemsCache();

	setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
} else {
	$uri->redirect('errors/404');
}
