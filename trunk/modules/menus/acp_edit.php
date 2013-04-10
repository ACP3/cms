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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	if (isset($_POST['submit']) === true) {
		if (!preg_match('/^[a-zA-Z]+\w/', $_POST['index_name']))
			$errors['index-name'] = ACP3_CMS::$lang->t('menus', 'type_in_index_name');
		if (!isset($errors) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE index_name = ? AND id != ?', array($_POST['index_name'], ACP3_CMS::$uri->id)) > 0)
			$errors['index-name'] = ACP3_CMS::$lang->t('menus', 'index_name_unique');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = ACP3_CMS::$lang->t('menus', 'menu_bar_title_to_short');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'index_name' => $_POST['index_name'],
				'title' => str_encode($_POST['title']),
			);

			$bool = ACP3_CMS::$db2->update(DB_PRE . 'menus', $update_values, array('id' => ACP3_CMS::$uri->id));

			setMenuItemsCache();

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$block = ACP3_CMS::$db2->fetchAssoc('SELECT index_name, title FROM ' . DB_PRE . 'menus WHERE id = ?', array(ACP3_CMS::$uri->id));

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $block);

		ACP3_CMS::$session->generateFormToken();
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}