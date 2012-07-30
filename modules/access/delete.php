<?php
/**
 ** Access Control List
 *
 * @author Tino Goratsch
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
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/access/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/access')));
} elseif ($uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = null;
	$level_undeletable = false;

	$nestedSet = new ACP3_NestedSet('acl_roles');
	foreach ($marked_entries as $entry) {
		if (in_array($entry, array(1, 2, 4)) === true) {
			$level_undeletable = true;
		} else {
			$bool = $nestedSet->deleteNode($entry);
			$bool2 = $db->delete('acl_rules', 'role_id = \'' . $entry . '\'');
			$bool3 = $db->delete('acl_user_roles', 'role_id = \'' . $entry . '\'');
		}
	}

	ACP3_Cache::purge(0, 'acl');

	if ($level_undeletable === true) {
		$text = $lang->t('access', 'role_undeletable');
	} else {
		$text = $bool !== false && $bool2 !== false && $bool3 !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error');
	}
	setRedirectMessage($text, 'acp/access');
} else {
	$uri->redirect('errors/404');
}
