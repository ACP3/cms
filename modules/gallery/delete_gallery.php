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
	$content = comboBox($lang->t('gallery', 'confirm_delete'), uri('acp/gallery/delete_gallery/entries_' . $marked_entries), uri('acp/gallery'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	$bool2 = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('id', 'gallery', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			// Hochgeladene Bilder löschen
			$pictures = $db->select('file', 'gallery_pictures', 'gallery_id = \'' . $entry . '\'');
			foreach ($pictures as $row) {
				removeFile('gallery', $row['file']);
			}
			// Fotogalerie mitsamt Bildern löschen
			$bool = $db->delete('gallery', 'id = \'' . $entry . '\'');
			$bool2 = $db->delete('gallery_pictures', 'gallery_id = \'' . $entry . '\'', 0);

			// Galerie Cache löschen
			cache::delete('gallery_pics_id_' . $entry);
		}
	}
	$content = comboBox($bool && $bool2 ? $lang->t('gallery', 'delete_success') : $lang->t('gallery', 'delete_error'), uri('acp/gallery'));
}
?>