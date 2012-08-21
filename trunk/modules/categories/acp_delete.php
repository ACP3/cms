<?php
/**
 * Categories
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
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/categories/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/categories')));
} elseif ($uri->action === 'confirmed') {
	require_once MODULES_DIR . 'categories/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	$in_use = false;

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $db->countRows('*', 'categories', 'id = \'' . $entry . '\'') == 1) {
			$category = $db->query('SELECT c.picture, n.name AS module FROM {pre}categories AS c JOIN {pre}modules AS m ON(m.id = c.module_id) WHERE c.id = \'' . $entry . '\'');
			if ($db->countRows('*', $db->escape($category[0]['module'], 3), 'category_id = \'' . $entry . '\'') > 0) {
				$in_use = true;
			} else {
				// Kategoriebild ebenfalls löschen
				removeUploadedFile('categories', $category[0]['picture']);
				$bool = $db->delete('categories', 'id = \'' . $entry . '\'');
				ACP3_Cache::delete('categories_' . $db->escape($category[0]['module'], 3));
			}
		}
	}
	// Cache für die Kategorien neu erstellen
	$mods = $db->query('SELECT m.name AS module FROM {pre}categories AS c JOIN {pre}modules AS m ON(m.id = c.module_id) GROUP BY c.module_id');
	foreach ($mods as $row) {
		setCategoriesCache($db->escape($row['module'], 3));
	}

	if ($in_use === true) {
		$text = $lang->t('categories', 'category_is_in_use');
		$bool = false;
	} else {
		$text = $lang->t('common', $bool !== false ? 'delete_success' : 'delete_error');
	}
	setRedirectMessage($bool, $text, 'acp/categories');
} else {
	$uri->redirect('errors/404');
}
