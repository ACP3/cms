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
elseif (ACP3\Core\Validate::deleteEntries(ACP3\CMS::$injector['URI']->entries) === true)
	$entries = ACP3\CMS::$injector['URI']->entries;

if (!isset($entries)) {
	ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'no_entries_selected')));
} elseif (ACP3\CMS::$injector['URI']->action !== 'confirmed') {
	$marked_entries = implode('|', (array) $entries);
	ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('system', 'confirm_delete'), ACP3\CMS::$injector['URI']->route('acp/menus/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3\CMS::$injector['URI']->route('acp/menus')));
} elseif (ACP3\CMS::$injector['URI']->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	$nestedSet = new ACP3\Core\NestedSet('menu_items', true);
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE id = ?', array($entry)) == 1) {
			// Der Navigationsleiste zugeordnete Menüpunkte ebenfalls löschen
			$items = ACP3\CMS::$injector['Db']->fetchAll('SELECT id FROM ' . DB_PRE . 'menu_items WHERE block_id = ?', array($entry));
			foreach ($items as $row) {
				$nestedSet->deleteNode($row['id']);
			}

			$block = ACP3\CMS::$injector['Db']->fetchColumn('SELECT index_name FROM ' . DB_PRE . 'menus WHERE id = ?', array($entry));
			$bool = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'menus', array('id' => $entry));
			ACP3\Core\Cache::delete('visible_items_' . $block, 'menus');
		}
	}

	require_once MODULES_DIR . 'menus/functions.php';
	setMenuItemsCache();

	ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/menus');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
