<?php
/**
 * News
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
	ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('common', 'confirm_delete'), ACP3_CMS::$uri->route('acp/news/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/news')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = false;
	foreach ($marked_entries as $entry) {
		$bool = ACP3_CMS::$db->delete('news', 'id = \'' . $entry . '\'');
		$bool2 = ACP3_CMS::$db->delete('comments', 'module = \'news\' AND entry_id = \'' . $entry . '\'');
		// News Cache löschen
		ACP3_Cache::delete('news_details_id_' . $entry);
		ACP3_SEO::deleteUriAlias('news/details/id_' . $entry);
	}
	setRedirectMessage($bool && $bool2, ACP3_CMS::$lang->t('common', $bool !== false && $bool2 !== false ? 'delete_success' : 'delete_error'), 'acp/news');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}