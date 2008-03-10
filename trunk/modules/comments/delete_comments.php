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
elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
	$entries = $modules->gen['entries'];

if (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = combo_box(lang('comments', 'confirm_delete'), uri('acp/comments/delete_comments/entries_' . $marked_entries), uri('acp/comments'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'comments', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
		$bool = $db->delete('comments', 'id = \'' . $entry . '\'');
	}
	$content = combo_box($bool ? lang('comments', 'delete_success') : lang('comments', 'delete_error'), uri('acp/comments'));
} else {
	redirect('errors/404');
}
?>