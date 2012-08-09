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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'acl_roles', 'id = \'' . $uri->id . '\'') == 1) {
	if (isset($_POST['submit']) === true) {
		if (empty($_POST['name']))
			$errors['name'] = $lang->t('common', 'name_to_short');
		if (!empty($_POST['name']) && $db->countRows('*', 'acl_roles', 'id != \'' . $uri->id . '\' AND name = \'' . $db->escape($_POST['name']) . '\'') == 1)
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
			$update_values = array(
				'name' => $db->escape($_POST['name']),
				'parent_id' => $uri->id == 1 ? 0 : $_POST['parent'],
			);
			$nestedSet = new ACP3_NestedSet('acl_roles');
			$bool = $nestedSet->EditNode($uri->id, $uri->id == 1 ? '' : (int) $_POST['parent'], $update_values);

			$db->link->beginTransaction();
			// Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugkommen sein könnten
			$db->delete('acl_rules', 'role_id = \'' . $uri->id . '\'');
			foreach ($_POST['privileges'] as $module_id => $privileges) {
				foreach ($privileges as $id => $permission) {
					$db->insert('acl_rules', array('id' => '', 'role_id' => $uri->id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
				}
			}
			$db->link->commit();

			// Cache der ACL zurücksetzen
			ACP3_Cache::purge(0, 'acl');

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/access');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$role = $db->select('name, parent_id, left_id, right_id', 'acl_roles', 'id = \'' . $uri->id . '\'');
		$role[0]['name'] = $db->escape($role[0]['name'], 3);

		if ($uri->id != 1) {
			$roles = ACP3_ACL::getAllRoles();
			$c_roles = count($roles);
			for ($i = 0; $i < $c_roles; ++$i) {
				if ($roles[$i]['left_id'] >= $role[0]['left_id'] && $roles[$i]['right_id'] <= $role[0]['right_id']) {
					unset($roles[$i]);
				} else {
					$roles[$i]['selected'] = selectEntry('roles', $roles[$i]['id'], $role[0]['parent_id']);
					$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
				}
			}
			$tpl->assign('parent', $roles);
		}

		$rules = ACP3_ACL::getRules(array($uri->id));
		$modules = $db->select('id, name', 'modules', 'active = 1');
		$c_modules = count($modules);
		$privileges = ACP3_ACL::getAllPrivileges();
		$c_privileges = count($privileges);
		$tpl->assign('privileges', $privileges);

		for ($i = 0; $i < $c_modules; ++$i) {
			for ($j = 0; $j < $c_privileges; ++$j) {
				$priv_val = isset($rules[$modules[$i]['name']][$privileges[$j]['key']]['permission']) ? $rules[$modules[$i]['name']][$privileges[$j]['key']]['permission'] : 0;
				$select = array();
				$select[0]['value'] = 0;
				$select[0]['selected'] = !isset($_POST['submit']) && $priv_val == 0 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
				$select[0]['lang'] = $lang->t('access', 'deny_access');
				$select[1]['value'] = 1;
				$select[1]['selected'] = !isset($_POST['submit']) && $priv_val == 1 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
				$select[1]['lang'] = $lang->t('access', 'allow_access');
				if ($uri->id != 1) {
					$select[2]['value'] = 2;
					$select[2]['selected'] = !isset($_POST['submit']) && $priv_val == 2 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
					$select[2]['lang'] = $lang->t('access', 'inherit_access');
					//$privileges[$j]['calculated'] = sprintf($lang->t('access', 'calculated_permission'), $rules[$privileges[$j]['key']]['access'] === true ? $lang->t('access', 'allow_access') :  $lang->t('access', 'deny_access'));
				}
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

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $role[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('access/acp_edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
