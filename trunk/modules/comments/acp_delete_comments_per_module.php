<?php
/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (preg_match('/^([\w|]+)$/', $uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	ACP3_View::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/comments/delete_comments_per_module/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/comments')));
} elseif ($uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		$bool = $db->delete('comments', 'module = \'' . $db->escape($entry) . '\'');
	}
	setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
} else {
	$uri->redirect('errors/404');
}
