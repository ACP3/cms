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

if (validate::isNumber($uri->id) && $db->countRows('*', 'acl_roles', 'id = \'' . $uri->id . '\'') == '1') {
	if (isset($_POST['form']) === true) {
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (!empty($form['name']) && $db->countRows('*', 'acl_roles', 'id != \'' . $uri->id . '\' AND name = \'' . $db->escape($form['name']) . '\'') == '1')
			$errors[] = $lang->t('access', 'role_already_exists');
		if (empty($form['privileges']) || !is_array($form['privileges']))
			$errors[] = $lang->t('access', 'no_privilege_selected');
		if (!empty($form['privileges']) && !validate::aclPrivilegesExist($form['privileges']))
			$errors[] = $lang->t('access', 'invalid_privileges');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (!validate::formToken()) {
			view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'parent_id' => $uri->id == 1 ? 0 : $form['parent'],
			);
			$bool = aclEditNode($uri->id, $uri->id == 1 ? '' : $form['parent'], $update_values);

			$db->link->beginTransaction();
			// Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugkommen sein könnten
			$db->delete('acl_rules', 'role_id = \'' . $uri->id . '\'');
			foreach ($form['privileges'] as $module_id => $privileges) {
				foreach ($privileges as $id => $permission) {
					$db->insert('acl_rules', array('id' => '', 'role_id' => $uri->id, 'module_id' => $module_id, 'privilege_id' => $id, 'permission' => $permission));
				}
			}
			$db->link->commit();

			// Cache der ACL zurücksetzen
			cache::purge(0, 'acl');

			$session->unsetFormToken();

			setRedirectMessage($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/access');
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		$role = $db->select('name, parent_id, left_id, right_id', 'acl_roles', 'id = \'' . $uri->id . '\'');
		$role[0]['name'] = $db->escape($role[0]['name'], 3);

		if ($uri->id != 1) {
			$roles = acl::getAllRoles();
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

		$rules = acl::getRules(array($uri->id));
		$modules = $db->select('id, name', 'modules', 'active = 1');
		$c_modules = count($modules);
		$privileges = acl::getAllPrivileges();
		$c_privileges = count($privileges);
		$tpl->assign('privileges', $privileges);

		for ($i = 0; $i < $c_modules; ++$i) {
			for ($j = 0; $j < $c_privileges; ++$j) {
				$priv_val = $rules[$modules[$i]['name']][$privileges[$j]['key']]['permission'];
				$select[0]['value'] = 0;
				$select[0]['selected'] = !isset($form) && $priv_val == 0 || isset($form) && $form['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 0 ? ' selected="selected"' : '';
				$select[0]['lang'] = $lang->t('access', 'deny_access');
				$select[1]['value'] = 1;
				$select[1]['selected'] = !isset($form) && $priv_val == 1 || isset($form) && $form['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 1 ? ' selected="selected"' : '';
				$select[1]['lang'] = $lang->t('access', 'allow_access');
				if ($uri->id != 1) {
					$select[2]['value'] = 2;
					$select[2]['selected'] = !isset($form) && $priv_val == 2 || isset($form) && $form['privileges'][$modules[$i]['id']][$privileges[$j]['id']] == 2 ? ' selected="selected"' : '';
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

		$tpl->assign('form', isset($form) ? $form : $role[0]);

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('access/edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
