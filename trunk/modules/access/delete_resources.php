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

$breadcrumb->append($lang->t('access', 'adm_list_resources'), $uri->route('acp/access/adm_list_resources'))
		   ->append($lang->t('access', 'delete_resources'));

if (!isset($entries)) {
	ACP3_View::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/access/delete_resources/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/access/adm_list_resources')));
} elseif ($uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = null;

	foreach ($marked_entries as $entry) {
		$bool = $db->delete('acl_resources', 'id = \'' . $entry . '\'');
	}

	ACP3_ACL::setResourcesCache();

	$text = $bool !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error');
	setRedirectMessage($text, 'acp/access/adm_list_resources');
} else {
	$uri->redirect('errors/404');
}
