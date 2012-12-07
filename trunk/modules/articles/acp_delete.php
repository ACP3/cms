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
elseif (ACP3_Validate::deleteEntries(ACP3_CMS::$uri->entries) === true)
	$entries = ACP3_CMS::$uri->entries;

if (!isset($entries)) {
	ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('system', 'confirm_delete'), ACP3_CMS::$uri->route('acp/articles/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/articles')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	$nestedSet = new ACP3_NestedSet('menu_items', true);
	foreach ($marked_entries as $entry) {
		$bool = ACP3_CMS::$db2->delete(DB_PRE . 'articles', array('id' => $entry));
		$nestedSet->deleteNode(ACP3_CMS::$db2->fetchColumn('SELECT id FROM ' . DB_PRE . 'menu_items WHERE uri = ?', array('articles/details/id_' . $entry . '/')));

		ACP3_Cache::delete('list_id_' . $entry, 'articles');
		ACP3_SEO::deleteUriAlias('articles/details/id_' . $entry);
	}
	if (ACP3_Modules::isInstalled('menus') === true) {
		require_once MODULES_DIR . 'menus/functions.php';
		setMenuItemsCache();
	}

	setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/articles');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
