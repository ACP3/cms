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
elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
	$entries = $modules->gen['entries'];

if (!isset($entries)) {
	$content = combo_box(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = combo_box(lang('categories', 'confirm_delete'), uri('acp/categories/delete/entries_' . $marked_entries), uri('acp/categories'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	$in_use = 0;

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'categories', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$category = $db->select('module', 'categories', 'id = \'' . $entry . '\'');
			$c_in_use = $db->select('id', $db->escape($category[0]['module'], 3), 'category_id = \'' . $entry . '\'', 0, 0, 0, 1);
			if ($c_in_use > 0) {
				$in_use = 1;
			} else {
				// Datei ebenfalls löschen
				$file = $db->select('picture', 'categories', 'id = \'' . $entry . '\'');
				if (is_file('uploads/categories/' . $file[0]['picture'])) {
					unlink('uploads/categories/' . $file[0]['picture']);
				}
				$bool = $db->delete('categories', 'id = \'' . $entry . '\'');
			}
		}
	}
	// Cache für die Kategorien neu erstellen
	$com_mods = $db->query('SELECT module FROM ' . CONFIG_DB_PRE . 'categories GROUP BY module');
	foreach ($com_mods as $row) {
		$cache->create('categories_' . $db->escape($row['module'], 3), $db->select('id, name, picture, description', 'categories', 'module = \'' . $db->escape($row['module'], 3) . '\'', 'name ASC'));
	}

	if ($in_use) {
		$text = lang('categories', 'category_is_in_use');
	} else {
		$text = $bool ? lang('categories', 'delete_success') : lang('categories', 'delete_error');
	}
	$content = combo_box($text, uri('acp/categories'));
}
?>