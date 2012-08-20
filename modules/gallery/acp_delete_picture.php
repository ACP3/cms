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
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/gallery/delete_picture/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/gallery/edit/id_' . $uri->id)));
} elseif ($uri->action === 'confirmed') {
	require_once MODULES_DIR . 'gallery/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $db->countRows('*', 'gallery_pictures', 'id = \'' . $entry . '\'') == 1) {
			// Datei ebenfalls löschen
			$picture = $db->select('pic, gallery_id, file', 'gallery_pictures', 'id = \'' . $entry . '\'');
			$db->query('UPDATE {pre}gallery_pictures SET pic = pic - 1 WHERE pic > ' . $picture[0]['pic'] . ' AND gallery_id = ' . $picture[0]['gallery_id'], 0);
			removePicture($picture[0]['file']);

			$bool = $db->delete('gallery_pictures', 'id = \'' . $entry . '\'');
			ACP3_SEO::deleteUriAlias('gallery/details/id_' . $entry);
			setGalleryCache($picture[0]['gallery_id']);
		}
	}
	setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery' . (!empty($picture[0]['gallery_id']) ? '/edit/id_' . $picture[0]['gallery_id'] : ''));
} else {
	$uri->redirect('errors/404');
}
