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
	ACP3_CMS::$view->setContent(confirmBox(ACP3_CMS::$lang->t('system', 'confirm_delete'), ACP3_CMS::$uri->route('acp/gallery/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/gallery')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = false;

	require_once MODULES_DIR . 'gallery/functions.php';

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array($entry)) == 1) {
			// Hochgeladene Bilder löschen
			$pictures = ACP3_CMS::$db2->fetchAll('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($entry));
			foreach ($pictures as $row) {
				removePicture($row['file']);
			}
			// Galerie Cache löschen
			ACP3_Cache::delete('pics_id_' . $entry, 'gallery');
			ACP3_SEO::deleteUriAlias('gallery/pics/id_' . $entry);
			deletePictureAliases($entry);

			// Fotogalerie mitsamt Bildern löschen
			$bool = ACP3_CMS::$db2->delete(DB_PRE . 'gallery', array('id' => $entry));
			$bool2 = ACP3_CMS::$db2->delete(DB_PRE . 'gallery_pictures', array('gallery_id' => $entry));
		}
	}
	setRedirectMessage($bool && $bool2, ACP3_CMS::$lang->t('system', $bool !== false && $bool2 !== false ? 'delete_success' : 'delete_error'), 'acp/gallery');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
