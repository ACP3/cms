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
elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
	$entries = $modules->gen['entries'];

if (!isset($entries)) {
	$content = combo_box(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = combo_box(lang('emoticons', 'confirm_delete'), uri('acp/emoticons/delete/entries_' . $marked_entries), uri('acp/emoticons'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'emoticons', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
		// Datei ebenfalls löschen
		$file = $db->select('img', 'emoticons', 'id = \'' . $entry . '\'');
		if (is_file('uploads/emoticons/' . $file[0]['img'])) {
			unlink('uploads/emoticons/' . $file[0]['img']);
		}
		$bool = $db->delete('emoticons', 'id = \'' . $entry . '\'');
	}
	$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));

	$content = combo_box($bool ? lang('emoticons', 'delete_success') : lang('emoticons', 'delete_error'), uri('acp/emoticons'));
}
?>