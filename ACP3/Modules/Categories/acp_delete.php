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
elseif (ACP3\Core\Validate::deleteEntries(ACP3\CMS::$injector['URI']->entries) === true)
	$entries = ACP3\CMS::$injector['URI']->entries;

if (!isset($entries)) {
	ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('system', 'confirm_delete'), ACP3\CMS::$injector['URI']->route('acp/categories/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3\CMS::$injector['URI']->route('acp/categories')));
} elseif (ACP3\CMS::$injector['URI']->action === 'confirmed') {
	require_once MODULES_DIR . 'categories/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	$in_use = false;

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array($entry)) == 1) {
			$category = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT c.picture, m.name AS module FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array($entry));
			if (ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . $category['module'] . ' WHERE category_id = ?', array($entry)) > 0) {
				$in_use = true;
			} else {
				// Kategoriebild ebenfalls löschen
				removeUploadedFile('categories', $category['picture']);
				$bool = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'categories', array('id' => $entry));
			}
		}
	}

	ACP3\Core\Cache::purge('sql', 'categories');

	if ($in_use === true) {
		$text = ACP3\CMS::$injector['Lang']->t('categories', 'category_is_in_use');
		$bool = false;
	} else {
		$text = ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error');
	}
	ACP3\Core\Functions::setRedirectMessage($bool, $text, 'acp/categories');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
