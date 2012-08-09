<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3_Validate::deleteEntries($uri->entries) === true)
	$entries = $uri->entries;

if (!isset($entries)) {
	ACP3_View::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/gallery/delete_gallery/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/gallery')));
} elseif ($uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = false;

	require_once MODULES_DIR . 'gallery/functions.php';

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $db->countRows('*', 'gallery', 'id = \'' . $entry . '\'') == 1) {
			// Hochgeladene Bilder löschen
			$pictures = $db->select('file', 'gallery_pictures', 'gallery_id = \'' . $entry . '\'');
			foreach ($pictures as $row) {
				removePicture($row['file']);
			}
			// Galerie Cache löschen
			ACP3_Cache::delete('gallery_pics_id_' . $entry);
			ACP3_SEO::deleteUriAlias('gallery/pics/id_' . $entry);
			deletePictureAliases($entry);

			// Fotogalerie mitsamt Bildern löschen
			$bool = $db->delete('gallery', 'id = \'' . $entry . '\'');
			$bool2 = $db->delete('gallery_pictures', 'gallery_id = \'' . $entry . '\'', 0);
		}
	}
	setRedirectMessage($bool !== false && $bool2 !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), 'acp/gallery');
} else {
	$uri->redirect('errors/404');
}
