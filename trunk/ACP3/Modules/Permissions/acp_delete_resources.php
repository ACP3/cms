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

ACP3\CMS::$injector['Breadcrumb']->append(ACP3\CMS::$injector['Lang']->t('permissions', 'acp_list_resources'), ACP3\CMS::$injector['URI']->route('acp/permissions/acp_list_resources'))
		   ->append(ACP3\CMS::$injector['Lang']->t('permissions', 'delete_resources'));

if (!isset($entries)) {
	ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('system', 'confirm_delete'), ACP3\CMS::$injector['URI']->route('acp/permissions/delete_resources/entries_' . $marked_entries . '/action_confirmed/'), ACP3\CMS::$injector['URI']->route('acp/permissions/list_resources')));
} elseif (ACP3\CMS::$injector['URI']->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;

	foreach ($marked_entries as $entry) {
		$bool = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'acl_resources', array('id' => $entry));
	}

	ACP3\Core\ACL::setResourcesCache();

	ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/permissions/list_resources');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
