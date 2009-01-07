<?php
/**
 * News
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
	$content = comboBox($lang->t('common', 'confirm_delete'), uri('acp/news/delete/entries_' . $marked_entries), uri('acp/news'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	$bool2 = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('COUNT(id)', 'news', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$bool = $db->delete('news', 'id = \'' . $entry . '\'');
			$bool2 = $db->delete('comments', 'module = \'news\' AND entry_id = \'' . $entry . '\'');
			// News Cache löschen
			cache::delete('news_details_id_' . $entry);
		}
	}
	$content = comboBox($bool && $bool2 ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), uri('acp/news'));
}
?>