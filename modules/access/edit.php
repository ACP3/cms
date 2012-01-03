<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

require_once MODULES_DIR . 'access/functions.php';

if (validate::isNumber($uri->id) && $db->countRows('*', 'acl_roles', 'id = \'' . $uri->id . '\'') == '1') {
	if (isset($_POST['form'])) {
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (!empty($form['name']) && $db->countRows('*', 'acl_roles', 'id != \'' . $uri->id . '\' AND name = \'' . $db->escape($form['name']) . '\'') == '1')
			$errors[] = $lang->t('access', 'role_already_exists');
		if (empty($form['privileges']) || !is_array($form['privileges']))
			$errors[] = $lang->t('access', 'no_privilege_selected');
		if (!empty($form['privileges']) && !validate::aclPrivilegesExist($form['privileges']))
			$errors[] = $lang->t('access', 'invalid_privileges');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
			);
			$bool = $db->update('acl_roles', $update_values, 'id = \'' . $uri->id . '\'');

			$db->link->beginTransaction();
			// Bestehende Berechtigungen löschen, da in der Zwischenzeit neue hinzugkommen sein könnten
			$db->delete('acl_role_privileges', 'role_id = \'' . $uri->id . '\'');
			foreach ($form['privileges'] as $id => $value) {
				$db->insert('acl_role_privileges', array('id' => '', 'role_id' => $uri->id, 'privilege_id' => $id, 'value' => $value));
			}
			$db->link->commit();

			// Cache der ACl zurücksetzen
			cache::purge(0, 0, 'acl');

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), $uri->route('acp/access'));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$role = $db->select('name, left_id, right_id', 'acl_roles', 'id = \'' . $uri->id . '\'');
		$role[0]['name'] = $db->escape($role[0]['name'], 3);

		if ($uri->id != 1) {
			$roles = $acl->getAllRoles();
			$c_roles = count($roles);
			$parent = $db->select('id', 'acl_roles', 'left_id < ' . $role[0]['left_id'] . ' AND right_id > ' . $role[0]['right_id'], 'left_id DESC', 1);
			for ($i = 0; $i < $c_roles; ++$i) {
				if ($roles[$i]['left_id'] >= $role[0]['left_id'] && $roles[$i]['right_id'] <= $role[0]['right_id']) {
					unset($roles[$i]);
				} else {
					$roles[$i]['selected'] = selectEntry('roles', $roles[$i]['id'], !empty($parent[0]['id']) ? $parent[0]['id'] : 0);
					$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
				}
			}
			$tpl->assign('roles', $roles);
		}

		$role_privileges = $acl->getRolePrivileges(array($uri->id));
		$privileges = $acl->getAllPrivileges();
		$c_privileges = count($privileges);
		for ($i = 0; $i < $c_privileges; ++$i) {
			$priv_val = $role_privileges[$privileges[$i]['key']]['value'];
			$select[0]['value'] = 0;
			$select[0]['checked'] = !isset($form) && $priv_val == 0 || isset($form) && $form['privileges'][$privileges[$i]['id']] == 0 ? ' checked="checked"' : '';
			$select[0]['lang'] = $lang->t('access', 'deny_access');
			$select[1]['value'] = 1;
			$select[1]['checked'] = !isset($form) && $priv_val == 1 || isset($form) && $form['privileges'][$privileges[$i]['id']] == 1 ? ' checked="checked"' : '';
			$select[1]['lang'] = $lang->t('access', 'allow_access');
			if ($uri->id != 1) {
				$select[2]['value'] = 2;
				$select[2]['checked'] = !isset($form) && $priv_val == 2 || isset($form) && $form['privileges'][$privileges[$i]['id']] == 2 ? ' checked="checked"' : '';
				$select[2]['lang'] = $lang->t('access', 'inherit_access');
				$privileges[$i]['calculated'] = sprintf($lang->t('access', 'calculated_permission'), $role_privileges[$privileges[$i]['key']]['access'] === true ? $lang->t('access', 'allow_access') :  $lang->t('access', 'deny_access'));
			}
			$privileges[$i]['select'] = $select;
			$privileges[$i]['name'] = empty($privileges[$i]['name']) ? $privileges[$i]['key'] : $privileges[$i]['name'];
		}
		$tpl->assign('privileges', $privileges);

		$tpl->assign('form', isset($form) ? $form : $role[0]);

		$content = modules::fetchTemplate('access/edit.html');
	}
} else {
	$uri->redirect('errors/404');
}
