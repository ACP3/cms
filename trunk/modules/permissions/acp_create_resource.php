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

ACP3_CMS::$breadcrumb
->append(ACP3_CMS::$lang->t('permissions', 'acp_list_resources'), ACP3_CMS::$uri->route('acp/permissions/list_resources'))
->append(ACP3_CMS::$lang->t('permissions', 'acp_create_resource'));

if (isset($_POST['submit']) === true) {
	if (empty($_POST['modules']) || ACP3_Modules::isInstalled($_POST['modules']) === false)
		$errors['modules'] = ACP3_CMS::$lang->t('permissions', 'select_module');
	if (empty($_POST['resource']) || preg_match('=/=', $_POST['resource']) || ACP3_Validate::isInternalURI($_POST['modules'] . '/' . $_POST['resource'] . '/') === false)
		$errors['resource'] = ACP3_CMS::$lang->t('permissions', 'type_in_resource');
	if (empty($_POST['privileges']) || ACP3_Validate::isNumber($_POST['privileges']) === false)
		$errors['privileges'] = ACP3_CMS::$lang->t('permissions', 'select_privilege');
	if (ACP3_Validate::isNumber($_POST['privileges']) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_resources WHERE id = ?', array($_POST['privileges'])) == 0)
		$errors['privileges'] = ACP3_CMS::$lang->t('permissions', 'privilege_does_not_exist');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$mod_id = ACP3_CMS::$db2->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($_POST['modules']));
		$insert_values = array(
			'id' => '',
			'module_id' => $mod_id,
			'page' => $_POST['resource'],
			'params' => '',
			'privilege_id' => $_POST['privileges'],
		);
		$bool = ACP3_CMS::$db2->insert(DB_PRE . 'acl_resources', $insert_values);

		ACP3_ACL::setResourcesCache();

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions/list_resources');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$modules = ACP3_Modules::getActiveModules();
	foreach ($modules as $row) {
		$modules[$row['name']]['selected'] = selectEntry('modules', $row['name']);
	}
	ACP3_CMS::$view->assign('modules', $modules);

	$privileges = ACP3_ACL::getAllPrivileges();
	$c_privileges = count($privileges);
	for ($i = 0; $i < $c_privileges; ++$i) {
		$privileges[$i]['selected'] = selectEntry('privileges', $privileges[$i]['id']);
	}
	ACP3_CMS::$view->assign('privileges', $privileges);

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('resource' => ''));

	ACP3_CMS::$session->generateFormToken();
}