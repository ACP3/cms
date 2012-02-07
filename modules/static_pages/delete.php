<?php
/**
 * Static Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries) === true)
	$entries = $uri->entries;

if (!isset($entries)) {
	view::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	view::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/static_pages/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/static_pages')));
} elseif (validate::deleteEntries($entries) === true && $uri->action === 'confirmed') {
	require_once MODULES_DIR . 'menu_items/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'static_pages', 'id = \'' . $entry . '\'') == '1') {
			$bool = $db->delete('static_pages', 'id = \'' . $entry . '\'');
			$page = $db->select('id', 'menu_items', 'uri = \'static_pages/list/id_' . $entry . '/\'');
			if (!empty($page))
				menuItemsDeleteNode($page[0]['id']);
			cache::delete('static_pages_list_id_' . $entry);
			seo::deleteUriAlias('static_pages/list/id_' . $entry);
		}
	}
	setMenuItemsCache();

	setRedirectMessage($bool !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), 'acp/static_pages');
} else {
	$uri->redirect('acp/errors/404');
}
