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
		$errors['name'] = ACP3\CMS::$injector['Lang']->t('system', 'name_to_short');
	if (!empty($_POST['name']) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_roles WHERE name = ?', array($_POST['name'])) == 1)
		$errors['name'] = ACP3\CMS::$injector['Lang']->t('permissions', 'role_already_exists');
	if (empty($_POST['privileges']) || is_array($_POST['privileges']) === false)
		$errors[] = ACP3\CMS::$injector['Lang']->t('permissions', 'no_privilege_selected');
	if (!empty($_POST['privileges']) && ACP3\Core\Validate::aclPrivilegesExist($_POST['privileges']) === false)
		$errors[] = ACP3\CMS::$injector['Lang']->t('permissions', 'invalid_privileges');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		ACP3\CMS::$injector['Db']->beginTransaction();

		$insert_values = array(
			'id' => '',
			'name' => ACP3\Core\Functions::str_encode($_POST['name']),
			'parent_id' => $_POST['parent'],
		);

		$nestedSet = new ACP3\Core\NestedSet('acl_roles');
		$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);
		$role_id = ACP3\CMS::$injector['Db']->lastInsertId();

		foreach ($_POST['privileges'] as $module_id => $privileges) {
			foreach ($privileges as $id => $permission) {
				ACP3\CMS::$injector['Db']->insert(DB_PRE . 'acl_rules', array('id' => '', 'role_id' => $role_id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
			}
		}

		ACP3\CMS::$injector['Db']->commit();

		ACP3\Core\ACL::setRolesCache();

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('name' => ''));

	$roles = ACP3\Core\ACL::getAllRoles();
	$c_roles = count($roles);
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['selected'] = ACP3\Core\Functions::selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
		$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
	}
	ACP3\CMS::$injector['View']->assign('parent', $roles);

	$modules = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, name FROM ' . DB_PRE . 'modules WHERE active = 1');
	$c_modules = count($modules);
	$privileges = ACP3\Core\ACL::getAllPrivileges();
	$c_privileges = count($privileges);
	ACP3\CMS::$injector['View']->assign('privileges', $privileges);

	for ($i = 0; $i < $c_modules; ++$i) {
		for ($j = 0; $j < $c_privileges; ++$j) {
			// Für jede Privilegie ein Input-Felder zuweisen
			$select = array();
			$select[0]['value'] = 0;
			$select[0]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
			$select[0]['lang'] = ACP3\CMS::$injector['Lang']->t('permissions', 'deny_access');
			$select[1]['value'] = 1;
			$select[1]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
			$select[1]['lang'] = ACP3\CMS::$injector['Lang']->t('permissions', 'allow_access');
			$select[2]['value'] = 2;
			$select[2]['selected'] = !isset($_POST['submit']) || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
			$select[2]['lang'] = ACP3\CMS::$injector['Lang']->t('permissions', 'inherit_access');
			$privileges[$j]['select'] = $select;
		}
		$name = ACP3\CMS::$injector['Lang']->t($modules[$i]['name'], $modules[$i]['name']);
		$modules[$name] = array(
			'id' => $modules[$i]['id'],
			'privileges' => $privileges,
		);
		unset($modules[$i]);
	}

	ksort($modules);
	ACP3\CMS::$injector['View']->assign('modules', $modules);

	ACP3\CMS::$injector['Session']->generateFormToken();
}
