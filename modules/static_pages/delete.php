<?php
/**
 * Static Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('common', 'confirm_delete'), uri('acp/static_pages/delete/entries_' . $marked_entries . '/action_confirmed/'), uri('acp/static_pages'));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
	require_once ACP3_ROOT . 'modules/menu_items/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = null;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'static_pages', 'id = \'' . $entry . '\'') == '1') {
			$bool = $db->delete('static_pages', 'id = \'' . $entry . '\'');
			$page = $db->select('id', 'menu_items', 'uri = \'static_pages/list/id_' . $entry . '/\'');
			if (!empty($page))
				deleteNode($page[0]['id']);
			cache::delete('static_pages_list_id_' . $entry);
		}
	}
	setMenuItemsCache();

	$content = comboBox($bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), uri('acp/static_pages'));
} else {
	redirect('acp/errors/404');
}
