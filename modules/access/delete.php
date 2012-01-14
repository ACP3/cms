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

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('common', 'confirm_delete'), $uri->route('acp/access/delete/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/access'));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
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
		$text = $bool !== null && $bool2 !== null && $bool3 !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error');
	}
	$content = comboBox($text, $uri->route('acp/access'));
} else {
	$uri->redirect('acp/errors/404');
}
