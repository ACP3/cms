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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	if (isset($_POST['submit']) === true) {
		if (!preg_match('/^[a-zA-Z]+\w/', $_POST['index_name']))
			$errors['index-name'] = ACP3\CMS::$injector['Lang']->t('menus', 'type_in_index_name');
		if (!isset($errors) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menus WHERE index_name = ? AND id != ?', array($_POST['index_name'], ACP3\CMS::$injector['URI']->id)) > 0)
			$errors['index-name'] = ACP3\CMS::$injector['Lang']->t('menus', 'index_name_unique');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = ACP3\CMS::$injector['Lang']->t('menus', 'menu_bar_title_to_short');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'index_name' => $_POST['index_name'],
				'title' => ACP3\Core\Functions::str_encode($_POST['title']),
			);

			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'menus', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));

			setMenuItemsCache();

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$block = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT index_name, title FROM ' . DB_PRE . 'menus WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $block);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}