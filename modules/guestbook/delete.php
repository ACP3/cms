<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
	$entries = $modules->gen['entries'];

if (!isset($entries)) {
	$content = combo_box(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = combo_box(lang('guestbook', 'confirm_delete'), uri('acp/guestbook/delete/entries_' . $marked_entries), uri('acp/guestbook'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->isNumber($entry) && $db->select('id', 'guestbook', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
		$bool = $db->delete('guestbook', 'id = \'' . $entry . '\'');
	}
	$content = combo_box($bool ? lang('guestbook', 'delete_success') : lang('guestbook', 'delete_error'), uri('acp/guestbook'));
}
?>