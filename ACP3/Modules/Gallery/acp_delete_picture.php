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
elseif (ACP3\Core\Validate::deleteEntries(ACP3\CMS::$injector['URI']->entries) === true)
	$entries = ACP3\CMS::$injector['URI']->entries;

if (!isset($entries)) {
	ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('system', 'confirm_delete'), ACP3\CMS::$injector['URI']->route('acp/gallery/delete_picture/entries_' . $marked_entries . '/action_confirmed/'), ACP3\CMS::$injector['URI']->route('acp/gallery/edit/id_' . ACP3\CMS::$injector['URI']->id)));
} elseif (ACP3\CMS::$injector['URI']->action === 'confirmed') {
	require_once MODULES_DIR . 'gallery/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($entry)) == 1) {
			// Datei ebenfalls löschen
			$picture = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT pic, gallery_id, file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($entry));
			ACP3\CMS::$injector['Db']->executeUpdate('UPDATE ' . DB_PRE . 'gallery_pictures SET pic = pic - 1 WHERE pic > ? AND gallery_id = ?', array($picture['pic'], $picture['gallery_id']));
			removePicture($picture['file']);

			$bool = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'gallery_pictures', array('id' => $entry));
			ACP3\Core\SEO::deleteUriAlias('gallery/details/id_' . $entry);
			setGalleryCache($picture['gallery_id']);
		}
	}
	ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
