<?php
/**
 * Menu bars
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (!preg_match('/^[a-zA-Z]+\w/', $_POST['index_name']))
		$errors['index-name'] = ACP3\CMS::$injector['Lang']->t('menus', 'type_in_index_name');
	if (!isset($errors) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE index_name = ?', array($_POST['index_name'])) > 0)
		$errors['index-name'] = ACP3\CMS::$injector['Lang']->t('menus', 'index_name_unique');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = ACP3\CMS::$injector['Lang']->t('menus', 'menu_bar_title_to_short');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'index_name' => $_POST['index_name'],
			'title' => ACP3\Core\Functions::str_encode($_POST['title']),
		);

		$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'menus', $insert_values);

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('index_name' => '', 'title' => ''));

	ACP3\CMS::$injector['Session']->generateFormToken();
}
