<?php
/**
 * Comments
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
	view::setContent(comboBox($lang->t('common', 'confirm_delete'), $uri->route('acp/comments/delete_comments/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/comments')));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = null;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'comments', 'id = \'' . $entry . '\'') == '1') {
			$bool = $db->delete('comments', 'id = \'' . $entry . '\'');
		}
	}
	setRedirectMessage($bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), 'acp/comments');
} else {
	$uri->redirect('acp/errors/404');
}
