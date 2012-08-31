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
		$errors['index-name'] = $lang->t('menus', 'type_in_index_name');
	if (!isset($errors) && $db->countRows('*', 'menus', 'index_name = \'' . $db->escape($_POST['index_name']) . '\'') > 0)
		$errors['index-name'] = $lang->t('menus', 'index_name_unique');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = $lang->t('menus', 'block_title_to_short');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'index_name' => $db->escape($_POST['index_name']),
			'title' => $db->escape($_POST['title']),
		);

		$bool = $db->insert('menus', $insert_values);

		$session->unsetFormToken();

		setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('index_name' => '', 'title' => ''));

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('menus/acp_create.tpl'));
}
