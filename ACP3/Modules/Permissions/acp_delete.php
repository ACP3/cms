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
elseif (ACP3\Core\Validate::deleteEntries(ACP3\CMS::$injector['URI']->entries) === true)
	$entries = ACP3\CMS::$injector['URI']->entries;

if (!isset($entries)) {
	ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('system', 'confirm_delete'), ACP3\CMS::$injector['URI']->route('acp/permissions/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3\CMS::$injector['URI']->route('acp/permissions')));
} elseif (ACP3\CMS::$injector['URI']->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = false;
	$level_undeletable = false;

	$nestedSet = new ACP3\Core\NestedSet('acl_roles');
	foreach ($marked_entries as $entry) {
		if (in_array($entry, array(1, 2, 4)) === true) {
			$level_undeletable = true;
		} else {
			$bool = $nestedSet->deleteNode($entry);
			$bool2 = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'acl_rules', array('role_id' => $entry));
			$bool3 = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'acl_user_roles', array('role_id' => $entry));
		}
	}

	ACP3\Core\Cache::purge(0, 'acl');

	if ($level_undeletable === true) {
		$text = ACP3\CMS::$injector['Lang']->t('permissions', 'role_undeletable');
	} else {
		$text = ACP3\CMS::$injector['Lang']->t('system', $bool !== false && $bool2 !== false && $bool3 !== false ? 'delete_success' : 'delete_error');
	}
	ACP3\Core\Functions::setRedirectMessage($bool && $bool2 && $bool3, $text, 'acp/permissions');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
