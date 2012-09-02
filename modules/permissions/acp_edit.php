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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'acl_roles', 'id = \'' . ACP3_CMS::$uri->id . '\'') == 1) {
	if (isset($_POST['submit']) === true) {
		if (empty($_POST['name']))
			$errors['name'] = ACP3_CMS::$lang->t('common', 'name_to_short');
		if (!empty($_POST['name']) && ACP3_CMS::$db->countRows('*', 'acl_roles', 'id != \'' . ACP3_CMS::$uri->id . '\' AND name = \'' . ACP3_CMS::$db->escape($_POST['name']) . '\'') == 1)
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
			$update_values = array(
				'name' => ACP3_CMS::$db->escape($_POST['name']),
				'parent_id' => ACP3_CMS::$uri->id == 1 ? 0 : $_POST['parent'],
			);
			$nestedSet = new ACP3_NestedSet('acl_roles');
			$bool = $nestedSet->EditNode(ACP3_CMS::$uri->id, ACP3_CMS::$uri->id == 1 ? '' : (int) $_POST['parent'], 0, $update_values);

			ACP3_CMS::$db->link->beginTransaction();
			// Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugkommen sein könnten
			ACP3_CMS::$db->delete('acl_rules', 'role_id = \'' . ACP3_CMS::$uri->id . '\'');
			foreach ($_POST['privileges'] as $module_id => $privileges) {
				foreach ($privileges as $id => $permission) {
					ACP3_CMS::$db->insert('acl_rules', array('id' => '', 'role_id' => ACP3_CMS::$uri->id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
				}
			}
			ACP3_CMS::$db->link->commit();

			// Cache der ACL zurücksetzen
			ACP3_Cache::purge(0, 'acl');

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$role = ACP3_CMS::$db->select('name, parent_id, left_id, right_id', 'acl_roles', 'id = \'' . ACP3_CMS::$uri->id . '\'');
		$role[0]['name'] = ACP3_CMS::$db->escape($role[0]['name'], 3);

		if (ACP3_CMS::$uri->id != 1) {
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
			ACP3_CMS::$view->assign('parent', $roles);
		}

		$rules = ACP3_ACL::getRules(array(ACP3_CMS::$uri->id));
		$modules = ACP3_CMS::$db->select('id, name', 'modules', 'active = 1');
		$c_modules = count($modules);
		$privileges = ACP3_ACL::getAllPrivileges();
		$c_privileges = count($privileges);
		ACP3_CMS::$view->assign('privileges', $privileges);

		for ($i = 0; $i < $c_modules; ++$i) {
			for ($j = 0; $j < $c_privileges; ++$j) {
				$priv_val = isset($rules[$modules[$i]['name']][$privileges[$j]['key']]['permission']) ? $rules[$modules[$i]['name']][$privileges[$j]['key']]['permission'] : 0;
				$select = array();
				$select[0]['value'] = 0;
				$select[0]['selected'] = !isset($_POST['submit']) && $priv_val == 0 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
				$select[0]['lang'] = ACP3_CMS::$lang->t('permissions', 'deny_access');
				$select[1]['value'] = 1;
				$select[1]['selected'] = !isset($_POST['submit']) && $priv_val == 1 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
				$select[1]['lang'] = ACP3_CMS::$lang->t('permissions', 'allow_access');
				if (ACP3_CMS::$uri->id != 1) {
					$select[2]['value'] = 2;
					$select[2]['selected'] = !isset($_POST['submit']) && $priv_val == 2 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
					$select[2]['lang'] = ACP3_CMS::$lang->t('permissions', 'inherit_access');
					//$privileges[$j]['calculated'] = sprintf(ACP3_CMS::$lang->t('permissions', 'calculated_permission'), $rules[$privileges[$j]['key']]['access'] === true ? ACP3_CMS::$lang->t('permissions', 'allow_access') :  ACP3_CMS::$lang->t('permissions', 'deny_access'));
				}
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

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $role[0]);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('permissions/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
