<?php
/**
 * Pages
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$breadcrumb->append($lang->t('menu_items', 'adm_list_blocks'), $uri->route('acp/menu_items/adm_list_blocks'))
		   ->append($lang->t('menu_items', 'create_block'));

if (isset($_POST['submit']) === true) {
	if (!preg_match('/^[a-zA-Z]+\w/', $_POST['index_name']))
		$errors['index-name'] = $lang->t('menu_items', 'type_in_index_name');
	if (!isset($errors) && $db->countRows('*', 'menu_items_blocks', 'index_name = \'' . $db->escape($_POST['index_name']) . '\'') > 0)
		$errors['index-name'] = $lang->t('menu_items', 'index_name_unique');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = $lang->t('menu_items', 'block_title_to_short');

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

		$bool = $db->insert('menu_items_blocks', $insert_values);

		$session->unsetFormToken();

		setRedirectMessage($bool !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), 'acp/menu_items/adm_list_blocks');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('index_name' => '', 'title' => ''));

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('menu_items/create_block.tpl'));
}
