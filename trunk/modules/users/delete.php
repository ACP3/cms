<?php
/**
 * Users
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

if (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = combo_box(lang('users', 'confirm_delete'), uri('acp/users/delete/entries_' . $marked_entries), uri('acp/users'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = false;
	$admin_user = false;
	$session_user = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'users', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			if ($entry == '1') {
				$admin_user = true;
			} else {
				if ($entry == USER_ID) {
					$session_user = true;
				}
				$bool = $db->delete('users', 'id = \'' . $entry . '\'');
			}
		}
	}
	// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
	if ($session_user) {
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time() - 3600, ROOT_DIR);
		}
		setcookie('ACP3_AUTH', '', time() - 3600, ROOT_DIR);

		$_SESSION = array();

		session_destroy();
		$check_admin = true;
	}
	if ($admin_user) {
		$text = lang('users', 'admin_user_undeletable');
	} else {
		$text = $bool ? lang('users', 'delete_success') : lang('users', 'delete_error');
	}
	$content = combo_box($text, $session_user ? ROOT_DIR : uri('acp/users'));
} else {
	redirect('errors/404');
}
?>