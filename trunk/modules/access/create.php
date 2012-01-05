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

require_once MODULES_DIR . 'access/functions.php';

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (empty($form['name']))
		$errors[] = $lang->t('common', 'name_to_short');
	if (!empty($form['name']) && $db->countRows('*', 'acl_roles', 'name = \'' . $db->escape($form['name']) . '\'') == '1')
		$errors[] = $lang->t('access', 'role_already_exists');
	if (empty($form['privileges']) || !is_array($form['privileges']))
		$errors[] = $lang->t('access', 'no_privilege_selected');
	if (!empty($form['privileges']) && !validate::aclPrivilegesExist($form['privileges']))
		$errors[] = $lang->t('access', 'invalid_privileges');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		require_once MODULES_DIR . 'access/functions.php';

		$db->link->beginTransaction();

		$insert_values = array(
			'id' => '',
			'name' => $db->escape($form['name']),
			'parent_id' => $form['parent'],
		);

		$bool = aclInsertNode($form['parent'], $insert_values);
		$role_id = $db->link->lastInsertId();

		foreach ($form['privileges'] as $module_id => $privileges) {
			foreach ($privileges as $id => $permission) {
				$db->insert('acl_rules', array('id' => '', 'role_id' => $role_id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
			}
		}

		$db->link->commit();

		acl::setRolesCache();

		$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), $uri->route('acp/access'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : array('name' => ''));

	$roles = acl::getAllRoles();
	$c_roles = count($roles);
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['selected'] = selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
		$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
	}
	$tpl->assign('parent', $roles);

	$modules = $db->select('id, name', 'modules', 'active = 1');
	$c_modules = count($modules);
	$privileges = acl::getAllPrivileges();
	$c_privileges = count($privileges);
	$tpl->assign('privileges', $privileges);

	for ($i = 0; $i < $c_modules; ++$i) {
		for ($j = 0; $j < $c_privileges; ++$j) {
			$select[0]['value'] = 0;
			$select[0]['selected'] = isset($form) && $form['privileges'][$modules[$i]['id']][$privileges[$j]['key']] == 0 ? ' selected="selected"' : '';
			$select[0]['lang'] = $lang->t('access', 'deny_access');
			$select[1]['value'] = 1;
			$select[1]['selected'] = isset($form) && $form['privileges'][$modules[$i]['id']][$privileges[$j]['key']] == 1 ? ' selected="selected"' : '';
			$select[1]['lang'] = $lang->t('access', 'allow_access');
			$select[2]['value'] = 2;
			$select[2]['selected'] = !isset($form) || isset($form) && $form['privileges'][$modules[$i]['id']][$privileges[$j]['key']] == 2 ? ' selected="selected"' : '';
			$select[2]['lang'] = $lang->t('access', 'inherit_access');
			$privileges[$j]['select'] = $select;
		}
		$modules[$i]['privileges'] = $privileges;
		$modules[$i]['name'] = $lang->t($modules[$i]['name'], $modules[$i]['name']);
	}
	$tpl->assign('modules', $modules);

	$content = modules::fetchTemplate('access/create.html');
}
