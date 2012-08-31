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

require_once MODULES_DIR . 'menus/functions.php';

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'menus', 'id = \'' . $uri->id . '\'') == 1) {
	if (isset($_POST['submit']) === true) {
		if (!preg_match('/^[a-zA-Z]+\w/', $_POST['index_name']))
			$errors['index-name'] = $lang->t('menus', 'type_in_index_name');
		if (!isset($errors) && $db->countRows('*', 'menus', 'index_name = \'' . $db->escape($_POST['index_name']) . '\' AND id != \'' . $uri->id . '\'') > 0)
			$errors['index-name'] = $lang->t('menus', 'index_name_unique');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = $lang->t('menus', 'block_title_to_short');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'index_name' => $db->escape($_POST['index_name']),
				'title' => $db->escape($_POST['title']),
			);

			$bool = $db->update('menus', $update_values, 'id = \'' . $uri->id . '\'');

			setMenuItemsCache();

			$session->unsetFormToken();

			setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$block = $db->select('index_name, title', 'menus', 'id = \'' . $uri->id . '\'');
		$block[0]['index_name'] = $db->escape($block[0]['index_name'], 3);
		$block[0]['title'] = $db->escape($block[0]['title'], 3);

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $block[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('menus/acp_edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}