<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit();

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (preg_match('/^([\d|]+)$/', $uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('menu_items', 'confirm_delete'), uri('acp/menu_items/delete/entries_' . $marked_entries), uri('acp/menu_items'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = $bool4 = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('COUNT(id)', 'menu_items', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$lr = $db->select('left_id, right_id', 'menu_items', 'id = \'' . $entry . '\'');

			$bool = deleteNode($entry, $lr[0]['left_id'], $lr[0]['right_id']);
		}
	}
	setNavbarCache();

	$content = comboBox($bool ? $lang->t('menu_items', 'delete_success') : $lang->t('menu_items', 'delete_error'), uri('acp/pages'));
}
?>