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

if (validate::isNumber($uri->id) && $db->select('COUNT(id)', 'pages_blocks', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == '1') {
	breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
	breadcrumb::assign($lang->t('pages', 'pages'), uri('acp/pages'));
	breadcrumb::assign($lang->t('pages', 'adm_list_blocks'), uri('acp/pages/adm_list_blocks'));
	breadcrumb::assign($lang->t('pages', 'edit_block'));

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (!preg_match('/^[a-zA-Z]+\w/', $form['index_name']))
			$errors[] = $lang->t('pages', 'type_in_index_name');
		if (preg_match('/^[a-zA-Z]+\w/', $form['index_name']) && $db->select('COUNT(id)', 'pages_blocks', 'index_name = \'' . $db->escape($form['index_name']) . '\' AND id != \'' . $uri->id . '\'', 0, 0, 0, 1) > 0)
			$errors[] = $lang->t('pages', 'index_name_unique');
		if (strlen($form['title']) < 3)
			$errors[] = $lang->t('pages', 'block_title_to_short');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'index_name' => $db->escape($form['index_name']),
				'title' => $db->escape($form['title']),
			);

			$bool = $db->update('pages_blocks', $update_values, 'id = \'' . $uri->id . '\'');

			setNavbarCache();

			$content = comboBox($bool ? $lang->t('pages', 'edit_block_success') : $lang->t('pages', 'edit_block_error'), uri('acp/pages/adm_list_blocks'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$block = $db->select('index_name, title', 'pages_blocks', 'id = \'' . $uri->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $block[0]);

		$content = $tpl->fetch('pages/edit_block.html');
	}
} else {
	redirect('errors/404');
}
?>