<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (preg_match('/^([\d|]+)$/', $modules->entries))
	$entries = $modules->entries;

if (!isset($entries)) {
	$content = comboBox(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox(lang('gallery', 'confirm_picture_delete'), uri('acp/gallery/delete_picture/entries_' . $marked_entries), uri('acp/gallery/edit_gallery/id_' . $modules->id));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $modules->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('id', 'galpics', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
		// Datei ebenfalls löschen
		$file = $db->select('file', 'galpics', 'id = \'' . $entry . '\'');
		removeFile('gallery', $file[0]['file']);

		$bool = $db->delete('galpics', 'id = \'' . $entry . '\'');
	}
	$content = comboBox($bool ? lang('gallery', 'picture_delete_success') : lang('gallery', 'picture_delete_error'), uri('acp/gallery'));
}
?>