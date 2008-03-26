<?php
/**
 * Polls
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
	$content = comboBox(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = comboBox(lang('polls', 'confirm_delete'), uri('acp/polls/delete/entries_' . $marked_entries), uri('acp/polls'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->isNumber($entry) && $db->select('id', 'poll_question', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$bool = $db->delete('poll_question', 'id = \'' . $entry . '\'');
			$bool2 = $db->delete('poll_answers', 'poll_id = \'' . $entry . '\'');
			$bool3 = $db->delete('poll_votes', 'poll_id = \'' . $entry . '\'');
		}
	}
	$content = comboBox($bool && $bool2 && $bool3 ? lang('polls', 'delete_success') : lang('polls', 'delete_error'), uri('acp/polls'));
}
?>