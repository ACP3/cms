<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (preg_match('/^([\d|]+)$/', $uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('emoticons', 'confirm_delete'), uri('acp/emoticons/delete/entries_' . $marked_entries), uri('acp/emoticons'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('id', 'emoticons', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			// Datei ebenfalls löschen
			$file = $db->select('img', 'emoticons', 'id = \'' . $entry . '\'');
			removeFile('emoticons', $file[0]['img']);
			$bool = $db->delete('emoticons', 'id = \'' . $entry . '\'');
		}
	}
	cache::create('emoticons', $db->select('code, description, img', 'emoticons'));

	$content = comboBox($bool ? $lang->t('emoticons', 'delete_success') : $lang->t('emoticons', 'delete_error'), uri('acp/emoticons'));
}
?>