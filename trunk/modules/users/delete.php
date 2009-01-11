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
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('common', 'confirm_delete'), uri('acp/users/delete/entries_' . $marked_entries), uri('acp/users'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = null;
	$admin_user = false;
	$self_delete = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('COUNT(id)', 'users', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			if ($entry == '1') {
				$admin_user = true;
			} else {
				// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
				if ($entry == USER_ID) {
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
	$content = comboBox($text, $self_delete ? ROOT_DIR : uri('acp/users'));
}
?>