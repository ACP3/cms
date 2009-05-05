<?php
/**
 * Files
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
	$content = comboBox($lang->t('common', 'confirm_delete'), uri('acp/files/delete/entries_' . $marked_entries), uri('acp/files'));
} elseif (validate::deleteEntries($entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = null;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'files', 'id = \'' . $entry . '\'') == '1') {
			// Datei ebenfalls löschen
			$file = $db->select('file', 'files', 'id = \'' . $entry . '\'');
			removeFile('files', $file[0]['file']);
			$bool = $db->delete('files', 'id = \'' . $entry . '\'');

			cache::delete('files_details_id_' . $entry);
		}
	}
	$content = comboBox($bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), uri('acp/files'));
}
?>