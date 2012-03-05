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

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3_Validate::deleteEntries($uri->entries) === true)
	$entries = $uri->entries;

if (!isset($entries)) {
	ACP3_View::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/users/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/users')));
} elseif ($uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	$admin_user = false;
	$self_delete = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $db->countRows('*', 'users', 'id = \'' . $entry . '\'') == 1) {
			if ($entry == 1) {
				$admin_user = true;
			} else {
				// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
				if ($entry == $auth->getUserId()) {
					$auth->logout();
					$self_delete = true;
				}
				$bool = $db->delete('users', 'id = \'' . $entry . '\'');
			}
		}
	}
	if ($admin_user === true) {
		$text = $lang->t('users', 'admin_user_undeletable');
	} else {
		$text = $bool !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error');
	}
	setRedirectMessage($text, $self_delete === true ? ROOT_DIR : 'acp/users');
} else {
	$uri->redirect('errors/404');
}
