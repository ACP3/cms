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

$breadcrumb->append($lang->t('access', 'adm_list_resources'), $uri->route('acp/access/adm_list_resources'))
		   ->append($lang->t('access', 'edit_resource'));

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'acl_resources', 'id = \'' . $uri->id . '\'') == 1) {
	if (isset($_POST['submit']) === true) {
		if (empty($_POST['privileges']) || ACP3_Validate::isNumber($_POST['privileges']) === false)
			$errors['privileges'] = $lang->t('access', 'select_privilege');
		if (ACP3_Validate::isNumber($_POST['privileges']) && $db->countRows('*', 'acl_resources', 'id = \'' . $_POST['privileges'] . '\'') == 0)
			$errors['privileges'] = $lang->t('access', 'privilege_does_not_exist');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'privilege_id' => $_POST['privileges'],
			);
			$bool = $db->update('acl_resources', $update_values, 'id = \'' . $uri->id . '\'');

			ACP3_ACL::setResourcesCache();

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/access/adm_list_resources');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$resource = $db->select('privilege_id', 'acl_resources', 'id =\'' . $uri->id . '\'');

		$privileges = ACP3_ACL::getAllPrivileges();
		$c_privileges = count($privileges);
		for ($i = 0; $i < $c_privileges; ++$i) {
			$privileges[$i]['selected'] = selectEntry('privileges', $privileges[$i]['id'], $resource[0]['privilege_id']);
		}
		$tpl->assign('privileges', $privileges);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('access/edit_resource.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}