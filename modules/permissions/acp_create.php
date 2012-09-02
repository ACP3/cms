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

if (isset($_POST['submit']) === true) {
	if (empty($_POST['name']))
		$errors['name'] = ACP3_CMS::$lang->t('common', 'name_to_short');
	if (!empty($_POST['name']) && ACP3_CMS::$db->countRows('*', 'acl_roles', 'name = \'' . ACP3_CMS::$db->escape($_POST['name']) . '\'') == 1)
		$errors['name'] = ACP3_CMS::$lang->t('permissions', 'role_already_exists');
	if (empty($_POST['privileges']) || is_array($_POST['privileges']) === false)
		$errors[] = ACP3_CMS::$lang->t('permissions', 'no_privilege_selected');
	if (!empty($_POST['privileges']) && ACP3_Validate::aclPrivilegesExist($_POST['privileges']) === false)
		$errors[] = ACP3_CMS::$lang->t('permissions', 'invalid_privileges');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
	} else {
		ACP3_CMS::$db->link->beginTransaction();

		$insert_values = array(
			'id' => '',
			'name' => ACP3_CMS::$db->escape($_POST['name']),
			'parent_id' => $_POST['parent'],
		);

		$nestedSet = new ACP3_NestedSet('acl_roles');
		$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);
		$role_id = ACP3_CMS::$db->link->lastInsertId();

		foreach ($_POST['privileges'] as $module_id => $privileges) {
			foreach ($privileges as $id => $permission) {
				ACP3_CMS::$db->insert('acl_rules', array('id' => '', 'role_id' => $role_id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
			}
		}

		ACP3_CMS::$db->link->commit();

		ACP3_ACL::setRolesCache();

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('name' => ''));

	$roles = ACP3_ACL::getAllRoles();
	$c_roles = count($roles);
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['selected'] = selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
		$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
	}
	ACP3_CMS::$view->assign('parent', $roles);

	$modules = ACP3_CMS::$db->select('id, name', 'modules', 'active = 1');
	$c_modules = count($modules);
	$privileges = ACP3_ACL::getAllPrivileges();
	$c_privileges = count($privileges);
	ACP3_CMS::$view->assign('privileges', $privileges);

	for ($i = 0; $i < $c_modules; ++$i) {
		for ($j = 0; $j < $c_privileges; ++$j) {
			// Für jede Privilegie ein Input-Felder zuweisen
			$select = array();
			$select[0]['value'] = 0;
			$select[0]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
			$select[0]['lang'] = ACP3_CMS::$lang->t('permissions', 'deny_access');
			$select[1]['value'] = 1;
			$select[1]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
			$select[1]['lang'] = ACP3_CMS::$lang->t('permissions', 'allow_access');
			$select[2]['value'] = 2;
			$select[2]['selected'] = !isset($_POST['submit']) || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
			$select[2]['lang'] = ACP3_CMS::$lang->t('permissions', 'inherit_access');
			$privileges[$j]['select'] = $select;
		}
		$name = ACP3_CMS::$lang->t($modules[$i]['name'], $modules[$i]['name']);
		$modules[$name] = array(
			'id' => $modules[$i]['id'],
			'privileges' => $privileges,
		);
		unset($modules[$i]);
	}

	ksort($modules);
	ACP3_CMS::$view->assign('modules', $modules);

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('permissions/acp_create.tpl'));
}
