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
		$errors['name'] = $lang->t('common', 'name_to_short');
	if (!empty($_POST['name']) && $db->countRows('*', 'acl_roles', 'name = \'' . $db->escape($_POST['name']) . '\'') == 1)
		$errors['name'] = $lang->t('access', 'role_already_exists');
	if (empty($_POST['privileges']) || is_array($_POST['privileges']) === false)
		$errors[] = $lang->t('access', 'no_privilege_selected');
	if (!empty($_POST['privileges']) && ACP3_Validate::aclPrivilegesExist($_POST['privileges']) === false)
		$errors[] = $lang->t('access', 'invalid_privileges');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		require_once MODULES_DIR . 'access/functions.php';

		$db->link->beginTransaction();

		$insert_values = array(
			'id' => '',
			'name' => $db->escape($_POST['name']),
			'parent_id' => $_POST['parent'],
		);

		$nestedSet = new ACP3_NestedSet('acl_roles');
		$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);
		$role_id = $db->link->lastInsertId();

		foreach ($_POST['privileges'] as $module_id => $privileges) {
			foreach ($privileges as $id => $permission) {
				$db->insert('acl_rules', array('id' => '', 'role_id' => $role_id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
			}
		}

		$db->link->commit();

		ACP3_ACL::setRolesCache();

		$session->unsetFormToken();

		setRedirectMessage($bool !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), 'acp/access');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('name' => ''));

	$roles = ACP3_ACL::getAllRoles();
	$c_roles = count($roles);
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['selected'] = selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
		$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
	}
	$tpl->assign('parent', $roles);

	$modules = $db->select('id, name', 'modules', 'active = 1');
	$c_modules = count($modules);
	$privileges = ACP3_ACL::getAllPrivileges();
	$c_privileges = count($privileges);
	$tpl->assign('privileges', $privileges);

	for ($i = 0; $i < $c_modules; ++$i) {
		for ($j = 0; $j < $c_privileges; ++$j) {
			// Für jede Privilegie ein Input-Felder zuweisen
			$select = array();
			$select[0]['value'] = 0;
			$select[0]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
			$select[0]['lang'] = $lang->t('access', 'deny_access');
			$select[1]['value'] = 1;
			$select[1]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
			$select[1]['lang'] = $lang->t('access', 'allow_access');
			$select[2]['value'] = 2;
			$select[2]['selected'] = !isset($_POST['submit']) || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
			$select[2]['lang'] = $lang->t('access', 'inherit_access');
			$privileges[$j]['select'] = $select;
		}
		$name = $lang->t($modules[$i]['name'], $modules[$i]['name']);
		$modules[$name] = array(
			'id' => $modules[$i]['id'],
			'privileges' => $privileges,
		);
		unset($modules[$i]);
	}

	ksort($modules);
	$tpl->assign('modules', $modules);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('access/acp_create.tpl'));
}
