<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$breadcrumb->append($lang->t('menu_items', 'adm_list_blocks'), $uri->route('acp/menu_items/adm_list_blocks'))
		   ->append($lang->t('menu_items', 'delete_blocks'));

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3_Validate::deleteEntries($uri->entries) === true)
	$entries = $uri->entries;

if (!isset($entries)) {
	ACP3_View::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/menu_items/delete_blocks/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/menu_items/adm_list_blocks')));
} elseif ($uri->action === 'confirmed') {
	require_once MODULES_DIR . 'menu_items/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $db->countRows('*', 'menu_items_blocks', 'id = \'' . $entry . '\'') == 1) {
			$block = $db->select('index_name', 'menu_items_blocks', 'id = \'' . $entry . '\'');
			ACP3_Cache::delete('visible_menu_items_' . $block[0]['index_name']);

			$bool = $db->delete('menu_items_blocks', 'id = \'' . $entry . '\'');
		}
	}

	setMenuItemsCache();

	setRedirectMessage($bool !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), 'acp/menu_items/adm_list_blocks');
} else {
	$uri->redirect('errors/404');
}
