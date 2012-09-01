<?php
/**
 * Articles
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
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/articles/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/articles')));
} elseif ($uri->action === 'confirmed') {
	require_once MODULES_DIR . 'menus/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		$bool = $db->delete('articles', 'id = \'' . $entry . '\'');
		$page = $db->select('id', 'menu_items', 'uri = \'articles/list/id_' . $entry . '/\'');
		if (!empty($page))
			menuItemsDeleteNode($page[0]['id']);
		ACP3_Cache::delete('articles_list_id_' . $entry);
		ACP3_SEO::deleteUriAlias('articles/list/id_' . $entry);
	}
	setMenuItemsCache();

	setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/articles');
} else {
	$uri->redirect('errors/404');
}
