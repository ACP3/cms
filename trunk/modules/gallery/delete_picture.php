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
elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
	$entries = $modules->gen['entries'];

if (!isset($entries)) {
	$content = combo_box(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = combo_box(lang('gallery', 'confirm_picture_delete'), uri('acp/gallery/delete_picture/entries_' . $marked_entries), uri('acp/gallery/edit_gallery/id_' . $modules->id));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'galpics', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
		// Datei ebenfalls löschen
		$file = $db->select('file', 'galpics', 'id = \'' . $entry . '\'');
		if (is_file('uploads/gallery/' . $file[0]['file'])) {
			unlink('uploads/gallery/' . $file[0]['file']);
		}

		// Galerie Cache löschen
		$bool = $db->delete('galpics', 'id = \'' . $entry . '\'');
	}
	$content = combo_box($bool ? lang('gallery', 'picture_delete_success') : lang('gallery', 'picture_delete_error'), uri('acp/gallery'));
}
?>