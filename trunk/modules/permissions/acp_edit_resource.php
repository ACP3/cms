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

ACP3_CMS::$breadcrumb
->append(ACP3_CMS::$lang->t('permissions', 'acp_list_resources'), ACP3_CMS::$uri->route('acp/permissions/list_resources'))
->append(ACP3_CMS::$lang->t('permissions', 'acp_edit_resource'));

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_resources WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	if (isset($_POST['submit']) === true) {
		if (empty($_POST['modules']) || ACP3_Modules::isInstalled($_POST['modules']) === false)
			$errors['modules'] = ACP3_CMS::$lang->t('permissions', 'select_module');
		if (empty($_POST['resource']) || preg_match('=/=', $_POST['resource']) || ACP3_Validate::isInternalURI($_POST['modules'] . '/' . $_POST['resource'] . '/') === false)
			$errors['resource'] = ACP3_CMS::$lang->t('permissions', 'type_in_resource');
		if (empty($_POST['privileges']) || ACP3_Validate::isNumber($_POST['privileges']) === false)
			$errors['privileges'] = ACP3_CMS::$lang->t('permissions', 'select_privilege');
		if (ACP3_Validate::isNumber($_POST['privileges']) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_resources WHERE id = ?', array($_POST['privileges'])) == 0)
			$errors['privileges'] = ACP3_CMS::$lang->t('permissions', 'privilege_does_not_exist');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'page' => $_POST['resource'],
				'privilege_id' => $_POST['privileges'],
			);
			$bool = ACP3_CMS::$db2->update(DB_PRE . 'acl_resources', $update_values, array('id' => ACP3_CMS::$uri->id));

			ACP3_ACL::setResourcesCache();

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions/list_resources');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$resource = ACP3_CMS::$db2->fetchAssoc('SELECT r.page, r.privilege_id, m.name AS module_name FROM ' . DB_PRE . 'acl_resources AS r JOIN ' . DB_PRE . 'modules AS m ON(m.id = r.module_id) WHERE r.id = ?', array(ACP3_CMS::$uri->id));

		$privileges = ACP3_ACL::getAllPrivileges();
		$c_privileges = count($privileges);
		for ($i = 0; $i < $c_privileges; ++$i) {
			$privileges[$i]['selected'] = selectEntry('privileges', $privileges[$i]['id'], $resource['privilege_id']);
		}
		ACP3_CMS::$view->assign('privileges', $privileges);

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('resource' => $resource['page'], 'modules' => $resource['module_name']));

		ACP3_CMS::$session->generateFormToken();
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}