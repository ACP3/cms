<?php
/**
 * Files
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
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/files/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/files')));
} elseif ($uri->action === 'confirmed') {
	require_once MODULES_DIR . 'files/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $db->countRows('*', 'files', 'id = \'' . $entry . '\'') == 1) {
			// Datei ebenfalls löschen
			$file = $db->select('file', 'files', 'id = \'' . $entry . '\'');
			removeUploadedFile('files', $file[0]['file']);
			$bool = $db->delete('files', 'id = \'' . $entry . '\'');

			ACP3_Cache::delete('files_details_id_' . $entry);
			ACP3_SEO::deleteUriAlias('files/details/id_' . $entry);
		}
	}
	setRedirectMessage($bool !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), 'acp/files');
} else {
	$uri->redirect('errors/404');
}
