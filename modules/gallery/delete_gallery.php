<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	view::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	view::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/gallery/delete_gallery/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/gallery')));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = false;

	require_once MODULES_DIR . 'gallery/functions.php';

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'gallery', 'id = \'' . $entry . '\'') == '1') {
			// Hochgeladene Bilder löschen
			$pictures = $db->select('file', 'gallery_pictures', 'gallery_id = \'' . $entry . '\'');
			foreach ($pictures as $row) {
				removePicture($row['file']);
			}
			// Galerie Cache löschen
			cache::delete('gallery_pics_id_' . $entry);
			seo::deleteUriAlias('gallery/pics/id_' . $entry);
			deletePictureAliases($entry);

			// Fotogalerie mitsamt Bildern löschen
			$bool = $db->delete('gallery', 'id = \'' . $entry . '\'');
			$bool2 = $db->delete('gallery_pictures', 'gallery_id = \'' . $entry . '\'', 0);
		}
	}
	setRedirectMessage($bool !== false && $bool2 !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), 'acp/gallery');
} else {
	$uri->redirect('acp/errors/404');
}
