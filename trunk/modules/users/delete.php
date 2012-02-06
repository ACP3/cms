<?php
/**
 * Users
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
	view::setContent(comboBox($lang->t('common', 'confirm_delete'), $uri->route('acp/users/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/users')));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = null;
	$admin_user = false;
	$self_delete = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'users', 'id = \'' . $entry . '\'') == '1') {
			if ($entry == '1') {
				$admin_user = true;
			} else {
				// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
				if ($entry == $auth->getUserId()) {
					setcookie('ACP3_AUTH', '', time() - 3600, '/');
					$self_delete = true;
				}
				$bool = $db->delete('users', 'id = \'' . $entry . '\'');
			}
		}
	}
	if ($admin_user) {
		$text = $lang->t('users', 'admin_user_undeletable');
	} else {
		$text = $bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error');
	}
	setRedirectMessage($text, $self_delete ? ROOT_DIR : 'acp/users');
} else {
	$uri->redirect('acp/errors/404');
}
