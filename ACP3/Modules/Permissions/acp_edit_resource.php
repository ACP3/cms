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

ACP3\CMS::$injector['Breadcrumb']
->append(ACP3\CMS::$injector['Lang']->t('permissions', 'acp_list_resources'), ACP3\CMS::$injector['URI']->route('acp/permissions/list_resources'))
->append(ACP3\CMS::$injector['Lang']->t('permissions', 'acp_edit_resource'));

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_resources WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	if (isset($_POST['submit']) === true) {
		if (empty($_POST['modules']) || ACP3\Core\Modules::isInstalled($_POST['modules']) === false)
			$errors['modules'] = ACP3\CMS::$injector['Lang']->t('permissions', 'select_module');
		if (empty($_POST['resource']) || preg_match('=/=', $_POST['resource']) || ACP3\Core\Validate::isInternalURI($_POST['modules'] . '/' . $_POST['resource'] . '/') === false)
			$errors['resource'] = ACP3\CMS::$injector['Lang']->t('permissions', 'type_in_resource');
		if (empty($_POST['privileges']) || ACP3\Core\Validate::isNumber($_POST['privileges']) === false)
			$errors['privileges'] = ACP3\CMS::$injector['Lang']->t('permissions', 'select_privilege');
		if (ACP3\Core\Validate::isNumber($_POST['privileges']) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'acl_resources WHERE id = ?', array($_POST['privileges'])) == 0)
			$errors['privileges'] = ACP3\CMS::$injector['Lang']->t('permissions', 'privilege_does_not_exist');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'page' => $_POST['resource'],
				'privilege_id' => $_POST['privileges'],
			);
			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'acl_resources', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));

			ACP3\Core\ACL::setResourcesCache();

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/permissions/list_resources');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$resource = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT r.page, r.privilege_id, m.name AS module_name FROM ' . DB_PRE . 'acl_resources AS r JOIN ' . DB_PRE . 'modules AS m ON(m.id = r.module_id) WHERE r.id = ?', array(ACP3\CMS::$injector['URI']->id));

		$privileges = ACP3\Core\ACL::getAllPrivileges();
		$c_privileges = count($privileges);
		for ($i = 0; $i < $c_privileges; ++$i) {
			$privileges[$i]['selected'] = ACP3\Core\Functions::selectEntry('privileges', $privileges[$i]['id'], $resource['privilege_id']);
		}
		ACP3\CMS::$injector['View']->assign('privileges', $privileges);

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('resource' => $resource['page'], 'modules' => $resource['module_name']));

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}