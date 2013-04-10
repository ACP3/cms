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
elseif (ACP3_Validate::deleteEntries(ACP3_CMS::$uri->entries) === true)
	$entries = ACP3_CMS::$uri->entries;

if (!isset($entries)) {
	ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::$view->setContent(confirmBox(ACP3_CMS::$lang->t('system', 'confirm_delete'), ACP3_CMS::$uri->route('acp/categories/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/categories')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	require_once MODULES_DIR . 'categories/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	$in_use = false;

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array($entry)) == 1) {
			$category = ACP3_CMS::$db2->fetchAssoc('SELECT c.picture, m.name AS module FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array($entry));
			if (ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . $category['module'] . ' WHERE category_id = ?', array($entry)) > 0) {
				$in_use = true;
			} else {
				// Kategoriebild ebenfalls löschen
				removeUploadedFile('categories', $category['picture']);
				$bool = ACP3_CMS::$db2->delete(DB_PRE . 'categories', array('id' => $entry));
			}
		}
	}

	ACP3_Cache::purge('sql', 'categories');

	if ($in_use === true) {
		$text = ACP3_CMS::$lang->t('categories', 'category_is_in_use');
		$bool = false;
	} else {
		$text = ACP3_CMS::$lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
	}
	setRedirectMessage($bool, $text, 'acp/categories');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
