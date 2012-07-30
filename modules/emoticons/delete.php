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
elseif (ACP3_Validate::deleteEntries($uri->entries) === true)
	$entries = $uri->entries;

if (!isset($entries)) {
	ACP3_View::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/emoticons/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/emoticons')));
} elseif ($uri->action === 'confirmed') {
	require_once MODULES_DIR . 'emoticons/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $db->countRows('*', 'emoticons', 'id = \'' . $entry . '\'') == 1) {
			// Datei ebenfalls löschen
			$file = $db->select('img', 'emoticons', 'id = \'' . $entry . '\'');
			removeUploadedFile('emoticons', $file[0]['img']);
			$bool = $db->delete('emoticons', 'id = \'' . $entry . '\'');
		}
	}
	setEmoticonsCache();

	setRedirectMessage($bool !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), 'acp/emoticons');
} else {
	$uri->redirect('errors/404');
}
