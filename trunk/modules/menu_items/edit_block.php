<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (validate::isNumber($uri->id) && $db->countRows('*', 'menu_items_blocks', 'id = \'' . $uri->id . '\'') == '1') {
	require_once MODULES_DIR . 'menu_items/functions.php';

	breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
	breadcrumb::assign($lang->t('menu_items', 'menu_items'), $uri->route('acp/menu_items'));
	breadcrumb::assign($lang->t('menu_items', 'adm_list_blocks'), $uri->route('acp/menu_items/adm_list_blocks'));
	breadcrumb::assign($lang->t('menu_items', 'edit_block'));

	if (isset($_POST['form'])) {
		$form = $_POST['form'];

		if (!preg_match('/^[a-zA-Z]+\w/', $form['index_name']))
			$errors[] = $lang->t('menu_items', 'type_in_index_name');
		if (preg_match('/^[a-zA-Z]+\w/', $form['index_name']) && $db->countRows('*', 'menu_items_blocks', 'index_name = \'' . $db->escape($form['index_name']) . '\' AND id != \'' . $uri->id . '\'') > 0)
			$errors[] = $lang->t('menu_items', 'index_name_unique');
		if (strlen($form['title']) < 3)
			$errors[] = $lang->t('menu_items', 'block_title_to_short');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'index_name' => $db->escape($form['index_name']),
				'title' => $db->escape($form['title']),
			);

			$bool = $db->update('menu_items_blocks', $update_values, 'id = \'' . $uri->id . '\'');

			setMenuItemsCache();

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), $uri->route('acp/menu_items/adm_list_blocks'));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$block = $db->select('index_name, title', 'menu_items_blocks', 'id = \'' . $uri->id . '\'');
		$block[0]['index_name'] = $db->escape($block[0]['index_name'], 3);
		$block[0]['title'] = $db->escape($block[0]['title'], 3);

		$tpl->assign('form', isset($form) ? $form : $block[0]);

		$content = modules::fetchTemplate('menu_items/edit_block.html');
	}
} else {
	$uri->redirect('errors/404');
}
