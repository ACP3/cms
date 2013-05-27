<?php

namespace ACP3\Modules\Permissions;

use ACP3\Core;

/**
 * Description of PermissionsAdmin
 *
 * @author Tino Goratsch
 */
class PermissionsAdmin extends Core\ModuleController {

	public function actionCreate() {
		if (isset($_POST['submit']) === true) {
			if (empty($_POST['name']))
				$errors['name'] = Core\Registry::get('Lang')->t('system', 'name_to_short');
			if (!empty($_POST['name']) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_roles WHERE name = ?', array($_POST['name'])) == 1)
				$errors['name'] = Core\Registry::get('Lang')->t('permissions', 'role_already_exists');
			if (empty($_POST['privileges']) || is_array($_POST['privileges']) === false)
				$errors[] = Core\Registry::get('Lang')->t('permissions', 'no_privilege_selected');
			if (!empty($_POST['privileges']) && Core\Validate::aclPrivilegesExist($_POST['privileges']) === false)
				$errors[] = Core\Registry::get('Lang')->t('permissions', 'invalid_privileges');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				Core\Registry::get('Db')->beginTransaction();

				$insert_values = array(
					'id' => '',
					'name' => Core\Functions::strEncode($_POST['name']),
					'parent_id' => $_POST['parent'],
				);

				$nestedSet = new Core\NestedSet('acl_roles');
				$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);
				$role_id = Core\Registry::get('Db')->lastInsertId();

				foreach ($_POST['privileges'] as $module_id => $privileges) {
					foreach ($privileges as $id => $permission) {
						Core\Registry::get('Db')->insert(DB_PRE . 'acl_rules', array('id' => '', 'role_id' => $role_id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
					}
				}

				Core\Registry::get('Db')->commit();

				Core\ACL::setRolesCache();

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('name' => ''));

			$roles = Core\ACL::getAllRoles();
			$c_roles = count($roles);
			for ($i = 0; $i < $c_roles; ++$i) {
				$roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
				$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
			}
			Core\Registry::get('View')->assign('parent', $roles);

			$modules = Core\Registry::get('Db')->fetchAll('SELECT id, name FROM ' . DB_PRE . 'modules WHERE active = 1');
			$c_modules = count($modules);
			$privileges = Core\ACL::getAllPrivileges();
			$c_privileges = count($privileges);
			Core\Registry::get('View')->assign('privileges', $privileges);

			for ($i = 0; $i < $c_modules; ++$i) {
				for ($j = 0; $j < $c_privileges; ++$j) {
					// Für jede Privilegie ein Input-Felder zuweisen
					$select = array();
					$select[0]['value'] = 0;
					$select[0]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
					$select[0]['lang'] = Core\Registry::get('Lang')->t('permissions', 'deny_access');
					$select[1]['value'] = 1;
					$select[1]['selected'] = isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
					$select[1]['lang'] = Core\Registry::get('Lang')->t('permissions', 'allow_access');
					$select[2]['value'] = 2;
					$select[2]['selected'] = !isset($_POST['submit']) || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
					$select[2]['lang'] = Core\Registry::get('Lang')->t('permissions', 'inherit_access');
					$privileges[$j]['select'] = $select;
				}
				$name = Core\Registry::get('Lang')->t($modules[$i]['name'], $modules[$i]['name']);
				$modules[$name] = array(
					'id' => $modules[$i]['id'],
					'privileges' => $privileges,
				);
				unset($modules[$i]);
			}

			ksort($modules);
			Core\Registry::get('View')->assign('modules', $modules);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionCreateResource() {
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('permissions', 'acp_list_resources'), Core\Registry::get('URI')->route('acp/permissions/list_resources'))
				->append(Core\Registry::get('Lang')->t('permissions', 'acp_create_resource'));

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['modules']) || Core\Modules::isInstalled($_POST['modules']) === false)
				$errors['modules'] = Core\Registry::get('Lang')->t('permissions', 'select_module');
			if (empty($_POST['resource']) || preg_match('=/=', $_POST['resource']) || Core\Validate::isInternalURI(strtolower($_POST['modules']) . '/' . $_POST['resource'] . '/') === false)
				$errors['resource'] = Core\Registry::get('Lang')->t('permissions', 'type_in_resource');
			if (empty($_POST['privileges']) || Core\Validate::isNumber($_POST['privileges']) === false)
				$errors['privileges'] = Core\Registry::get('Lang')->t('permissions', 'select_privilege');
			if (Core\Validate::isNumber($_POST['privileges']) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_resources WHERE id = ?', array($_POST['privileges'])) == 0)
				$errors['privileges'] = Core\Registry::get('Lang')->t('permissions', 'privilege_does_not_exist');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$mod_id = Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($_POST['modules']));
				$insert_values = array(
					'id' => '',
					'module_id' => $mod_id,
					'page' => $_POST['resource'],
					'params' => '',
					'privilege_id' => $_POST['privileges'],
				);
				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'acl_resources', $insert_values);

				Core\ACL::setResourcesCache();

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions/list_resources');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$modules = Core\Modules::getActiveModules();
			foreach ($modules as $row) {
				$modules[$row['name']]['selected'] = Core\Functions::selectEntry('modules', $row['name']);
			}
			Core\Registry::get('View')->assign('modules', $modules);

			$privileges = Core\ACL::getAllPrivileges();
			$c_privileges = count($privileges);
			for ($i = 0; $i < $c_privileges; ++$i) {
				$privileges[$i]['selected'] = Core\Functions::selectEntry('privileges', $privileges[$i]['id']);
			}
			Core\Registry::get('View')->assign('privileges', $privileges);

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('resource' => ''));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionDelete() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/permissions/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/permissions')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = $bool2 = $bool3 = false;
			$level_undeletable = false;

			$nestedSet = new Core\NestedSet('acl_roles');
			foreach ($marked_entries as $entry) {
				if (in_array($entry, array(1, 2, 4)) === true) {
					$level_undeletable = true;
				} else {
					$bool = $nestedSet->deleteNode($entry);
					$bool2 = Core\Registry::get('Db')->delete(DB_PRE . 'acl_rules', array('role_id' => $entry));
					$bool3 = Core\Registry::get('Db')->delete(DB_PRE . 'acl_user_roles', array('role_id' => $entry));
				}
			}

			Core\Cache::purge(0, 'acl');

			if ($level_undeletable === true) {
				$text = Core\Registry::get('Lang')->t('permissions', 'role_undeletable');
			} else {
				$text = Core\Registry::get('Lang')->t('system', $bool !== false && $bool2 !== false && $bool3 !== false ? 'delete_success' : 'delete_error');
			}
			Core\Functions::setRedirectMessage($bool && $bool2 && $bool3, $text, 'acp/permissions');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionDeleteResources() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		Core\Registry::get('Breadcrumb')->append(Core\Registry::get('Lang')->t('permissions', 'acp_list_resources'), Core\Registry::get('URI')->route('acp/permissions/acp_list_resources'))
				->append(Core\Registry::get('Lang')->t('permissions', 'delete_resources'));

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/permissions/delete_resources/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/permissions/list_resources')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;

			foreach ($marked_entries as $entry) {
				$bool = Core\Registry::get('Db')->delete(DB_PRE . 'acl_resources', array('id' => $entry));
			}

			Core\ACL::setResourcesCache();

			Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/permissions/list_resources');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_roles WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			if (isset($_POST['submit']) === true) {
				if (empty($_POST['name']))
					$errors['name'] = Core\Registry::get('Lang')->t('system', 'name_to_short');
				if (!empty($_POST['name']) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_roles WHERE id != ? AND name = ?', array(Core\Registry::get('URI')->id, $_POST['name'])) == 1)
					$errors['name'] = Core\Registry::get('Lang')->t('permissions', 'role_already_exists');
				if (empty($_POST['privileges']) || is_array($_POST['privileges']) === false)
					$errors[] = Core\Registry::get('Lang')->t('permissions', 'no_privilege_selected');
				if (!empty($_POST['privileges']) && Core\Validate::aclPrivilegesExist($_POST['privileges']) === false)
					$errors[] = Core\Registry::get('Lang')->t('permissions', 'invalid_privileges');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'name' => Core\Functions::strEncode($_POST['name']),
						'parent_id' => Core\Registry::get('URI')->id == 1 ? 0 : $_POST['parent'],
					);
					$nestedSet = new Core\NestedSet('acl_roles');
					$bool = $nestedSet->EditNode(Core\Registry::get('URI')->id, Core\Registry::get('URI')->id == 1 ? '' : (int) $_POST['parent'], 0, $update_values);

					Core\Registry::get('Db')->beginTransaction();
					// Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugekommen sein könnten
					Core\Registry::get('Db')->delete(DB_PRE . 'acl_rules', array('role_id' => Core\Registry::get('URI')->id));
					foreach ($_POST['privileges'] as $module_id => $privileges) {
						foreach ($privileges as $id => $permission) {
							Core\Registry::get('Db')->insert(DB_PRE . 'acl_rules', array('id' => '', 'role_id' => Core\Registry::get('URI')->id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
						}
					}
					Core\Registry::get('Db')->commit();

					// Cache der ACL zurücksetzen
					Core\Cache::purge(0, 'acl');

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$role = Core\Registry::get('Db')->fetchAssoc('SELECT name, parent_id, left_id, right_id FROM ' . DB_PRE . 'acl_roles WHERE id = ?', array(Core\Registry::get('URI')->id));

				if (Core\Registry::get('URI')->id != 1) {
					$roles = Core\ACL::getAllRoles();
					$c_roles = count($roles);
					for ($i = 0; $i < $c_roles; ++$i) {
						if ($roles[$i]['left_id'] >= $role['left_id'] && $roles[$i]['right_id'] <= $role['right_id']) {
							unset($roles[$i]);
						} else {
							$roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], $role['parent_id']);
							$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
						}
					}
					Core\Registry::get('View')->assign('parent', $roles);
				}

				$rules = Core\ACL::getRules(array(Core\Registry::get('URI')->id));
				$modules = Core\Registry::get('Db')->fetchAll('SELECT id, name FROM ' . DB_PRE . 'modules WHERE active = 1');
				$c_modules = count($modules);
				$privileges = Core\ACL::getAllPrivileges();
				$c_privileges = count($privileges);
				Core\Registry::get('View')->assign('privileges', $privileges);

				for ($i = 0; $i < $c_modules; ++$i) {
					for ($j = 0; $j < $c_privileges; ++$j) {
						$priv_val = isset($rules[$modules[$i]['name']][$privileges[$j]['key']]['permission']) ? $rules[$modules[$i]['name']][$privileges[$j]['key']]['permission'] : 0;
						$select = array();
						$select[0]['value'] = 0;
						$select[0]['selected'] = !isset($_POST['submit']) && $priv_val == 0 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 0 ? ' checked="checked"' : '';
						$select[0]['lang'] = Core\Registry::get('Lang')->t('permissions', 'deny_access');
						$select[1]['value'] = 1;
						$select[1]['selected'] = !isset($_POST['submit']) && $priv_val == 1 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 1 ? ' checked="checked"' : '';
						$select[1]['lang'] = Core\Registry::get('Lang')->t('permissions', 'allow_access');
						if (Core\Registry::get('URI')->id != 1) {
							$select[2]['value'] = 2;
							$select[2]['selected'] = !isset($_POST['submit']) && $priv_val == 2 || isset($_POST['submit']) && $_POST['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 2 ? ' checked="checked"' : '';
							$select[2]['lang'] = Core\Registry::get('Lang')->t('permissions', 'inherit_access');
							//$privileges[$j]['calculated'] = sprintf(Core\Registry::get('Lang')->t('permissions', 'calculated_permission'), $rules[$privileges[$j]['key']]['access'] === true ? Core\Registry::get('Lang')->t('permissions', 'allow_access') :  Core\Registry::get('Lang')->t('permissions', 'deny_access'));
						}
						$privileges[$j]['select'] = $select;
					}
					$name = Core\Registry::get('Lang')->t($modules[$i]['name'], $modules[$i]['name']);
					$modules[$name] = array(
						'id' => $modules[$i]['id'],
						'privileges' => $privileges,
					);
					unset($modules[$i]);
				}

				ksort($modules);
				Core\Registry::get('View')->assign('modules', $modules);

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $role);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEditResource() {
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('permissions', 'acp_list_resources'), Core\Registry::get('URI')->route('acp/permissions/list_resources'))
				->append(Core\Registry::get('Lang')->t('permissions', 'acp_edit_resource'));

		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_resources WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			if (isset($_POST['submit']) === true) {
				if (empty($_POST['modules']) || Core\Modules::isInstalled($_POST['modules']) === false)
					$errors['modules'] = Core\Registry::get('Lang')->t('permissions', 'select_module');
				if (empty($_POST['resource']) || preg_match('=/=', $_POST['resource']) || Core\Validate::isInternalURI($_POST['modules'] . '/' . $_POST['resource'] . '/') === false)
					$errors['resource'] = Core\Registry::get('Lang')->t('permissions', 'type_in_resource');
				if (empty($_POST['privileges']) || Core\Validate::isNumber($_POST['privileges']) === false)
					$errors['privileges'] = Core\Registry::get('Lang')->t('permissions', 'select_privilege');
				if (Core\Validate::isNumber($_POST['privileges']) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_resources WHERE id = ?', array($_POST['privileges'])) == 0)
					$errors['privileges'] = Core\Registry::get('Lang')->t('permissions', 'privilege_does_not_exist');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'page' => $_POST['resource'],
						'privilege_id' => $_POST['privileges'],
					);
					$bool = Core\Registry::get('Db')->update(DB_PRE . 'acl_resources', $update_values, array('id' => Core\Registry::get('URI')->id));

					Core\ACL::setResourcesCache();

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions/list_resources');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$resource = Core\Registry::get('Db')->fetchAssoc('SELECT r.page, r.privilege_id, m.name AS module_name FROM ' . DB_PRE . 'acl_resources AS r JOIN ' . DB_PRE . 'modules AS m ON(m.id = r.module_id) WHERE r.id = ?', array(Core\Registry::get('URI')->id));

				$privileges = Core\ACL::getAllPrivileges();
				$c_privileges = count($privileges);
				for ($i = 0; $i < $c_privileges; ++$i) {
					$privileges[$i]['selected'] = Core\Functions::selectEntry('privileges', $privileges[$i]['id'], $resource['privilege_id']);
				}
				Core\Registry::get('View')->assign('privileges', $privileges);

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('resource' => $resource['page'], 'modules' => $resource['module_name']));

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$roles = Core\ACL::getAllRoles();
		$c_roles = count($roles);

		if ($c_roles > 0) {
			for ($i = 0; $i < $c_roles; ++$i) {
				$roles[$i]['spaces'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']);
			}
			Core\Registry::get('View')->assign('roles', $roles);
			Core\Registry::get('View')->assign('can_delete', Core\Modules::hasPermission('permissions', 'acp_delete'));
			Core\Registry::get('View')->assign('can_order', Core\Modules::hasPermission('permissions', 'acp_order'));
		}
	}

	public function actionListResources() {
		Core\Functions::getRedirectMessage();

		$resources = Core\Registry::get('Db')->fetchAll('SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.privilege_id, p.key AS privilege_name FROM ' . DB_PRE . 'acl_resources AS r JOIN ' . DB_PRE . 'modules AS m ON(r.module_id = m.id) JOIN ' . DB_PRE . 'acl_privileges AS p ON(r.privilege_id = p.id) ORDER BY r.module_id ASC, r.page ASC');
		$c_resources = count($resources);
		$output = array();
		for ($i = 0; $i < $c_resources; ++$i) {
			if (Core\Modules::isActive($resources[$i]['module_name']) === true) {
				$module = Core\Registry::get('Lang')->t($resources[$i]['module_name'], $resources[$i]['module_name']);
				$output[$module][] = $resources[$i];
			}
		}
		ksort($output);
		Core\Registry::get('View')->assign('resources', $output);
		Core\Registry::get('View')->assign('can_delete_resource', Core\Modules::hasPermission('permissions', 'acp_delete_resources'));
	}

	public function actionOrder() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_roles WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$nestedSet = new Core\NestedSet('acl_roles');
			$nestedSet->order(Core\Registry::get('URI')->id, Core\Registry::get('URI')->action);

			Core\Cache::purge(0, 'acl');

			Core\Registry::get('URI')->redirect('acp/permissions');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

}