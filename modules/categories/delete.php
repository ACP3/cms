<?php
/**
 * Categories
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (preg_match('/^([\d|]+)$/', $modules->entries))
	$entries = $modules->entries;

if (!isset($entries)) {
	$content = comboBox(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox(lang('categories', 'confirm_delete'), uri('acp/categories/delete/entries_' . $marked_entries), uri('acp/categories'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $modules->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	$in_use = 0;

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->isNumber($entry) && $db->select('id', 'categories', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$category = $db->select('picture, module', 'categories', 'id = \'' . $entry . '\'');
			if ($db->select('id', $db->escape($category[0]['module'], 3), 'category_id = \'' . $entry . '\'', 0, 0, 0, 1) > 0) {
				$in_use = 1;
			} else {
				// Kategoriebild ebenfalls löschen
				removeFile('categories', $category[0]['picture']);
				$bool = $db->delete('categories', 'id = \'' . $entry . '\'');
				$cache->delete('categories_' . $db->escape($category[0]['module'], 3));
			}
		}
	}
	// Cache für die Kategorien neu erstellen
	$mods = $db->query('SELECT module FROM ' . CONFIG_DB_PRE . 'categories GROUP BY module');
	foreach ($mods as $row) {
		$cache->create('categories_' . $db->escape($row['module'], 3), $db->select('id, name, picture, description', 'categories', 'module = \'' . $db->escape($row['module'], 3) . '\'', 'name ASC'));
	}

	if ($in_use) {
		$text = lang('categories', 'category_is_in_use');
	} else {
		$text = $bool ? lang('categories', 'delete_success') : lang('categories', 'delete_error');
	}
	$content = comboBox($text, uri('acp/categories'));
}
?>