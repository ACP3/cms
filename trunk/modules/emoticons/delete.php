<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('common', 'confirm_delete'), uri('acp/emoticons/delete/entries_' . $marked_entries), uri('acp/emoticons'));
} elseif (validate::deleteEntries($entries) && $uri->confirmed) {
	require_once ACP3_ROOT . 'modules/emoticons/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = null;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'emoticons', 'id = \'' . $entry . '\'') == '1') {
			// Datei ebenfalls löschen
			$file = $db->select('img', 'emoticons', 'id = \'' . $entry . '\'');
			removeFile('emoticons', $file[0]['img']);
			$bool = $db->delete('emoticons', 'id = \'' . $entry . '\'');
		}
	}
	setEmoticonsCache();

	$content = comboBox($bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), uri('acp/emoticons'));
}
?>