<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (preg_match('/^([\d|]+)$/', $uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox(lang('access', 'confirm_delete'), uri('acp/access/delete/entries_' . $marked_entries), uri('acp/access'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	$level_undeletable = 0;

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('id', 'access', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			if ($entry == '1' || $entry == '2' || $entry == '3') {
				$level_undeletable = 1;
			} else {
				$bool = $db->delete('access', 'id = \'' . $entry . '\'');
			}
		}
	}
	if ($level_undeletable) {
		$text = lang('access', 'access_level_undeletable');
	} else {
		$text = $bool ? lang('access', 'delete_success') : lang('access', 'delete_error');
	}
	$content = comboBox($text, uri('acp/access'));
}
?>