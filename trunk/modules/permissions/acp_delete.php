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
elseif (ACP3_Validate::deleteEntries(ACP3_CMS::$uri->entries) === true)
	$entries = ACP3_CMS::$uri->entries;

if (!isset($entries)) {
	ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('system', 'confirm_delete'), ACP3_CMS::$uri->route('acp/permissions/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/permissions')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = false;
	$level_undeletable = false;

	$nestedSet = new ACP3_NestedSet('acl_roles');
	foreach ($marked_entries as $entry) {
		if (in_array($entry, array(1, 2, 4)) === true) {
			$level_undeletable = true;
		} else {
			$bool = $nestedSet->deleteNode($entry);
			$bool2 = ACP3_CMS::$db2->delete(DB_PRE . 'acl_rules', array('role_id' => $entry));
			$bool3 = ACP3_CMS::$db2->delete(DB_PRE . 'acl_user_roles', array('role_id' => $entry));
		}
	}

	ACP3_Cache::purge(0, 'acl');

	if ($level_undeletable === true) {
		$text = ACP3_CMS::$lang->t('permissions', 'role_undeletable');
	} else {
		$text = ACP3_CMS::$lang->t('system', $bool !== false && $bool2 !== false && $bool3 !== false ? 'delete_success' : 'delete_error');
	}
	setRedirectMessage($bool && $bool2 && $bool3, $text, 'acp/permissions');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
