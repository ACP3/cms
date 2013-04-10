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
	ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::$view->setContent(confirmBox(ACP3_CMS::$lang->t('system', 'confirm_delete'), ACP3_CMS::$uri->route('acp/gallery/delete_picture/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/gallery/edit/id_' . ACP3_CMS::$uri->id)));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	require_once MODULES_DIR . 'gallery/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($entry)) == 1) {
			// Datei ebenfalls löschen
			$picture = ACP3_CMS::$db2->fetchAssoc('SELECT pic, gallery_id, file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($entry));
			ACP3_CMS::$db2->executeUpdate('UPDATE ' . DB_PRE . 'gallery_pictures SET pic = pic - 1 WHERE pic > ? AND gallery_id = ?', array($picture['pic'], $picture['gallery_id']));
			removePicture($picture['file']);

			$bool = ACP3_CMS::$db2->delete(DB_PRE . 'gallery_pictures', array('id' => $entry));
			ACP3_SEO::deleteUriAlias('gallery/details/id_' . $entry);
			setGalleryCache($picture['gallery_id']);
		}
	}
	setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
