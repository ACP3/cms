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
elseif (ACP3_Validate::deleteEntries(ACP3_CMS::$uri->entries) === true)
	$entries = ACP3_CMS::$uri->entries;

if (!isset($entries)) {
	ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'no_entries_selected')));
} elseif (ACP3_CMS::$uri->action !== 'confirmed') {
	$marked_entries = implode('|', (array) $entries);
	ACP3_CMS::$view->setContent(confirmBox(ACP3_CMS::$lang->t('system', 'confirm_delete'), ACP3_CMS::$uri->route('acp/menus/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/menus')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	$nestedSet = new ACP3_NestedSet('menu_items', true);
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE id = ?', array($entry)) == 1) {
			// Der Navigationsleiste zugeordnete Menüpunkte ebenfalls löschen
			$items = ACP3_CMS::$db2->fetchAll('SELECT id FROM ' . DB_PRE . 'menu_items WHERE block_id = ?', array($entry));
			foreach ($items as $row) {
				$nestedSet->deleteNode($row['id']);
			}

			$block = ACP3_CMS::$db2->fetchColumn('SELECT index_name FROM ' . DB_PRE . 'menus WHERE id = ?', array($entry));
			$bool = ACP3_CMS::$db2->delete(DB_PRE . 'menus', array('id' => $entry));
			ACP3_Cache::delete('visible_items_' . $block, 'menus');
		}
	}

	require_once MODULES_DIR . 'menus/functions.php';
	setMenuItemsCache();

	setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
