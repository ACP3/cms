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
		$errors['index-name'] = ACP3_CMS::$lang->t('menus', 'type_in_index_name');
	if (!isset($errors) && ACP3_CMS::$db->countRows('*', 'menus', 'index_name = \'' . ACP3_CMS::$db->escape($_POST['index_name']) . '\'') > 0)
		$errors['index-name'] = ACP3_CMS::$lang->t('menus', 'index_name_unique');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = ACP3_CMS::$lang->t('menus', 'menu_bar_title_to_short');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'index_name' => ACP3_CMS::$db->escape($_POST['index_name']),
			'title' => ACP3_CMS::$db->escape($_POST['title']),
		);

		$bool = ACP3_CMS::$db->insert('menus', $insert_values);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('index_name' => '', 'title' => ''));

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('menus/acp_create.tpl'));
}
