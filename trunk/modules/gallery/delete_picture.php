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
elseif (preg_match('/^([\d|]+)$/', $uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('gallery', 'confirm_picture_delete'), uri('acp/gallery/delete_picture/entries_' . $marked_entries), uri('acp/gallery/edit_gallery/id_' . $uri->id));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('COUNT(id)', 'gallery_pictures', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			// Datei ebenfalls löschen
			$picture = $db->select('gallery_id, file', 'gallery_pictures', 'id = \'' . $entry . '\'');
			removeFile('gallery', $picture[0]['file']);

			$bool = $db->delete('gallery_pictures', 'id = \'' . $entry . '\'');
			setGalleryCache($picture[0]['gallery_id']);
		}
	}
	$content = comboBox($bool ? $lang->t('gallery', 'picture_delete_success') : $lang->t('gallery', 'picture_delete_error'), uri(!empty($picture[0]['gallery_id']) ? 'acp/gallery/edit_gallery/id_' . $picture[0]['gallery_id'] : 'acp/gallery'));
}
?>