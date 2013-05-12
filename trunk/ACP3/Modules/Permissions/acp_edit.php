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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_roles WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	if (isset($_POST['submit']) === true) {
		if (empty($_POST['name']))
			$errors['name'] = ACP3\CMS::$injector['Lang']->t('system', 'name_to_short');
		if (!empty($_POST['name']) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_roles WHERE id != ? AND name = ?', array(ACP3\CMS::$injector['URI']->id, $_POST['name'])) == 1)
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
			$update_values = array(
				'name' => ACP3\Core\Functions::str_encode($_POST['name']),
				'parent_id' => ACP3\CMS::$injector['URI']->id == 1 ? 0 : $_POST['parent'],
			);
			$nestedSet = new ACP3\Core\NestedSet('acl_roles');
			$bool = $nestedSet->EditNode(ACP3\CMS::$injector['URI']->id, ACP3\CMS::$injector['URI']->id == 1 ? '' : (int) $_POST['parent'], 0, $update_values);

			ACP3\CMS::$injector['Db']->beginTransaction();
			// Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugekommen sein könnten
			ACP3\CMS::$injector['Db']->delete(DB_PRE . 'acl_rules', array('role_id' => ACP3\CMS::$injector['URI']->id));
			foreach ($_POST['privileges'] as $module_id => $privileges) {
				foreach ($privileges as $id => $permission) {
					ACP3\CMS::$injector['Db']->insert(DB_PRE . 'acl_rules', array('id' => '', 'role_id' => ACP3\CMS::$injector['URI']->id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
				}
			}
			ACP3\CMS::$injector['Db']->commit();

			// Cache der ACL zurücksetzen
			ACP3\Core\Cache::purge(0, 'acl');

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$role = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT name, parent_id, left_id, right_id FROM ' . DB_PRE . 'acl_roles WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

		if (ACP3\CMS::$injector['URI']->id != 1) {
			$roles = ACP3\Core\ACL::getAllRoles();
			$c_roles = count($roles);
			for ($i = 0; $i < $c_roles; ++$i) {
				if ($roles[$i]['left_id'] >= $role['left_id'] && $roles[$i]['right_id'] <= $role['right_id']) {
					unset($roles[$i]);
				} else {
					$roles[$i]['selected'] = ACP3\Core\Functions::selectEntry('roles', $roles[$i]['id'], $role['parent_id']);
					$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
				}
			}
			ACP3\CMS::$injector['View']->assign('parent', $roles);
		}

		$rules = ACP3\Core\ACL::getRules(array(ACP3\CMS::$injector['URI']->id));
		$modules = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, name FROM ' . DB_PRE . 'modules WHERE active = 1');
		$c_modules = count($modules);
		$privileges = ACP3\Core\ACL::getAllPrivileges();
		$c_privileges = count($privileges);
		ACP3\CMS::$injector['View']->assign('privileges', $privileges);

		for ($i = 0; $i < $c_modules; ++$i) {
			for ($j = 0; $j < $c_privileges; ++$j) {
				$priv_val = isset($rules[$modules[$i]['name']][$privileges[$j]['key']]['permission']) ? $rules[$modules[$i]['name']][$privileges[$j]['key']]['permission'] : 0;
				$select = array();
				$select[0]['value'] = 0;
				$select[0]['selected'] = !isset($_POST['submit']) && $priv_val == 0 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
				$select[0]['lang'] = ACP3\CMS::$injector['Lang']->t('permissions', 'deny_access');
				$select[1]['value'] = 1;
				$select[1]['selected'] = !isset($_POST['submit']) && $priv_val == 1 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
				$select[1]['lang'] = ACP3\CMS::$injector['Lang']->t('permissions', 'allow_access');
				if (ACP3\CMS::$injector['URI']->id != 1) {
					$select[2]['value'] = 2;
					$select[2]['selected'] = !isset($_POST['submit']) && $priv_val == 2 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
					$select[2]['lang'] = ACP3\CMS::$injector['Lang']->t('permissions', 'inherit_access');
					//$privileges[$j]['calculated'] = sprintf(ACP3\CMS::$injector['Lang']->t('permissions', 'calculated_permission'), $rules[$privileges[$j]['key']]['access'] === true ? ACP3\CMS::$injector['Lang']->t('permissions', 'allow_access') :  ACP3\CMS::$injector['Lang']->t('permissions', 'deny_access'));
				}
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

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $role);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
