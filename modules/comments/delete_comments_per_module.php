<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (isset($modules->gen['entries']) && preg_match('/^([\w|]+)$/', $modules->gen['entries']))
	$entries = $modules->gen['entries'];

if (!isset($entries)) {
	$content = comboBox(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = comboBox(lang('comments', 'confirm_delete'), uri('acp/comments/delete_comments_per_module/entries_' . $marked_entries), uri('acp/comments'));
} elseif (preg_match('/^([\w|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && preg_match('/^(\w+)$/', $entry) && $db->select('id', 'comments', 'module = \'' . $entry . '\'', 0, 0, 0, 1) > '0')
		$bool = $db->delete('comments', 'module = \'' . $entry . '\'');
	}
	$content = comboBox($bool ? lang('comments', 'delete_success') : lang('comments', 'delete_error'), uri('acp/comments'));
}
?>