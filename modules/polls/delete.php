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
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('common', 'confirm_delete'), uri('acp/polls/delete/entries_' . $marked_entries), uri('acp/polls'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = null;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('COUNT(id)', 'poll_question', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$bool = $db->delete('poll_question', 'id = \'' . $entry . '\'');
			$bool2 = $db->delete('poll_answers', 'poll_id = \'' . $entry . '\'');
			$bool3 = $db->delete('poll_votes', 'poll_id = \'' . $entry . '\'');
		}
	}
	$content = comboBox($bool !== null && $bool2 !== null && $bool3 !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), uri('acp/polls'));
}
?>