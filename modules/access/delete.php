<?php
/**
 ** Access Control List
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries) === true)
	$entries = $uri->entries;

if (!isset($entries)) {
	view::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	view::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/access/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/access')));
} elseif (validate::deleteEntries($entries) === true && $uri->action === 'confirmed') {
	require_once MODULES_DIR . 'access/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = null;
	$level_undeletable = false;

	foreach ($marked_entries as $entry) {
		if (in_array($entry, array(1, 2, 4)) === true) {
			$level_undeletable = true;
		} else {
			$bool = aclDeleteNode($entry);
			$bool2 = $db->delete('acl_rules', 'role_id = \'' . $entry . '\'');
			$bool3 = $db->delete('acl_user_roles', 'role_id = \'' . $entry . '\'');
		}
	}

	cache::purge(0, 'acl');

	if ($level_undeletable === true) {
		$text = $lang->t('access', 'role_undeletable');
	} else {
		$text = $bool !== false && $bool2 !== false && $bool3 !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error');
	}
	setRedirectMessage($text, 'acp/access');
} else {
	$uri->redirect('acp/errors/404');
}
