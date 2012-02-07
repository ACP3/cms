<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
breadcrumb::assign($lang->t('menu_items', 'menu_items'), $uri->route('acp/menu_items'));
breadcrumb::assign($lang->t('menu_items', 'adm_list_blocks'), $uri->route('acp/menu_items/adm_list_blocks'));
breadcrumb::assign($lang->t('menu_items', 'create_block'));

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (!preg_match('/^[a-zA-Z]+\w/', $form['index_name']))
		$errors[] = $lang->t('menu_items', 'type_in_index_name');
	if (preg_match('/^[a-zA-Z]+\w/', $form['index_name']) && $db->countRows('*', 'menu_items_blocks', 'index_name = \'' . $db->escape($form['index_name']) . '\'') > 0)
		$errors[] = $lang->t('menu_items', 'index_name_unique');
	if (strlen($form['title']) < 3)
		$errors[] = $lang->t('menu_items', 'block_title_to_short');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'index_name' => $db->escape($form['index_name']),
			'title' => $db->escape($form['title']),
		);

		$bool = $db->insert('menu_items_blocks', $insert_values);

		$session->unsetFormToken();

		setRedirectMessage($bool !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), 'acp/menu_items/adm_list_blocks');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($form) ? $form : array('index_name' => '', 'title' => ''));

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('menu_items/create_block.tpl'));
}
