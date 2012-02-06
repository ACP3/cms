<?php
/**
 * Polls
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
	view::setContent(comboBox($lang->t('common', 'confirm_delete'), $uri->route('acp/polls/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/polls')));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = null;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'polls', 'id = \'' . $entry . '\'') == '1') {
			$bool = $db->delete('polls', 'id = \'' . $entry . '\'');
			$bool2 = $db->delete('poll_answers', 'poll_id = \'' . $entry . '\'');
			$bool3 = $db->delete('poll_votes', 'poll_id = \'' . $entry . '\'');
		}
	}
	view::setContent(comboBox($bool !== null && $bool2 !== null && $bool3 !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), $uri->route('acp/polls')));
} else {
	$uri->redirect('acp/errors/404');
}
