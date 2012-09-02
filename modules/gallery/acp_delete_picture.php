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
elseif (ACP3_Validate::deleteEntries(ACP3_CMS::$uri->entries) === true)
	$entries = ACP3_CMS::$uri->entries;

if (!isset($entries)) {
	ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('common', 'confirm_delete'), ACP3_CMS::$uri->route('acp/gallery/delete_picture/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/gallery/edit/id_' . ACP3_CMS::$uri->id)));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	require_once MODULES_DIR . 'gallery/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3_CMS::$db->countRows('*', 'gallery_pictures', 'id = \'' . $entry . '\'') == 1) {
			// Datei ebenfalls löschen
			$picture = ACP3_CMS::$db->select('pic, gallery_id, file', 'gallery_pictures', 'id = \'' . $entry . '\'');
			ACP3_CMS::$db->query('UPDATE {pre}gallery_pictures SET pic = pic - 1 WHERE pic > ' . $picture[0]['pic'] . ' AND gallery_id = ' . $picture[0]['gallery_id'], 0);
			removePicture($picture[0]['file']);

			$bool = ACP3_CMS::$db->delete('gallery_pictures', 'id = \'' . $entry . '\'');
			ACP3_SEO::deleteUriAlias('gallery/details/id_' . $entry);
			setGalleryCache($picture[0]['gallery_id']);
		}
	}
	setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery' . (!empty($picture[0]['gallery_id']) ? '/edit/id_' . $picture[0]['gallery_id'] : ''));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
