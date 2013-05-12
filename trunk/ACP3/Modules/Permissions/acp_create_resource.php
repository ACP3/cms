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

ACP3\CMS::$injector['Breadcrumb']
->append(ACP3\CMS::$injector['Lang']->t('permissions', 'acp_list_resources'), ACP3\CMS::$injector['URI']->route('acp/permissions/list_resources'))
->append(ACP3\CMS::$injector['Lang']->t('permissions', 'acp_create_resource'));

if (isset($_POST['submit']) === true) {
	if (empty($_POST['modules']) || ACP3\Core\Modules::isInstalled($_POST['modules']) === false)
		$errors['modules'] = ACP3\CMS::$injector['Lang']->t('permissions', 'select_module');
	if (empty($_POST['resource']) || preg_match('=/=', $_POST['resource']) || ACP3\Core\Validate::isInternalURI($_POST['modules'] . '/' . $_POST['resource'] . '/') === false)
		$errors['resource'] = ACP3\CMS::$injector['Lang']->t('permissions', 'type_in_resource');
	if (empty($_POST['privileges']) || ACP3\Core\Validate::isNumber($_POST['privileges']) === false)
		$errors['privileges'] = ACP3\CMS::$injector['Lang']->t('permissions', 'select_privilege');
	if (ACP3\Core\Validate::isNumber($_POST['privileges']) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_resources WHERE id = ?', array($_POST['privileges'])) == 0)
		$errors['privileges'] = ACP3\CMS::$injector['Lang']->t('permissions', 'privilege_does_not_exist');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$mod_id = ACP3\CMS::$injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($_POST['modules']));
		$insert_values = array(
			'id' => '',
			'module_id' => $mod_id,
			'page' => $_POST['resource'],
			'params' => '',
			'privilege_id' => $_POST['privileges'],
		);
		$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'acl_resources', $insert_values);

		ACP3\Core\ACL::setResourcesCache();

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions/list_resources');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$modules = ACP3\Core\Modules::getActiveModules();
	foreach ($modules as $row) {
		$modules[$row['name']]['selected'] = ACP3\Core\Functions::selectEntry('modules', $row['name']);
	}
	ACP3\CMS::$injector['View']->assign('modules', $modules);

	$privileges = ACP3\Core\ACL::getAllPrivileges();
	$c_privileges = count($privileges);
	for ($i = 0; $i < $c_privileges; ++$i) {
		$privileges[$i]['selected'] = ACP3\Core\Functions::selectEntry('privileges', $privileges[$i]['id']);
	}
	ACP3\CMS::$injector['View']->assign('privileges', $privileges);

	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('resource' => ''));

	ACP3\CMS::$injector['Session']->generateFormToken();
}