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
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('common', 'confirm_delete'), uri('acp/categories/delete/entries_' . $marked_entries . '/action_confirmed/'), uri('acp/categories'));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
	require_once ACP3_ROOT . 'modules/categories/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = null;
	$in_use = 0;

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'categories', 'id = \'' . $entry . '\'') == '1') {
			$category = $db->select('picture, module', 'categories', 'id = \'' . $entry . '\'');
			if ($db->countRows('*', db::escape($category[0]['module'], 3), 'category_id = \'' . $entry . '\'') > 0) {
				$in_use = 1;
			} else {
				// Kategoriebild ebenfalls löschen
				removeFile('categories', $category[0]['picture']);
				$bool = $db->delete('categories', 'id = \'' . $entry . '\'');
				cache::delete('categories_' . db::escape($category[0]['module'], 3));
			}
		}
	}
	// Cache für die Kategorien neu erstellen
	$mods = $db->query('SELECT module FROM ' . $db->prefix . 'categories GROUP BY module');
	foreach ($mods as $row) {
		setCategoriesCache(db::escape($row['module'], 3));
	}

	if ($in_use) {
		$text = $lang->t('categories', 'category_is_in_use');
	} else {
		$text = $bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error');
	}
	$content = comboBox($text, uri('acp/categories'));
} else {
	redirect('acp/errors/404');
}
