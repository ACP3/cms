<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	view::setContent(comboBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	view::setContent(comboBox($lang->t('common', 'confirm_delete'), $uri->route('acp/emoticons/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/emoticons')));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
	require_once MODULES_DIR . 'emoticons/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = null;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'emoticons', 'id = \'' . $entry . '\'') == '1') {
			// Datei ebenfalls löschen
			$file = $db->select('img', 'emoticons', 'id = \'' . $entry . '\'');
			removeUploadedFile('emoticons', $file[0]['img']);
			$bool = $db->delete('emoticons', 'id = \'' . $entry . '\'');
		}
	}
	setEmoticonsCache();

	setRedirectMessage($bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), 'acp/emoticons');
} else {
	$uri->redirect('acp/errors/404');
}
