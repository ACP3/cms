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

breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
breadcrumb::assign($lang->t('access', 'access'), $uri->route('acp/access'));
breadcrumb::assign($lang->t('access', 'adm_list_resources'), $uri->route('acp/access/adm_list_resources'));
breadcrumb::assign($lang->t('access', 'edit_resource'));

require_once MODULES_DIR . 'access/functions.php';

if (validate::isNumber($uri->id) && $db->countRows('*', 'acl_resources', 'id = \'' . $uri->id . '\'') == '1') {
	if (isset($_POST['form'])) {
		$form = $_POST['form'];

		if (empty($form['privileges']) || !validate::isNumber($form['privileges']))
			$errors[] = $lang->t('access', 'select_privilege');
		if (validate::isNumber($form['privileges']) && $db->countRows('*', 'acl_resources', 'id = \'' . $form['privileges'] . '\'') == 0)
			$errors[] = $lang->t('access', 'privilege_does_not_exist');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'privilege_id' => $form['privileges'],
			);
			$bool = $db->update('acl_resources', $update_values, 'id = \'' . $uri->id . '\'');

			acl::setResourcesCache();

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), $uri->route('acp/access/adm_list_resources'));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$resource = $db->select('privilege_id', 'acl_resources', 'id =\'' . $uri->id . '\'');

		$privileges = acl::getAllPrivileges();
		$c_privileges = count($privileges);
		for ($i = 0; $i < $c_privileges; ++$i) {
			$privileges[$i]['selected'] = selectEntry('privileges', $privileges[$i]['id'], $resource[0]['privilege_id']);
		}
		$tpl->assign('privileges', $privileges);

		$content = modules::fetchTemplate('access/edit_resource.html');
	}
} else {
	$uri->redirect('errors/404');
}