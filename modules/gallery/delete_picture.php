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
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('common', 'confirm_delete'), $uri->route('acp/gallery/delete_picture/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/gallery/edit_gallery/id_' . $uri->id));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
	require_once MODULES_DIR . 'gallery/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = null;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'gallery_pictures', 'id = \'' . $entry . '\'') == '1') {
			// Datei ebenfalls löschen
			$picture = $db->select('pic, gallery_id, file', 'gallery_pictures', 'id = \'' . $entry . '\'');
			$db->query('UPDATE {pre}gallery_pictures SET pic = pic - 1 WHERE pic > ' . $picture[0]['pic'] . ' AND gallery_id = ' . $picture[0]['gallery_id'], 0);
			removeFile('gallery', $picture[0]['file']);

			$bool = $db->delete('gallery_pictures', 'id = \'' . $entry . '\'');
			seo::deleteUriAlias('gallery/details/id_' . $entry);
			setGalleryCache($picture[0]['gallery_id']);
		}
	}
	$content = comboBox($bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), $uri->route(!empty($picture[0]['gallery_id']) ? 'acp/gallery/edit_gallery/id_' . $picture[0]['gallery_id'] : 'acp/gallery'));
} else {
	$uri->redirect('acp/errors/404');
}
