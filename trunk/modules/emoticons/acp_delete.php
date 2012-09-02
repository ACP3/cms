<?php
/**
 * Emoticons
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
	ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('common', 'confirm_delete'), ACP3_CMS::$uri->route('acp/emoticons/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/emoticons')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	require_once MODULES_DIR . 'emoticons/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3_CMS::$db->countRows('*', 'emoticons', 'id = \'' . $entry . '\'') == 1) {
			// Datei ebenfalls löschen
			$file = ACP3_CMS::$db->select('img', 'emoticons', 'id = \'' . $entry . '\'');
			removeUploadedFile('emoticons', $file[0]['img']);
			$bool = ACP3_CMS::$db->delete('emoticons', 'id = \'' . $entry . '\'');
		}
	}
	setEmoticonsCache();

	setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/emoticons');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
