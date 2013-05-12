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
	ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('system', 'confirm_delete'), ACP3\CMS::$injector['URI']->route('acp/gallery/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3\CMS::$injector['URI']->route('acp/gallery')));
} elseif (ACP3\CMS::$injector['URI']->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = false;

	require_once MODULES_DIR . 'gallery/functions.php';

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array($entry)) == 1) {
			// Hochgeladene Bilder löschen
			$pictures = ACP3\CMS::$injector['Db']->fetchAll('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($entry));
			foreach ($pictures as $row) {
				removePicture($row['file']);
			}
			// Galerie Cache löschen
			ACP3\Core\Cache::delete('pics_id_' . $entry, 'gallery');
			ACP3\Core\SEO::deleteUriAlias('gallery/pics/id_' . $entry);
			deletePictureAliases($entry);

			// Fotogalerie mitsamt Bildern löschen
			$bool = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'gallery', array('id' => $entry));
			$bool2 = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'gallery_pictures', array('gallery_id' => $entry));
		}
	}
	ACP3\Core\Functions::setRedirectMessage($bool && $bool2, ACP3\CMS::$injector['Lang']->t('system', $bool !== false && $bool2 !== false ? 'delete_success' : 'delete_error'), 'acp/gallery');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
