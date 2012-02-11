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

$breadcrumb->assign($lang->t('access', 'adm_list_resources'), $uri->route('acp/access/adm_list_resources'))
		   ->assign($lang->t('access', 'edit_resource'));

require_once MODULES_DIR . 'access/functions.php';

if (validate::isNumber($uri->id) === true && $db->countRows('*', 'acl_resources', 'id = \'' . $uri->id . '\'') == '1') {
	if (isset($_POST['form']) === true) {
		$form = $_POST['form'];

		if (empty($form['privileges']) || validate::isNumber($form['privileges']) === false)
			$errors[] = $lang->t('access', 'select_privilege');
		if (validate::isNumber($form['privileges']) && $db->countRows('*', 'acl_resources', 'id = \'' . $form['privileges'] . '\'') == 0)
			$errors[] = $lang->t('access', 'privilege_does_not_exist');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (validate::formToken() === false) {
			view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'privilege_id' => $form['privileges'],
			);
			$bool = $db->update('acl_resources', $update_values, 'id = \'' . $uri->id . '\'');

			acl::setResourcesCache();

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/access/adm_list_resources');
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		$resource = $db->select('privilege_id', 'acl_resources', 'id =\'' . $uri->id . '\'');

		$privileges = acl::getAllPrivileges();
		$c_privileges = count($privileges);
		for ($i = 0; $i < $c_privileges; ++$i) {
			$privileges[$i]['selected'] = selectEntry('privileges', $privileges[$i]['id'], $resource[0]['privilege_id']);
		}
		$tpl->assign('privileges', $privileges);

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('access/edit_resource.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}