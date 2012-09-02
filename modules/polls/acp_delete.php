<?php
/**
 * Polls
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3_Validate::deleteEntries(ACP3_CMS::$uri->entries) === true)
	$entries = ACP3_CMS::$uri->entries;

if (!isset($entries)) {
	ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('common', 'confirm_delete'), ACP3_CMS::$uri->route('acp/polls/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/polls')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = false;
	foreach ($marked_entries as $entry) {
		$bool = ACP3_CMS::$db->delete('polls', 'id = \'' . $entry . '\'');
		$bool2 = ACP3_CMS::$db->delete('poll_answers', 'poll_id = \'' . $entry . '\'');
		$bool3 = ACP3_CMS::$db->delete('poll_votes', 'poll_id = \'' . $entry . '\'');
	}
	setRedirectMessage($bool && $bool2 && $bool3, $bool !== false && $bool2 !== false && $bool3 !== false ? ACP3_CMS::$lang->t('common', 'delete_success') : ACP3_CMS::$lang->t('common', 'delete_error'), 'acp/polls');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
