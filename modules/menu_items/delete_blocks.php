<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
breadcrumb::assign($lang->t('menu_items', 'menu_items'), uri('acp/menu_items'));
breadcrumb::assign($lang->t('menu_items', 'adm_list_blocks'), uri('acp/menu_items/adm_list_blocks'));
breadcrumb::assign($lang->t('menu_items', 'delete_blocks'));

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('common', 'confirm_delete'), uri('acp/menu_items/delete_blocks/entries_' . $marked_entries), uri('acp/menu_items/adm_list_blocks'));
} elseif (preg_match('/^((\d+)|)*(\d+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = null;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('COUNT(id)', 'menu_items_blocks', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$bool = $db->delete('menu_items_blocks', 'id = \'' . $entry . '\'');
		}
	}

	setNavbarCache();

	$content = comboBox($bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), uri('acp/menu_items/adm_list_blocks'));
}
?>