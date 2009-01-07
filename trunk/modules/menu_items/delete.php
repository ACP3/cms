<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit();

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('common', 'confirm_delete'), uri('acp/menu_items/delete/entries_' . $marked_entries), uri('acp/menu_items'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = $bool4 = 0;
	foreach ($marked_entries as $entry) {
		$bool = deleteNode($entry);
	}
	setNavbarCache();

	$content = comboBox($bool ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), uri('acp/menu_items'));
}
?>