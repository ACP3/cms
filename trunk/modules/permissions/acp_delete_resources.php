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

$breadcrumb->append($lang->t('permissions', 'acp_list_resources'), $uri->route('acp/permissions/acp_list_resources'))
		   ->append($lang->t('permissions', 'delete_resources'));

if (!isset($entries)) {
	ACP3_View::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/permissions/delete_resources/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/permissions/list_resources')));
} elseif ($uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;

	foreach ($marked_entries as $entry) {
		$bool = $db->delete('acl_resources', 'id = \'' . $entry . '\'');
	}

	ACP3_ACL::setResourcesCache();

	setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/permissions/list_resources');
} else {
	$uri->redirect('errors/404');
}
